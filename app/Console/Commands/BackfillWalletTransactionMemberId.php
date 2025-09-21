<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\WalletTransaction;
use App\Models\Wallet;

class BackfillWalletTransactionMemberId extends Command
{
    protected $signature = 'wallet:backfill-member-id';
    protected $description = 'Backfill member_id in wallet_transactions table using wallet relation';

    public function handle()
    {
        $count = 0;

        WalletTransaction::whereNull('member_id')
            ->with('wallet.member') // eager load to avoid N+1
            ->chunkById(100, function ($transactions) use (&$count) {
                foreach ($transactions as $transaction) {
                    if ($transaction->wallet && $transaction->wallet->member_id) {
                        $transaction->member_id = $transaction->wallet->member_id;
                        $transaction->save();
                        $count++;
                    }
                }
            });

        $this->info("Backfilling completed. Total updated: {$count}");
    }
}
