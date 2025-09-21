<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
   public function run()
{
    $categories = [
        'Food',
        'Drinks',
        'Household',
        'Apparels',
        'Health & Beauty',
        'Electronics',
        'Sports & Outdoors',
        'Toys & Games',
        'Books & Stationery',
        'Automotive',
        'Pets',
        'Gardening',
        'Office Supplies',
        'Jewelry & Accessories',
        'Music & Movies',
    ];
    
    foreach ($categories as $category) {
        \App\Models\Category::firstOrCreate(['name' => $category]);
    }
    
    $this->command->info('Categories seeded successfully.');
}
}
