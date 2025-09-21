<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\{User, Member, Wallet, Product, Order, OrderItem};
use Illuminate\Support\Str;

class OrderTestSeeder extends Seeder
{
    public function run(): void
    {
        // Create Members with Wallets
        \App\Models\User::factory(3)->create()->each(function ($user) {
            $member = Member::factory()->create(['user_id' => $user->id]);
            $member->wallet()->create(['balance' => 0]);
            $member->cashbackWallet()->create(['balance' => 0]);
        });

        // Create Products
        $products = Product::factory()->count(5)->create([
            'price' => rand(50, 200),
            'cashback' => rand(5, 20)
        ]);

        // Create Orders
        Member::with('cashbackWallet')->get()->each(function ($member) use ($products) {
            $order = Order::create([
                'member_id' => $member->id,
                'total_amount' => 0,
                'total_cashback' => 0,
                'status' => 'On the Way',
                'payment_method' => 'COD',
                'cashback_given' => false,
            ]);

            $total = 0;
            $totalCashback = 0;

            $items = $products->random(2);

            foreach ($items as $product) {
                $qty = rand(1, 3);
                $total += $product->price * $qty;
                $totalCashback += $product->cashback * $qty;

                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $product->id,
                    'price' => $product->price,
                    'quantity' => $qty,
                    'cashback' => $product->cashback
                ]);
            }

            $order->update([
                'total_amount' => $total,
                'total_cashback' => $totalCashback,
            ]);
        });
    }
}
