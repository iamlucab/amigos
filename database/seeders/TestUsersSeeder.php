<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Member;
use App\Models\Wallet;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class TestUsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $now = Carbon::now();
        
        // Create admin
        $adminMember = Member::firstOrCreate(
            ['mobile_number' => '09170000001'],
            [
                'first_name'     => 'System',
                'middle_name'    => 'A.',
                'last_name'      => 'Admin',
                'birthday'       => '1990-01-01',
                'occupation'     => 'Administrator',
                'address'        => 'HQ City',
                'photo'          => 'default-profile.png',
                'role'           => 'Admin',
                'status'         => 'Approved',
                'loan_eligible'  => true,
                'created_at'     => $now,
                'updated_at'     => $now,
            ]
        );
        
        User::firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name'              => $adminMember->first_name . ' ' . $adminMember->last_name,
                'mobile_number'     => '09170000001',
                'role'              => 'Admin',
                'member_id'         => $adminMember->id,
                'email_verified_at' => $now,
                'password'          => Hash::make('password123'),
                'status'            => 'Approved',
                'created_at'        => $now,
                'updated_at'        => $now,
            ]
        );
        
        // Create wallets for admin
        foreach (['main', 'cashback'] as $type) {
            Wallet::firstOrCreate([
                'member_id' => $adminMember->id,
                'type'      => $type,
            ], [
                'wallet_id'  => 'WALLET-' . strtoupper(uniqid()),
                'balance'    => 1000, // Give admin some initial balance
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }
        
        $this->command->info('Admin created successfully.');
    }
}
