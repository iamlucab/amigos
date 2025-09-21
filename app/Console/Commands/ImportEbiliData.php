<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use App\Models\Member;
use App\Models\User;
use App\Models\MembershipCode;

class ImportEbiliData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ebili:import-data {sql_file=ebili1.sql}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import data from ebili1.sql file and assign membership codes';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $sqlFile = $this->argument('sql_file');
        
        if (!File::exists($sqlFile)) {
            $this->error("SQL file '{$sqlFile}' not found!");
            $this->info("Please place the ebili1.sql file in the root directory of your project.");
            return 1;
        }

        $this->info('🚀 Starting import of ebili1.sql data...');
        
        try {
            // Import the SQL file
            $this->importSqlFile($sqlFile);
            
            // Assign membership codes to members without codes
            $this->assignMembershipCodes();
            
            // Update statuses
            $this->updateStatuses();
            
            // Show summary
            $this->showSummary();
            
            $this->info('✅ Data import completed successfully!');
            
        } catch (\Exception $e) {
            $this->error('❌ Import failed: ' . $e->getMessage());
            return 1;
        }

        return 0;
    }

    private function importSqlFile($sqlFile)
    {
        $this->info('📥 Importing SQL file...');
        
        // Read the SQL file
        $sql = File::get($sqlFile);
        
        // Remove comments and split into statements
        $sql = preg_replace('/--.*$/m', '', $sql);
        $sql = preg_replace('/\/\*.*?\*\//s', '', $sql);
        
        // Split by semicolon but be careful with data that might contain semicolons
        $statements = array_filter(array_map('trim', explode(';', $sql)));
        
        // Filter out CREATE TABLE, ALTER TABLE, and other DDL statements
        // Only keep INSERT statements and some specific statements
        $dataStatements = [];
        foreach ($statements as $statement) {
            $statement = trim($statement);
            if (empty($statement)) continue;
            
            // Skip DDL statements (CREATE, ALTER, DROP, etc.)
            if (preg_match('/^\s*(CREATE|ALTER|DROP|SET|START|COMMIT|LOCK|UNLOCK)/i', $statement)) {
                continue;
            }
            
            // Keep INSERT statements and other data manipulation
            if (preg_match('/^\s*(INSERT|UPDATE|DELETE)/i', $statement)) {
                $dataStatements[] = $statement;
            }
        }
        
        $this->info('Executing ' . count($dataStatements) . ' data statements...');
        
        if (empty($dataStatements)) {
            $this->warn('No INSERT statements found in SQL file');
            return;
        }
        
        DB::beginTransaction();
        
        try {
            // Disable foreign key checks temporarily
            DB::statement('SET FOREIGN_KEY_CHECKS=0');
            
            foreach ($dataStatements as $statement) {
                try {
                    DB::unprepared($statement);
                } catch (\Exception $e) {
                    // Log the error but continue with other statements
                    $this->warn('Skipped statement due to error: ' . substr($statement, 0, 100) . '...');
                    $this->warn('Error: ' . $e->getMessage());
                }
            }
            
            // Re-enable foreign key checks
            DB::statement('SET FOREIGN_KEY_CHECKS=1');
            
            DB::commit();
            $this->info('✓ Data imported successfully');
            
        } catch (\Exception $e) {
            DB::statement('SET FOREIGN_KEY_CHECKS=1');
            DB::rollback();
            throw $e;
        }
    }

    private function assignMembershipCodes()
    {
        $this->info('🎫 Assigning membership codes to members without codes...');
        
        // Find members without membership codes
        $membersWithoutCodes = DB::select("
            SELECT m.id, m.first_name, m.last_name, u.id as user_id
            FROM members m
            LEFT JOIN users u ON m.id = u.member_id
            LEFT JOIN membership_codes mc ON u.id = mc.used_by
            WHERE mc.id IS NULL AND u.id IS NOT NULL AND m.status = 'Approved'
        ");

        if (empty($membersWithoutCodes)) {
            $this->info('✓ All members already have membership codes assigned');
            return;
        }

        $this->info('Found ' . count($membersWithoutCodes) . ' members without codes');

        // Check if we have enough unused codes
        $unusedCodes = MembershipCode::where('used', false)->count();
        $neededCodes = count($membersWithoutCodes) - $unusedCodes;

        if ($neededCodes > 0) {
            $this->info("Generating {$neededCodes} additional membership codes...");
            
            for ($i = 0; $i < $neededCodes; $i++) {
                do {
                    $code = strtoupper(\Str::random(8));
                } while (MembershipCode::where('code', $code)->exists());

                MembershipCode::create([
                    'code' => $code,
                    'used' => false,
                ]);
            }
        }

        // Assign codes to members
        foreach ($membersWithoutCodes as $member) {
            $availableCode = MembershipCode::where('used', false)->first();
            
            if ($availableCode) {
                $availableCode->update([
                    'used' => true,
                    'used_by' => $member->user_id,
                    'used_at' => now(),
                ]);
                
                $this->info("✓ Assigned code {$availableCode->code} to {$member->first_name} {$member->last_name}");
            }
        }
    }

    private function updateStatuses()
    {
        $this->info('🔄 Updating member statuses...');
        
        // Update 'Active' to 'Approved'
        $updatedMembers = DB::table('members')
            ->where('status', 'Active')
            ->update(['status' => 'Approved']);
            
        if ($updatedMembers > 0) {
            $this->info("✓ Updated {$updatedMembers} members from 'Active' to 'Approved'");
        }

        $updatedUsers = DB::table('users')
            ->where('status', 'Active')
            ->update(['status' => 'Approved']);
            
        if ($updatedUsers > 0) {
            $this->info("✓ Updated {$updatedUsers} users from 'Active' to 'Approved'");
        }
    }

    private function showSummary()
    {
        $this->info('📊 Import Summary:');
        
        $totalMembers = Member::count();
        $approvedMembers = Member::where('status', 'Approved')->count();
        $pendingMembers = Member::where('status', 'Pending')->count();
        $usedCodes = MembershipCode::where('used', true)->count();
        $unusedCodes = MembershipCode::where('used', false)->count();
        
        $totalWalletBalance = DB::table('wallets')->sum('balance');
        $referralBonuses = DB::table('referral_bonus_logs')->sum('amount');
        $cashbackLogs = DB::table('cashback_logs')->count();
        $walletTransactions = DB::table('wallet_transactions')->count();

        $this->table(['Metric', 'Count/Amount'], [
            ['Total Members', $totalMembers],
            ['Approved Members', $approvedMembers],
            ['Pending Members', $pendingMembers],
            ['Used Membership Codes', $usedCodes],
            ['Available Membership Codes', $unusedCodes],
            ['Total Wallet Balance', '₱' . number_format($totalWalletBalance, 2)],
            ['Total Referral Bonuses', '₱' . number_format($referralBonuses, 2)],
            ['Cashback Log Entries', $cashbackLogs],
            ['Wallet Transactions', $walletTransactions],
        ]);

        // Verify data integrity
        $membersWithoutCodes = DB::select("
            SELECT COUNT(*) as count
            FROM members m
            LEFT JOIN users u ON m.id = u.member_id
            LEFT JOIN membership_codes mc ON u.id = mc.used_by
            WHERE mc.id IS NULL AND u.id IS NOT NULL AND m.status = 'Approved'
        ")[0]->count;

        if ($membersWithoutCodes > 0) {
            $this->warn("⚠️  {$membersWithoutCodes} approved members still don't have membership codes!");
        } else {
            $this->info('✅ All approved members have membership codes assigned');
        }
    }
}