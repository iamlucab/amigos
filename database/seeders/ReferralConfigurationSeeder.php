<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\ReferralConfiguration;

class ReferralConfigurationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Deactivate any existing configurations
        ReferralConfiguration::where('is_active', true)
            ->update(['is_active' => false]);
            
        // Create default configuration based on current env values
        ReferralConfiguration::create([
            'name' => 'Default Configuration',
            'total_allocation' => env('LEVEL_1_BONUS', 25) + env('LEVEL_2_BONUS', 15) + env('LEVEL_3_BONUS', 10),
            'max_level' => 3,
            'level_bonuses' => [
                '1' => env('LEVEL_1_BONUS', 25),
                '2' => env('LEVEL_2_BONUS', 15),
                '3' => env('LEVEL_3_BONUS', 10),
            ],
            'description' => 'Default configuration based on original system settings',
            'is_active' => true,
        ]);
        
        $this->command->info('✅ Default referral configuration created');
    }
}
