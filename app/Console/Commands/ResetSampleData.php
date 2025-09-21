<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Artisan;

class ResetSampleData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sample:reset';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Reset sample data, preserving users, members, wallets, and genealogy';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🔁 Resetting non-member tables...');

        DB::statement('SET FOREIGN_KEY_CHECKS=0');

        $tablesToTruncate = [
            'products',
            'categories',
            'units',
            'orders',
            'order_items',
            'rewards',
            'referral_bonus_logs',
            'tickets',
            'promos',
            'discounts',
            'carts',
        ];

        foreach ($tablesToTruncate as $table) {
            if (DB::getSchemaBuilder()->hasTable($table)) {
                DB::table($table)->truncate();
                $this->info("✅ Truncated: {$table}");
            } else {
                $this->warn("⚠️  Skipped missing table: {$table}");
            }
        }

        DB::statement('SET FOREIGN_KEY_CHECKS=1');

        $this->info('🔄 Reseeding sample data...');
        Artisan::call('db:seed');

        $this->info('🎉 Sample data reset complete.');
    }
}