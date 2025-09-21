<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Member;
use App\Models\Wallet;
use Illuminate\Support\Str;

class BackfillWallets extends Command
{
    protected $signature = 'wallets:backfill';
    protected $description = 'Backfill missing wallets for members';

    public function handle()
    {
        $membersWithoutWallets = Member::doesntHave('wallet')->get();

        foreach ($membersWithoutWallets as $member) {
            Wallet::create([
                'type' => 'main',
                'wallet_id' => 'WALLET-' . strtoupper(Str::random(13)),
                'balance' => 0,
                'member_id' => $member->id,
                'user_id' => $member->user?->id,
            ]);

            $this->info("✅ Wallet created for Member ID: {$member->id}");
        }

        $this->info('🎉 Backfill complete!');
    }
}
