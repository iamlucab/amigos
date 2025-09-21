<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Product;
use App\Models\User;

class FixProductOwnership extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'products:fix-ownership {--user-id= : Assign all products without created_by to this user ID}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fix products that don\'t have created_by field set';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $userId = $this->option('user-id');
        
        // Find products without created_by
        $productsWithoutOwner = Product::whereNull('created_by')->get();
        
        if ($productsWithoutOwner->isEmpty()) {
            $this->info('All products already have owners assigned.');
            return;
        }
        
        $this->info("Found {$productsWithoutOwner->count()} products without owners:");
        
        foreach ($productsWithoutOwner as $product) {
            $this->line("- Product ID {$product->id}: {$product->name}");
        }
        
        if (!$userId) {
            // Show available users
            $this->info("\nAvailable users:");
            $users = User::whereIn('role', ['Admin', 'Staff'])->get();
            foreach ($users as $user) {
                $this->line("- User ID {$user->id}: {$user->name} ({$user->role})");
            }
            
            $userId = $this->ask('Enter the user ID to assign these products to');
        }
        
        $user = User::find($userId);
        if (!$user) {
            $this->error('User not found!');
            return;
        }
        
        if (!in_array($user->role, ['Admin', 'Staff'])) {
            $this->error('User must be Admin or Staff!');
            return;
        }
        
        if ($this->confirm("Assign all {$productsWithoutOwner->count()} products to {$user->name} ({$user->role})?")) {
            Product::whereNull('created_by')->update(['created_by' => $userId]);
            $this->info("Successfully assigned {$productsWithoutOwner->count()} products to {$user->name}");
        } else {
            $this->info('Operation cancelled.');
        }
    }
}