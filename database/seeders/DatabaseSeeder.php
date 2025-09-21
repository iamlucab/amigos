<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // Essential seeders for system functionality
        $this->call([
            CategorySeeder::class,
            UnitSeeder::class,
            SettingSeeder::class,
            ReferralConfigurationSeeder::class,
        ]);
        
        // Create admin user with credentials: admin / admin123
        $this->call(AdminUserSeeder::class);
        
        // Deployment seeder - clears test data and creates Super Admin
        $this->call(DeploymentSeeder::class);
        
        // Clear all data except specified tables and seed roles
        $this->call(ClearAndSeedRolesSeeder::class);
    }
}
