<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use App\Models\Member;
use App\Models\User;
use Exception;

class CreateSponsorMember extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'create:sponsor-member';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create the required sponsor member (Ruthcil Cabandez - ID: 16) for integration';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Creating required sponsor member (Ruthcil Cabandez - ID: 16)...');

        try {
            // Check if member 16 already exists
            $existingMember = Member::find(16);
            
            if ($existingMember) {
                $this->info("✅ Member ID 16 already exists: {$existingMember->full_name}");
                return 0;
            }

            // Create Ruthcil as member ID 16
            $member = new Member([
                'first_name' => 'Ruthcil',
                'middle_name' => 'Alcazar',
                'last_name' => 'Cabandez',
                'birthday' => '1982-06-13',
                'mobile_number' => '09192222222',
                'occupation' => 'Accountant',
                'address' => 'Door C Alpha 11 Building, Rizal Extension Street, Davao City',
                'photo' => null,
                'role' => 'Member',
                'sponsor_id' => null, // No sponsor for now
                'voter_id' => null,
                'loan_eligible' => 0,
                'status' => 'Approved'
            ]);
            
            // Disable model events to prevent automatic wallet creation
            Member::withoutEvents(function () use ($member) {
                $member->id = 16;
                $member->created_at = '2025-07-15 14:32:02';
                $member->updated_at = '2025-07-15 14:33:33';
                $member->save();
            });
            
            $this->info("✅ Created member: {$member->full_name} (ID: 16)");
            
            // Check if user 10 already exists
            $existingUser = User::find(10);
            
            if (!$existingUser) {
                // Create corresponding user
                $user = new User([
                    'name' => 'Ruthcil Cabandez',
                    'mobile_number' => '09192222222',
                    'email' => '09192222222@coop.local',
                    'role' => 'Member',
                    'member_id' => 16,
                    'status' => 'Approved',
                    'password' => Hash::make('password123')
                ]);
                
                $user->id = 10;
                $user->created_at = '2025-07-15 14:32:02';
                $user->updated_at = '2025-07-15 14:33:33';
                $user->save();
                
                $this->info("✅ Created user: {$user->name} (ID: 10)");
            } else {
                $this->info("✅ User ID 10 already exists: {$existingUser->name}");
            }
            
            $this->info("\n🎉 Sponsor member setup completed! You can now run the integration.");
            $this->info("Run: php artisan integrate:specific-members");
            
            return 0;
            
        } catch (Exception $e) {
            $this->error("❌ Error: " . $e->getMessage());
            return 1;
        }
    }
}