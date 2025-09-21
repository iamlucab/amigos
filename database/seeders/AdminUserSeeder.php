<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Member;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $now = Carbon::now();
        
        // Create admin member
        $adminMember = Member::firstOrCreate(
            ['mobile_number' => '09171234567'],
            [
                'first_name'     => 'System',
                'last_name'      => 'Administrator',
                'birthday'       => '1990-01-01',
                'occupation'     => 'Administrator',
                'address'        => 'Admin Office',
                'role'           => 'admin',
                'status'         => 'Approved',
                'created_at'     => $now,
                'updated_at'     => $now,
            ]
        );
        
        // Create admin user
        $adminUser = User::firstOrCreate(
            ['mobile_number' => '09171234567'],
            [
                'name'              => $adminMember->first_name . ' ' . $adminMember->last_name,
                'email'             => 'admin@example.com',
                'role'              => 'Admin',
                'member_id'         => $adminMember->id,
                'email_verified_at' => $now,
                'password'          => Hash::make('admin123'),
                'status'            => 'Approved',
                'created_at'        => $now,
                'updated_at'        => $now,
            ]
        );
        
        // Create 2 staff users
        $staffUsers = [
            [
                'first_name' => 'Staff',
                'last_name' => 'One',
                'mobile_number' => '09171234568',
                'email' => 'staff1@example.com',
            ],
            [
                'first_name' => 'Staff',
                'last_name' => 'Two',
                'mobile_number' => '09171234569',
                'email' => 'staff2@example.com',
            ]
        ];
        
        foreach ($staffUsers as $staffData) {
            // Create staff member
            $staffMember = Member::firstOrCreate(
                ['mobile_number' => $staffData['mobile_number']],
                [
                    'first_name'     => $staffData['first_name'],
                    'last_name'      => $staffData['last_name'],
                    'birthday'       => '1990-01-01',
                    'occupation'     => 'Staff',
                    'address'        => 'Staff Office',
                    'role'           => 'staff',
                    'status'         => 'Approved',
                    'sponsor_id'     => $adminMember->id, // Admin as sponsor
                    'created_at'     => $now,
                    'updated_at'     => $now,
                ]
            );
            
            // Create staff user
            User::firstOrCreate(
                ['mobile_number' => $staffData['mobile_number']],
                [
                    'name'              => $staffData['first_name'] . ' ' . $staffData['last_name'],
                    'email'             => $staffData['email'],
                    'role'              => 'Staff',
                    'member_id'         => $staffMember->id,
                    'email_verified_at' => $now,
                    'password'          => Hash::make('admin123'),
                    'status'            => 'Approved',
                    'created_at'        => $now,
                    'updated_at'        => $now,
                ]
            );
            
            $this->command->info('Staff user created: ' . $staffData['email']);
        }
        
        // Create 2 member users
        $memberUsers = [
            [
                'first_name' => 'Member',
                'last_name' => 'One',
                'mobile_number' => '09171234570',
                'email' => 'member1@example.com',
            ],
            [
                'first_name' => 'Member',
                'last_name' => 'Two',
                'mobile_number' => '09171234571',
                'email' => 'member2@example.com',
            ]
        ];
        
        foreach ($memberUsers as $memberData) {
            // Create member
            $member = Member::firstOrCreate(
                ['mobile_number' => $memberData['mobile_number']],
                [
                    'first_name'     => $memberData['first_name'],
                    'last_name'      => $memberData['last_name'],
                    'birthday'       => '1990-01-01',
                    'occupation'     => 'Member',
                    'address'        => 'Member Address',
                    'role'           => 'member',
                    'status'         => 'Approved',
                    'sponsor_id'     => $adminMember->id, // Admin as sponsor
                    'created_at'     => $now,
                    'updated_at'     => $now,
                ]
            );
            
            // Create user
            User::firstOrCreate(
                ['mobile_number' => $memberData['mobile_number']],
                [
                    'name'              => $memberData['first_name'] . ' ' . $memberData['last_name'],
                    'email'             => $memberData['email'],
                    'role'              => 'Member',
                    'member_id'         => $member->id,
                    'email_verified_at' => $now,
                    'password'          => Hash::make('admin123'),
                    'status'            => 'Approved',
                    'created_at'        => $now,
                    'updated_at'        => $now,
                ]
            );
            
            $this->command->info('Member user created: ' . $memberData['email']);
        }
        
        $this->command->info('Admin user created successfully.');
        $this->command->info('Email: admin@example.com');
        $this->command->info('Mobile: 09171234567');
        $this->command->info('Password: admin123');
        $this->command->info('Staff users created with password: admin123');
        $this->command->info('Member users created with password: admin123');
    }
}