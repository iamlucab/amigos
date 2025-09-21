<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class ResetDatabaseButKeepMembers extends Command
{
    protected $signature = 'db:reset-keep-members';
    protected $description = 'Reset all tables but retain users, members, and genealogy relationships';

    public function handle()
    {
        $this->info('🔒 Backing up users and members...');

        $users = DB::table('users')->get();
        $members = DB::table('members')->get();

        $this->info('🧹 Truncating all tables except users and members...');

        $tablesToExclude = ['migrations', 'users', 'members'];

        $tables = DB::connection()->getDoctrineSchemaManager()->listTableNames();

        Schema::disableForeignKeyConstraints();
        foreach ($tables as $table) {
            if (!in_array($table, $tablesToExclude)) {
                DB::table($table)->truncate();
                $this->line("✅ Truncated: $table");
            }
        }
        Schema::enableForeignKeyConstraints();

        $this->info('✅ Database reset while retaining users and members.');
    }
}
