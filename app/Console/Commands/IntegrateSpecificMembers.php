<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\Member;
use App\Models\User;
use App\Models\Wallet;
use App\Models\WalletTransaction;
use App\Models\CashInRequest;
use App\Models\ReferralBonusLog;
use Exception;

class IntegrateSpecificMembers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'integrate:specific-members {--verify : Only run verification without integration}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Integrate specific members from amigos-latest.sql with their users, wallets, and transactions';

    private $specificMembers = [
        10026 => [
            'first_name' => 'Bernie',
            'middle_name' => 'Paraguya', 
            'last_name' => 'Baldesco',
            'birthday' => '1980-04-04',
            'mobile_number' => '09465935416',
            'occupation' => 'Businessman',
            'address' => null,
            'photo' => null,
            'role' => 'Member',
            'sponsor_id' => 16,
            'voter_id' => null,
            'loan_eligible' => 0,
            'status' => 'Approved',
            'created_at' => '2025-07-22 07:06:54',
            'updated_at' => '2025-07-22 07:06:54'
        ],
        10027 => [
            'first_name' => 'Cindy',
            'middle_name' => 'Polison',
            'last_name' => 'Bandao', 
            'birthday' => '1998-02-23',
            'mobile_number' => '09914528619',
            'occupation' => 'Saleswoman',
            'address' => null,
            'photo' => null,
            'role' => 'Member',
            'sponsor_id' => 10026,
            'voter_id' => null,
            'loan_eligible' => 0,
            'status' => 'Approved',
            'created_at' => '2025-07-22 07:08:08',
            'updated_at' => '2025-07-22 07:08:08'
        ],
        10028 => [
            'first_name' => 'Nor',
            'middle_name' => 'U',
            'last_name' => 'Umpar',
            'birthday' => '1982-04-04', 
            'mobile_number' => '09099200018',
            'occupation' => 'Lawyer',
            'address' => null,
            'photo' => null,
            'role' => 'Member',
            'sponsor_id' => 16,
            'voter_id' => null,
            'loan_eligible' => 0,
            'status' => 'Approved',
            'created_at' => '2025-07-22 07:09:47',
            'updated_at' => '2025-07-22 07:09:47'
        ],
        10029 => [
            'first_name' => 'Ariel',
            'middle_name' => 'Besmar',
            'last_name' => 'Capili',
            'birthday' => '1967-10-19',
            'mobile_number' => '09171852313',
            'occupation' => null,
            'address' => null,
            'photo' => null,
            'role' => 'Member',
            'sponsor_id' => 10028,
            'voter_id' => null,
            'loan_eligible' => 0,
            'status' => 'Approved',
            'created_at' => '2025-07-22 07:10:44',
            'updated_at' => '2025-07-22 07:10:44'
        ],
        10030 => [
            'first_name' => 'Mary Ann',
            'middle_name' => 'Pagas',
            'last_name' => 'Olbez',
            'birthday' => '1982-10-25',
            'mobile_number' => '09264663844',
            'occupation' => null,
            'address' => null,
            'photo' => null,
            'role' => 'Member',
            'sponsor_id' => 16,
            'voter_id' => null,
            'loan_eligible' => 0,
            'status' => 'Approved',
            'created_at' => '2025-07-22 07:11:45',
            'updated_at' => '2025-07-22 07:11:45'
        ],
        10031 => [
            'first_name' => 'Renz',
            'middle_name' => 'Lim',
            'last_name' => 'Licarte',
            'birthday' => '1988-05-11',
            'mobile_number' => '09763632594',
            'occupation' => 'Engineer',
            'address' => null,
            'photo' => null,
            'role' => 'Member',
            'sponsor_id' => 16,
            'voter_id' => null,
            'loan_eligible' => 0,
            'status' => 'Approved',
            'created_at' => '2025-07-22 07:12:43',
            'updated_at' => '2025-07-22 07:12:43'
        ],
        10032 => [
            'first_name' => 'Margie',
            'middle_name' => 'Navea',
            'last_name' => 'Palacio',
            'birthday' => '1993-07-12',
            'mobile_number' => '09670891993',
            'occupation' => 'Business owner',
            'address' => null,
            'photo' => null,
            'role' => 'Member',
            'sponsor_id' => 16,
            'voter_id' => null,
            'loan_eligible' => 0,
            'status' => 'Approved',
            'created_at' => '2025-07-22 07:13:36',
            'updated_at' => '2025-07-22 07:13:36'
        ],
        10033 => [
            'first_name' => 'Leah',
            'middle_name' => 'Maldepeña',
            'last_name' => 'Perez',
            'birthday' => '1989-01-21',
            'mobile_number' => '09198649321',
            'occupation' => 'Supervisor',
            'address' => null,
            'photo' => null,
            'role' => 'Member',
            'sponsor_id' => 10032,
            'voter_id' => null,
            'loan_eligible' => 0,
            'status' => 'Approved',
            'created_at' => '2025-07-22 07:16:31',
            'updated_at' => '2025-07-22 07:16:31'
        ],
        10034 => [
            'first_name' => 'MELANIE',
            'middle_name' => 'MORAN',
            'last_name' => 'GUIDAY',
            'birthday' => '1988-12-01',
            'mobile_number' => '09165210706',
            'occupation' => 'Real Estate Salesperson',
            'address' => null,
            'photo' => null,
            'role' => 'Member',
            'sponsor_id' => 10028,
            'voter_id' => null,
            'loan_eligible' => 0,
            'status' => 'Approved',
            'created_at' => '2025-07-23 12:19:27',
            'updated_at' => '2025-07-23 12:19:27'
        ]
    ];

    private $specificUsers = [
        11045 => [
            'name' => 'Bernie Baldesco',
            'mobile_number' => '09465935416',
            'email' => '09465935416@coop.local',
            'role' => 'Member',
            'member_id' => 10026,
            'status' => 'Approved',
            'created_at' => '2025-07-22 07:06:55',
            'updated_at' => '2025-07-22 07:06:55'
        ],
        11046 => [
            'name' => 'Cindy Bandao',
            'mobile_number' => '09914528619',
            'email' => '09914528619@coop.local',
            'role' => 'Member',
            'member_id' => 10027,
            'status' => 'Approved',
            'created_at' => '2025-07-22 07:08:08',
            'updated_at' => '2025-07-22 07:08:08'
        ],
        11047 => [
            'name' => 'Nor Umpar',
            'mobile_number' => '09099200018',
            'email' => '09099200018@coop.local',
            'role' => 'Member',
            'member_id' => 10028,
            'status' => 'Approved',
            'created_at' => '2025-07-22 07:09:47',
            'updated_at' => '2025-07-22 07:09:47'
        ],
        11048 => [
            'name' => 'Ariel Capili',
            'mobile_number' => '09171852313',
            'email' => '09171852313@coop.local',
            'role' => 'Member',
            'member_id' => 10029,
            'status' => 'Approved',
            'created_at' => '2025-07-22 07:10:44',
            'updated_at' => '2025-07-22 07:10:44'
        ],
        11049 => [
            'name' => 'Mary Ann Olbez',
            'mobile_number' => '09264663844',
            'email' => '09264663844@coop.local',
            'role' => 'Member',
            'member_id' => 10030,
            'status' => 'Approved',
            'created_at' => '2025-07-22 07:11:45',
            'updated_at' => '2025-07-22 07:11:45'
        ],
        11050 => [
            'name' => 'Renz Licarte',
            'mobile_number' => '09763632594',
            'email' => '09763632594@coop.local',
            'role' => 'Member',
            'member_id' => 10031,
            'status' => 'Approved',
            'created_at' => '2025-07-22 07:12:43',
            'updated_at' => '2025-07-22 07:12:43'
        ],
        11051 => [
            'name' => 'Margie Palacio',
            'mobile_number' => '09670891993',
            'email' => '09670891993@coop.local',
            'role' => 'Member',
            'member_id' => 10032,
            'status' => 'Approved',
            'created_at' => '2025-07-22 07:13:36',
            'updated_at' => '2025-07-22 07:13:36'
        ],
        11052 => [
            'name' => 'Leah Perez',
            'mobile_number' => '09198649321',
            'email' => '09198649321@coop.local',
            'role' => 'Member',
            'member_id' => 10033,
            'status' => 'Approved',
            'created_at' => '2025-07-22 07:16:31',
            'updated_at' => '2025-07-22 07:16:31'
        ],
        11053 => [
            'name' => 'Melanie Guiday',
            'mobile_number' => '09165210706',
            'email' => '09165210706@coop.local',
            'role' => 'Member',
            'member_id' => 10034,
            'status' => 'Approved',
            'created_at' => '2025-07-23 12:19:27',
            'updated_at' => '2025-07-23 12:19:27'
        ]
    ];

    private $specificWallets = [
        // Bernie Baldesco (10026)
        26 => ['type' => 'main', 'member_id' => 10026, 'wallet_id' => 'WALLET-687F388ED45A0', 'balance' => 0.00, 'created_at' => '2025-07-22 07:06:54', 'updated_at' => '2025-07-22 07:06:54'],
        27 => ['type' => 'cashback', 'member_id' => 10026, 'wallet_id' => 'WALLET-687F388ED45A2', 'balance' => 25.00, 'created_at' => '2025-07-22 07:06:54', 'updated_at' => '2025-07-22 07:08:08'],
        
        // Cindy Bandao (10027)
        28 => ['type' => 'main', 'member_id' => 10027, 'wallet_id' => 'WALLET-687F38D835794', 'balance' => 0.00, 'created_at' => '2025-07-22 07:08:08', 'updated_at' => '2025-07-22 07:08:08'],
        29 => ['type' => 'cashback', 'member_id' => 10027, 'wallet_id' => 'WALLET-687F38D835797', 'balance' => 0.00, 'created_at' => '2025-07-22 07:08:08', 'updated_at' => '2025-07-22 07:08:08'],
        
        // Nor Umpar (10028)
        30 => ['type' => 'main', 'member_id' => 10028, 'wallet_id' => 'WALLET-687F393B42C13', 'balance' => 0.00, 'created_at' => '2025-07-22 07:09:47', 'updated_at' => '2025-07-22 07:09:47'],
        31 => ['type' => 'cashback', 'member_id' => 10028, 'wallet_id' => 'WALLET-687F393B42C16', 'balance' => 50.00, 'created_at' => '2025-07-22 07:09:47', 'updated_at' => '2025-07-23 12:19:27'],
        
        // Ariel Capili (10029)
        32 => ['type' => 'main', 'member_id' => 10029, 'wallet_id' => 'WALLET-687F3974D5BA1', 'balance' => 0.00, 'created_at' => '2025-07-22 07:10:44', 'updated_at' => '2025-07-22 07:10:44'],
        33 => ['type' => 'cashback', 'member_id' => 10029, 'wallet_id' => 'WALLET-687F3974D5BA4', 'balance' => 0.00, 'created_at' => '2025-07-22 07:10:44', 'updated_at' => '2025-07-22 07:10:44'],
        
        // Mary Ann Olbez (10030)
        34 => ['type' => 'main', 'member_id' => 10030, 'wallet_id' => 'WALLET-687F39B119356', 'balance' => 0.00, 'created_at' => '2025-07-22 07:11:45', 'updated_at' => '2025-07-22 07:11:45'],
        35 => ['type' => 'cashback', 'member_id' => 10030, 'wallet_id' => 'WALLET-687F39B119359', 'balance' => 0.00, 'created_at' => '2025-07-22 07:11:45', 'updated_at' => '2025-07-22 07:11:45'],
        
        // Renz Licarte (10031)
        36 => ['type' => 'main', 'member_id' => 10031, 'wallet_id' => 'WALLET-687F39EB801AE', 'balance' => 0.00, 'created_at' => '2025-07-22 07:12:43', 'updated_at' => '2025-07-22 07:12:43'],
        37 => ['type' => 'cashback', 'member_id' => 10031, 'wallet_id' => 'WALLET-687F39EB801B0', 'balance' => 0.00, 'created_at' => '2025-07-22 07:12:43', 'updated_at' => '2025-07-22 07:12:43'],
        
        // Margie Palacio (10032)
        38 => ['type' => 'main', 'member_id' => 10032, 'wallet_id' => 'WALLET-687F3A208CD7A', 'balance' => 0.00, 'created_at' => '2025-07-22 07:13:36', 'updated_at' => '2025-07-22 07:13:36'],
        39 => ['type' => 'cashback', 'member_id' => 10032, 'wallet_id' => 'WALLET-687F3A208CD7D', 'balance' => 25.00, 'created_at' => '2025-07-22 07:13:36', 'updated_at' => '2025-07-22 07:16:31'],
        
        // Leah Perez (10033)
        40 => ['type' => 'main', 'member_id' => 10033, 'wallet_id' => 'WALLET-687F3ACFC7BFA', 'balance' => 0.00, 'created_at' => '2025-07-22 07:16:31', 'updated_at' => '2025-07-22 07:16:31'],
        41 => ['type' => 'cashback', 'member_id' => 10033, 'wallet_id' => 'WALLET-687F3ACFC7C01', 'balance' => 0.00, 'created_at' => '2025-07-22 07:16:31', 'updated_at' => '2025-07-22 07:16:31'],
        
        // Melanie Guiday (10034)
        42 => ['type' => 'main', 'member_id' => 10034, 'wallet_id' => 'WALLET-6880005F782C9', 'balance' => 0.00, 'created_at' => '2025-07-23 12:19:27', 'updated_at' => '2025-07-23 12:19:27'],
        43 => ['type' => 'cashback', 'member_id' => 10034, 'wallet_id' => 'WALLET-6880005F782CC', 'balance' => 0.00, 'created_at' => '2025-07-23 12:19:27', 'updated_at' => '2025-07-23 12:19:27']
    ];

    private $walletTransactions = [
        // Referral bonuses from the SQL file
        19 => ['wallet_id' => 27, 'member_id' => 10026, 'type' => 'credit', 'amount' => 25.00, 'source' => null, 'description' => 'Direct referral bonus from Cindy Bandao', 'notes' => null, 'related_member_id' => null, 'created_at' => '2025-07-22 07:08:08', 'updated_at' => '2025-07-22 07:08:08'],
        25 => ['wallet_id' => 31, 'member_id' => 10028, 'type' => 'credit', 'amount' => 25.00, 'source' => null, 'description' => 'Direct referral bonus from Ariel Capili', 'notes' => null, 'related_member_id' => null, 'created_at' => '2025-07-22 07:10:44', 'updated_at' => '2025-07-22 07:10:44'],
        44 => ['wallet_id' => 31, 'member_id' => 10028, 'type' => 'credit', 'amount' => 25.00, 'source' => null, 'description' => 'Direct referral bonus from MELANIE GUIDAY', 'notes' => null, 'related_member_id' => null, 'created_at' => '2025-07-23 12:19:27', 'updated_at' => '2025-07-23 12:19:27'],
        37 => ['wallet_id' => 39, 'member_id' => 10032, 'type' => 'credit', 'amount' => 25.00, 'source' => null, 'description' => 'Direct referral bonus from Leah Perez', 'notes' => null, 'related_member_id' => null, 'created_at' => '2025-07-22 07:16:31', 'updated_at' => '2025-07-22 07:16:31']
    ];

    private $cashInRequests = [
        2 => [
            'member_id' => 10034,
            'amount' => 100.00,
            'payment_method' => 'GCash',
            'note' => null,
            'proof_path' => 'proofs/NnYNGX7kLhJ9ieWp3Ofcw4KqWl0XJDcSfJrQrQbG.jpg',
            'status' => 'Pending',
            'created_at' => '2025-07-23 18:34:31',
            'updated_at' => '2025-07-23 18:34:31'
        ],
        3 => [
            'member_id' => 10034,
            'amount' => 100.00,
            'payment_method' => 'GCash',
            'note' => null,
            'proof_path' => 'proofs/4QJvF5Yz3KolhSqKEDuKEemZ7QgkGuyUHkkOumh5.jpg',
            'status' => 'Pending',
            'created_at' => '2025-07-23 18:34:36',
            'updated_at' => '2025-07-23 18:34:36'
        ]
    ];

    /**
     * Execute the console command.
     */
    public function handle()
    {
        if ($this->option('verify')) {
            $this->verify();
            return;
        }

        $this->info('Starting integration of specific members from amigos-latest.sql...');
        
        DB::beginTransaction();
        
        try {
            $this->integrateMembers();
            $this->integrateUsers();
            $this->integrateWallets();
            $this->integrateWalletTransactions();
            $this->integrateCashInRequests();
            
            DB::commit();
            $this->info('✅ Integration completed successfully!');
            
            // Run verification after successful integration
            $this->verify();
            
        } catch (Exception $e) {
            DB::rollback();
            $this->error('❌ Integration failed: ' . $e->getMessage());
            $this->error('Stack trace: ' . $e->getTraceAsString());
            return 1;
        }

        return 0;
    }

    private function integrateMembers()
    {
        $this->info('Integrating members...');
        
        foreach ($this->specificMembers as $memberId => $memberData) {
            // Check if member already exists
            $existingMember = Member::find($memberId);
            
            if ($existingMember) {
                $this->line("  - Member {$memberData['first_name']} {$memberData['last_name']} (ID: {$memberId}) already exists, updating...");
                $existingMember->update($memberData);
            } else {
                $this->line("  - Creating member {$memberData['first_name']} {$memberData['last_name']} (ID: {$memberId})...");
                
                // Temporarily disable model events to prevent automatic wallet creation
                Member::withoutEvents(function () use ($memberId, $memberData) {
                    $member = new Member($memberData);
                    $member->id = $memberId;
                    $member->save();
                });
            }
        }
        
        $this->info('  ✅ Members integration completed');
    }

    private function integrateUsers()
    {
        $this->info('Integrating users...');
        
        foreach ($this->specificUsers as $userId => $userData) {
            // Check if user already exists
            $existingUser = User::find($userId);
            
            if ($existingUser) {
                $this->line("  - User {$userData['name']} (ID: {$userId}) already exists, updating...");
                $existingUser->update($userData);
            } else {
                $this->line("  - Creating user {$userData['name']} (ID: {$userId})...");
                
                $user = new User($userData);
                $user->id = $userId;
                $user->password = Hash::make('password123'); // Default password
                $user->save();
            }
        }
        
        $this->info('  ✅ Users integration completed');
    }

    private function integrateWallets()
    {
        $this->info('Integrating wallets...');
        
        foreach ($this->specificWallets as $walletId => $walletData) {
            // Check if wallet already exists
            $existingWallet = Wallet::find($walletId);
            
            if ($existingWallet) {
                $this->line("  - Wallet ID {$walletId} already exists, updating balance...");
                $existingWallet->update([
                    'balance' => $walletData['balance'],
                    'updated_at' => $walletData['updated_at']
                ]);
            } else {
                $memberName = $this->getMemberName($walletData['member_id']);
                $this->line("  - Creating {$walletData['type']} wallet for {$memberName} (Wallet ID: {$walletId})...");
                
                $wallet = new Wallet($walletData);
                $wallet->id = $walletId;
                $wallet->save();
            }
        }
        
        $this->info('  ✅ Wallets integration completed');
    }

    private function integrateWalletTransactions()
    {
        $this->info('Integrating wallet transactions...');
        
        foreach ($this->walletTransactions as $transactionId => $transactionData) {
            // Check if transaction already exists
            $existingTransaction = WalletTransaction::find($transactionId);
            
            if ($existingTransaction) {
                $this->line("  - Transaction ID {$transactionId} already exists, skipping...");
                continue;
            }
            
            $this->line("  - Creating wallet transaction ID {$transactionId}: {$transactionData['description']}...");
            
            $transaction = new WalletTransaction($transactionData);
            $transaction->id = $transactionId;
            $transaction->save();
        }
        
        $this->info('  ✅ Wallet transactions integration completed');
    }

    private function integrateCashInRequests()
    {
        $this->info('Integrating cash-in requests...');
        
        foreach ($this->cashInRequests as $requestId => $requestData) {
            // Check if request already exists
            $existingRequest = CashInRequest::find($requestId);
            
            if ($existingRequest) {
                $this->line("  - Cash-in request ID {$requestId} already exists, skipping...");
                continue;
            }
            
            $memberName = $this->getMemberName($requestData['member_id']);
            $this->line("  - Creating cash-in request for {$memberName} (Amount: ₱{$requestData['amount']})...");
            
            $request = new CashInRequest($requestData);
            $request->id = $requestId;
            $request->save();
        }
        
        $this->info('  ✅ Cash-in requests integration completed');
    }

    private function getMemberName($memberId)
    {
        if (isset($this->specificMembers[$memberId])) {
            $member = $this->specificMembers[$memberId];
            return $member['first_name'] . ' ' . $member['last_name'];
        }
        return "Member ID {$memberId}";
    }

    public function verify()
    {
        $this->info("\n=== VERIFICATION REPORT ===");
        
        // Verify members
        $this->info("\n1. Members:");
        foreach ($this->specificMembers as $memberId => $memberData) {
            $member = Member::find($memberId);
            if ($member) {
                $this->line("  ✅ {$member->full_name} (ID: {$memberId}) - Status: {$member->status}");
            } else {
                $this->error("  ❌ Member ID {$memberId} not found");
            }
        }
        
        // Verify users
        $this->info("\n2. Users:");
        foreach ($this->specificUsers as $userId => $userData) {
            $user = User::find($userId);
            if ($user) {
                $this->line("  ✅ {$user->name} (ID: {$userId}) - Mobile: {$user->mobile_number}");
            } else {
                $this->error("  ❌ User ID {$userId} not found");
            }
        }
        
        // Verify wallets
        $this->info("\n3. Wallets:");
        foreach ($this->specificWallets as $walletId => $walletData) {
            $wallet = Wallet::find($walletId);
            if ($wallet) {
                $memberName = $this->getMemberName($wallet->member_id);
                $this->line("  ✅ {$memberName} - {$wallet->type} wallet (Balance: ₱{$wallet->balance})");
            } else {
                $this->error("  ❌ Wallet ID {$walletId} not found");
            }
        }
        
        // Verify transactions
        $this->info("\n4. Wallet Transactions:");
        foreach ($this->walletTransactions as $transactionId => $transactionData) {
            $transaction = WalletTransaction::find($transactionId);
            if ($transaction) {
                $this->line("  ✅ Transaction ID {$transactionId}: {$transaction->description} (₱{$transaction->amount})");
            } else {
                $this->error("  ❌ Transaction ID {$transactionId} not found");
            }
        }
        
        // Verify cash-in requests
        $this->info("\n5. Cash-in Requests:");
        foreach ($this->cashInRequests as $requestId => $requestData) {
            $request = CashInRequest::find($requestId);
            if ($request) {
                $memberName = $this->getMemberName($request->member_id);
                $this->line("  ✅ {$memberName} - ₱{$request->amount} ({$request->status})");
            } else {
                $this->error("  ❌ Cash-in request ID {$requestId} not found");
            }
        }
        
        $this->info("\n=== END VERIFICATION ===");
    }
}