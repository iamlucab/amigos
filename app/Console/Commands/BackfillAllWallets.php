<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Member;
use App\Models\Wallet;

class BackfillAllWallets extends Command
{
    protected $signature = 'wallets:backfill-all';
    protected $description = 'Backfill missing main and cashback wallets for members';

    public function handle()
    {
        $members = Member::with(['wallet', 'cashbackWallet'])->get();
        $created = 0;

        foreach ($members as $member) {
            if (!$member->wallet) {
                $member->allWallets()->create([
                    'wallet_id' => Wallet::generateWalletId(),
                    'type' => 'main',
                    'balance' => 0,
                ]);
                $this->info("Created MAIN wallet for Member ID {$member->id}");
                $created++;
            }

            if (!$member->cashbackWallet) {
                $member->allWallets()->create([
                    'wallet_id' => Wallet::generateWalletId(),
                    'type' => 'cashback',
                    'balance' => 0,
                ]);
                $this->info("Created CASHBACK wallet for Member ID {$member->id}");
                $created++;
            }
        }

        $this->info("✅ Done. Created {$created} missing wallets.");
    }
}
