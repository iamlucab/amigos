<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\WalletTransaction;
use App\Models\Member;
use App\Models\Wallet;


class WalletTransactionSeeder extends Seeder
{
    public function run()
    {
        $wallet = Wallet::first(); // or Wallet::find(1);

        if (!$wallet) {
            $this->command->error('No wallet found. Seed a wallet first.');
            return;
        }

        WalletTransaction::insert([
            [
                'wallet_id' => $wallet->id,
                'type' => 'debit',
                'amount' => 250.00,
                'source' => 'product_purchase',
                'description' => 'Ordered items from shop',
                'related_member_id' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'wallet_id' => $wallet->id,
                'type' => 'credit',
                'amount' => 50.00,
                'source' => 'cashback',
                'description' => 'Cashback from order #1',
                'related_member_id' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }

}
