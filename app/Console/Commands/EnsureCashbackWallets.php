<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Member;
use App\Models\Wallet;
use Illuminate\Support\Facades\DB;

class EnsureCashbackWallets extends Command
{
    protected $signature = 'wallets:ensure-cashback';

    protected $description = 'Ensure all members have a cashback wallet';

    public function handle()
    {
        $this->info('Checking members without cashback wallets...');

        $count = 0;

        Member::doesntHave('cashbackWallet')->chunkById(100, function ($members) use (&$count) {
            foreach ($members as $member) {
                DB::transaction(function () use ($member, &$count) {
                    $member->allWallets()->create([
                        'wallet_id' => Wallet::generateWalletId(),
                        'type' => 'cashback',
                        'balance' => 0,
                    ]);
                    $count++;
                });
            }
        });

        $this->info("Created cashback wallets for {$count} member(s).");
        $this->info('✅ Done.');
        return 0;
    }
}
