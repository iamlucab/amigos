<?php

namespace Database\Seeders;

use App\Models\Member;
use App\Models\Wallet;
use Illuminate\Database\Seeder;

class BackfillWalletSeeder extends Seeder
{
    public function run()
    {
        $members = Member::doesntHave('wallet')->get();

        foreach ($members as $member) {
            $member->wallet()->create([
                'balance' => 0,
                'wallet_id' => Wallet::generateWalletId(),
            ]);
        }

        $this->command->info("Wallets created for {$members->count()} members.");
    }
}
