<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use App\Models\User;
use App\Models\Member;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class ClearAndSeedRolesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Disable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        // Tables to keep data (excluding migrations which should never be cleared)
        $tablesToKeep = [
            'categories',
            'migrations',
            'products',
            'settings',
            'units'
        ];

        // Get all table names
        $tables = DB::select('SHOW TABLES');
        $tableNameKey = 'Tables_in_' . env('DB_DATABASE');

        // Clear all tables except the ones to keep
        foreach ($tables as $table) {
            $tableName = $table->$tableNameKey;
            
            // Skip tables that should be kept
            if (in_array($tableName, $tablesToKeep)) {
                $this->command->info("Keeping table: $tableName");
                continue;
            }
            
            // Skip migrations table as it's needed to track migrations
            if ($tableName === 'migrations') {
                $this->command->info("Keeping table: $tableName");
                continue;
            }
            
            // Clear the table
            try {
                DB::table($tableName)->truncate();
                $this->command->info("Cleared table: $tableName");
            } catch (\Exception $e) {
                $this->command->warn("Could not clear table $tableName: " . $e->getMessage());
            }
        }

        // Re-enable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // Create timestamp for all records
        $now = Carbon::now();
        
        // Create Administrator role
        $adminMember = Member::create([
            'first_name'     => 'System',
            'last_name'      => 'Administrator',
            'birthday'       => '1990-01-01',
            'mobile_number'  => '09171234567',
            'occupation'     => 'Administrator',
            'address'        => 'Admin Office',
            'role'           => 'admin',
            'status'         => 'Approved',
            'created_at'     => $now,
            'updated_at'     => $now,
        ]);
        
        $adminUser = User::create([
            'name'              => $adminMember->first_name . ' ' . $adminMember->last_name,
            'mobile_number'     => $adminMember->mobile_number,
            'email'             => 'admin@example.com',
            'role'              => 'Admin',
            'member_id'         => $adminMember->id,
            'email_verified_at' => $now,
            'password'          => Hash::make('temporary123'), // Temporary password
            'status'            => 'Approved',
            'created_at'        => $now,
            'updated_at'        => $now,
        ]);
        
        $this->command->info('Administrator created:');
        $this->command->info('- Email: admin@example.com');
        $this->command->info('- Mobile: 09171234567');
        $this->command->info('- Password: temporary123');
        
        // Create 2 Staff roles
        $staffData = [
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
        
        foreach ($staffData as $index => $data) {
            $staffMember = Member::create([
                'first_name'     => $data['first_name'],
                'last_name'      => $data['last_name'],
                'birthday'       => '1990-01-01',
                'mobile_number'  => $data['mobile_number'],
                'occupation'     => 'Staff',
                'address'        => 'Staff Office',
                'role'           => 'staff',
                'status'         => 'Approved',
                'sponsor_id'     => $adminMember->id, // Admin as sponsor
                'created_at'     => $now,
                'updated_at'     => $now,
            ]);
            
            User::create([
                'name'              => $data['first_name'] . ' ' . $data['last_name'],
                'mobile_number'     => $data['mobile_number'],
                'email'             => $data['email'],
                'role'              => 'Staff',
                'member_id'         => $staffMember->id,
                'email_verified_at' => $now,
                'password'          => Hash::make('temporary123'), // Temporary password
                'status'            => 'Approved',
                'created_at'        => $now,
                'updated_at'        => $now,
            ]);
            
            $this->command->info("Staff {$data['first_name']} {$data['last_name']} created:");
            $this->command->info("- Email: {$data['email']}");
            $this->command->info("- Password: temporary123");
        }
        
        // Create 3 Members roles
        $memberData = [
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
            ],
            [
                'first_name' => 'Member',
                'last_name' => 'Three',
                'mobile_number' => '09171234572',
                'email' => 'member3@example.com',
            ]
        ];
        
        foreach ($memberData as $index => $data) {
            $member = Member::create([
                'first_name'     => $data['first_name'],
                'last_name'      => $data['last_name'],
                'birthday'       => '1990-01-01',
                'mobile_number'  => $data['mobile_number'],
                'occupation'     => 'Member',
                'address'        => 'Member Address',
                'role'           => 'member',
                'status'         => 'Approved',
                'sponsor_id'     => $adminMember->id, // Admin as sponsor
                'created_at'     => $now,
                'updated_at'     => $now,
            ]);
            
            User::create([
                'name'              => $data['first_name'] . ' ' . $data['last_name'],
                'mobile_number'     => $data['mobile_number'],
                'email'             => $data['email'],
                'role'              => 'Member',
                'member_id'         => $member->id,
                'email_verified_at' => $now,
                'password'          => Hash::make('temporary123'), // Temporary password
                'status'            => 'Approved',
                'created_at'        => $now,
                'updated_at'        => $now,
            ]);
            
            $this->command->info("Member {$data['first_name']} {$data['last_name']} created:");
            $this->command->info("- Email: {$data['email']}");
            $this->command->info("- Password: temporary123");
        }
        
        $this->command->info('All roles seeded successfully with temporary password: temporary123');
    }
}