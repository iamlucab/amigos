<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Database\Seeders\DeploymentSeeder;

class PrepareDeployment extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'deploy:prepare {--force : Force the operation without confirmation}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Prepare database for deployment by clearing data and seeding with admin user';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        if (!$this->option('force')) {
            if (!$this->confirm('This will clear most database tables and reset with deployment data. Are you sure?')) {
                $this->info('Operation cancelled.');
                return 0;
            }
        }

        $this->info('Starting deployment preparation...');
        $this->newLine();

        try {
            // Run the deployment seeder
            $seeder = new DeploymentSeeder();
            $seeder->setCommand($this);
            $seeder->run();

            $this->newLine();
            $this->info('✅ Deployment preparation completed successfully!');
            $this->newLine();
            
            $this->table(['Role', 'Username', 'Email', 'Password'], [
                ['Admin', '09177260180', 'mrcabandez@gmail.com', '!@#123123'],
                ['Staff', 'staff001', 'staff1@ebili.com', 'password123'],
                ['Staff', 'staff002', 'staff2@ebili.com', 'password123'],
                ['Member', 'member001', 'member1@ebili.com', 'password123'],
                ['Member', 'member002', 'member2@ebili.com', 'password123'],
                ['Member', 'member003', 'member3@ebili.com', 'password123'],
            ]);

            $this->newLine();
            $this->info('Preserved tables: categories, migrations, products, units');
            $this->info('All users have been synced to the members table.');
            
            return 0;
        } catch (\Exception $e) {
            $this->error('❌ Deployment preparation failed: ' . $e->getMessage());
            return 1;
        }
    }
}