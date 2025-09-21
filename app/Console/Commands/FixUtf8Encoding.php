<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class FixUtf8Encoding extends Command
{
    protected $signature = 'fix:utf8 {--dry-run} {--log}';
    protected $description = 'Fix garbled UTF-8 characters in target tables with dry-run and logging support';

    // Define tables and their primary key if not 'id'
    protected $tables = [
        'orders'               => 'id',
        'order_items'          => 'id',
        'products'             => 'id',
        'wallets'              => 'id',
        'wallet_transactions'  => 'id',
        'referral_bonus_logs'  => 'id',
        'reward_programs'      => 'id',
        'loans'                => 'id',
        'loan_payments'        => 'id',
        'cash_in_requests'     => 'id',
        'cashback_logs'        => 'id',
    ];

    public function handle()
    {
        $dryRun = $this->option('dry-run');
        $enableLog = $this->option('log');

        if ($enableLog) {
            Log::channel('single')->info("=== UTF8 Fix Started at " . now() . " ===");
        }

        foreach ($this->tables as $table => $primaryKey) {
            $this->info("🔍 Checking table: {$table}");

            if (!Schema::hasTable($table)) {
                $this->warn("⚠️ Table '{$table}' not found. Skipping.");
                continue;
            }

            $columns = Schema::getColumnListing($table);
            $stringColumns = [];

            // Detect string/text columns only
            foreach ($columns as $column) {
                try {
                    $type = Schema::getConnection()
                        ->getDoctrineColumn($table, $column)
                        ->getType()
                        ->getName();

                    if (in_array($type, ['string', 'text'])) {
                        $stringColumns[] = $column;
                    }
                } catch (\Throwable $e) {
                    $this->warn("⚠️ Skipping column {$column} due to error: " . $e->getMessage());
                }
            }

            if (empty($stringColumns)) {
                $this->warn("⚠️ No string/text columns in {$table}. Skipping.");
                continue;
            }

            $records = DB::table($table)->select($primaryKey, ...$stringColumns)->get();

            foreach ($records as $record) {
                $updates = [];
                foreach ($stringColumns as $col) {
                    $original = $record->$col;
                    if ($original === null) continue;

                    $fixed = mb_convert_encoding($original, 'UTF-8', 'UTF-8');

                    if ($original !== $fixed) {
                        $updates[$col] = $fixed;
                    }
                }

                if (!empty($updates)) {
                    $id = $record->{$primaryKey};

                    if ($dryRun) {
                        $this->line("🔎 Would fix {$table} [{$primaryKey} = $id]: " . implode(', ', array_keys($updates)));
                    } else {
                        DB::table($table)->where($primaryKey, $id)->update($updates);
                        $this->line("✅ Fixed {$table} [{$primaryKey} = $id]");

                        if ($enableLog) {
                            Log::channel('single')->info("Fixed {$table} [{$primaryKey} = $id] - Columns: " . implode(', ', array_keys($updates)));
                        }
                    }
                }
            }

            $this->info("✅ Done: {$table}");
        }

        $this->info("🎉 All done!");

        if ($enableLog) {
            Log::channel('single')->info("=== UTF8 Fix Completed at " . now() . " ===");
        }
    }
}
