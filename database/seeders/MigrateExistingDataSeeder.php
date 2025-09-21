<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Member;
use App\Models\User;
use App\Models\MembershipCode;
use App\Models\ReferralBonusLog;
use App\Models\CashbackLog;
use App\Models\Wallet;
use App\Models\WalletTransaction;

class MigrateExistingDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->command->info('🚀 Starting migration of existing data...');
        
        // Step 1: Ensure all existing members have membership codes
        $this->assignMembershipCodes();
        
        // Step 2: Update member statuses to new system
        $this->updateMemberStatuses();
        
        // Step 3: Preserve existing cashback and bonus history
        $this->preserveExistingHistory();
        
        // Step 4: Validate data integrity
        $this->validateDataIntegrity();
        
        $this->command->info('✅ Data migration completed successfully!');
    }

    /**
     * Assign membership codes to existing members who don't have them
     */
    private function assignMembershipCodes()
    {
        $this->command->info('📋 Assigning membership codes to existing members...');
        
        // Get members without assigned membership codes
        $membersWithoutCodes = DB::table('members')
            ->leftJoin('users', 'members.id', '=', 'users.member_id')
            ->leftJoin('membership_codes', 'users.id', '=', 'membership_codes.used_by')
            ->whereNull('membership_codes.id')
            ->whereNotNull('users.id')
            ->select('members.*', 'users.id as user_id', 'users.name as user_name')
            ->get();

        if ($membersWithoutCodes->count() > 0) {
            $this->command->info("Found {$membersWithoutCodes->count()} members without membership codes.");
            
            // Generate additional codes if needed
            $availableCodes = MembershipCode::where('used', false)->count();
            $neededCodes = $membersWithoutCodes->count() - $availableCodes;
            
            if ($neededCodes > 0) {
                $this->command->info("Generating {$neededCodes} additional membership codes...");
                for ($i = 0; $i < $neededCodes; $i++) {
                    do {
                        $code = strtoupper(\Str::random(8));
                    } while (MembershipCode::where('code', $code)->exists());

                    MembershipCode::create([
                        'code' => $code,
                        'used' => false,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }
            
            // Assign codes to members
            foreach ($membersWithoutCodes as $member) {
                $availableCode = MembershipCode::where('used', false)->first();
                
                if ($availableCode && $member->user_id) {
                    $availableCode->update([
                        'used' => true,
                        'used_by' => $member->user_id,
                        'used_at' => now(),
                        'updated_at' => now(),
                    ]);
                    
                    $this->command->info("✓ Assigned code {$availableCode->code} to {$member->first_name} {$member->last_name}");
                }
            }
        } else {
            $this->command->info('✓ All existing members already have membership codes assigned.');
        }
    }

    /**
     * Update member statuses to match new system requirements
     */
    private function updateMemberStatuses()
    {
        $this->command->info('🔄 Updating member statuses to new system...');
        
        // Update members with 'Active' status to 'Approved'
        $updatedMembers = DB::table('members')
            ->where('status', 'Active')
            ->update(['status' => 'Approved', 'updated_at' => now()]);
            
        if ($updatedMembers > 0) {
            $this->command->info("✓ Updated {$updatedMembers} members from 'Active' to 'Approved' status.");
        }

        // Update corresponding users
        $updatedUsers = DB::table('users')
            ->where('status', 'Active')
            ->update(['status' => 'Approved', 'updated_at' => now()]);
            
        if ($updatedUsers > 0) {
            $this->command->info("✓ Updated {$updatedUsers} users from 'Active' to 'Approved' status.");
        }

        // Handle any other status variations
        DB::table('members')
            ->whereIn('status', ['active', 'ACTIVE'])
            ->update(['status' => 'Approved', 'updated_at' => now()]);
            
        DB::table('users')
            ->whereIn('status', ['active', 'ACTIVE'])
            ->update(['status' => 'Approved', 'updated_at' => now()]);
    }

    /**
     * Preserve existing cashback and bonus history
     */
    private function preserveExistingHistory()
    {
        $this->command->info('💰 Preserving existing cashback and bonus history...');
        
        // Check existing referral bonus logs
        $bonusLogs = ReferralBonusLog::count();
        $this->command->info("✓ Found {$bonusLogs} existing referral bonus log entries - preserved.");
        
        // Check existing cashback logs
        $cashbackLogs = CashbackLog::count();
        $this->command->info("✓ Found {$cashbackLogs} existing cashback log entries - preserved.");
        
        // Check existing wallet transactions
        $walletTransactions = WalletTransaction::count();
        $this->command->info("✓ Found {$walletTransactions} existing wallet transaction entries - preserved.");
        
        // Verify wallet balances are maintained
        $walletsWithBalance = Wallet::where('balance', '>', 0)->count();
        $this->command->info("✓ Found {$walletsWithBalance} wallets with positive balances - preserved.");
    }

    /**
     * Validate data integrity after migration
     */
    private function validateDataIntegrity()
    {
        $this->command->info('🔍 Validating data integrity...');
        
        // Check that all approved members have membership codes
        $approvedMembersWithoutCodes = DB::table('members')
            ->leftJoin('users', 'members.id', '=', 'users.member_id')
            ->leftJoin('membership_codes', 'users.id', '=', 'membership_codes.used_by')
            ->where('members.status', 'Approved')
            ->whereNull('membership_codes.id')
            ->count();
            
        if ($approvedMembersWithoutCodes > 0) {
            $this->command->error("❌ Found {$approvedMembersWithoutCodes} approved members without membership codes!");
        } else {
            $this->command->info('✓ All approved members have membership codes assigned.');
        }
        
        // Check that all used membership codes have corresponding members
        $orphanedCodes = DB::table('membership_codes')
            ->leftJoin('users', 'membership_codes.used_by', '=', 'users.id')
            ->where('membership_codes.used', true)
            ->whereNull('users.id')
            ->count();
            
        if ($orphanedCodes > 0) {
            $this->command->error("❌ Found {$orphanedCodes} used membership codes without corresponding users!");
        } else {
            $this->command->info('✓ All used membership codes have corresponding users.');
        }
        
        // Summary statistics
        $totalMembers = Member::count();
        $approvedMembers = Member::where('status', 'Approved')->count();
        $pendingMembers = Member::where('status', 'Pending')->count();
        $usedCodes = MembershipCode::where('used', true)->count();
        $unusedCodes = MembershipCode::where('used', false)->count();
        
        $this->command->info('📊 Migration Summary:');
        $this->command->info("   Total Members: {$totalMembers}");
        $this->command->info("   Approved Members: {$approvedMembers}");
        $this->command->info("   Pending Members: {$pendingMembers}");
        $this->command->info("   Used Membership Codes: {$usedCodes}");
        $this->command->info("   Available Membership Codes: {$unusedCodes}");
    }
}