<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\{User, Member, Wallet, WalletTransaction, ReferralBonusLog};
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Carbon\Carbon;

class AdminWithReferralSeeder extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now();

        // 1. Create Admin Member
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

        // 2. Create Admin User
        User::firstOrCreate(
            ['mobile_number' => '09170000001'],
            [
                'name'              => $adminMember->first_name . ' ' . $adminMember->last_name,
                'email'             => 'admin@hugpong.com',
                'role'              => 'Admin',
                'member_id'         => $adminMember->id,
                'email_verified_at' => $now,
                'password'          => Hash::make('admin123'),
                'status'            => 'Approved',
                'created_at'        => $now,
                'updated_at'        => $now,
            ]
        );

        // 3. Create Admin Wallets
        foreach (['main', 'cashback'] as $type) {
            Wallet::firstOrCreate([
                'member_id' => $adminMember->id,
                'type'      => $type,
            ], [
                'balance'    => 0,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }

        $this->command->info('✅ Admin seeded successfully with wallets.');
    }
}
