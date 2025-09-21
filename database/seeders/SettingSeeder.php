<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Setting;

class SettingSeeder extends Seeder
{
    public function run(): void
    {
        Setting::updateOrCreate(['key' => 'discount_values'], [
            'value' => json_encode(["10", "20", "50"])
        ]);

        Setting::updateOrCreate(['key' => 'promo_codes'], [
            'value' => json_encode(["PROMO10", "NEW25"])
        ]);

        Setting::updateOrCreate(['key' => 'available_sizes'], [
            'value' => json_encode(["S", "M", "L", "XL"])
        ]);

        Setting::updateOrCreate(['key' => 'available_colors'], [
            'value' => json_encode(["Red", "Blue", "Green"])
        ]);
        
        Setting::updateOrCreate(['key' => 'membership_code_price'], [
            'value' => '100'
        ]);
    }
}
