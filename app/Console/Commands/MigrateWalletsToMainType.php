<?php

namespace App\Console\Commands;
use Illuminate\Support\Facades\Schema;
use Illuminate\Console\Command;
use App\Models\Wallet;
use Illuminate\Support\Facades\DB;

class MigrateWalletsToMainType extends Command
{
    protected $signature = 'wallets:migrate-to-main-type';
    protected $description = 'Ensure all existing wallets are tagged as type = main, and move any member.wallet_balance to Wallet table';

    public function handle()
    {
        $this->info('Starting wallet migration...');

        DB::transaction(function () {
            $walletsUpdated = Wallet::whereNull('type')->orWhere('type', '')->update(['type' => 'main']);

            $this->info("Updated {$walletsUpdated} wallet(s) to type = main.");

            // OPTIONAL: Move wallet_balance from members table if it exists
            if (Schema::hasColumn('members', 'wallet_balance')) {
                $affected = 0;

                $members = \App\Models\Member::with('wallet')->whereNotNull('wallet_balance')->get();

                foreach ($members as $member) {
                    if ($member->wallet && $member->wallet->type === 'main') {
                        $member->wallet->balance = $member->wallet_balance;
                        $member->wallet->save();
                        $affected++;
                    }
                }

                $this->info("Migrated {$affected} member balances to their wallet records.");
            } else {
                $this->warn("No wallet_balance column found in members table — skipping balance sync.");
            }
        });

        $this->info('Wallet migration complete.');
    }
}
