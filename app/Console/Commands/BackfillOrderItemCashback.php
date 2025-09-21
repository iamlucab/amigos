<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\OrderItem;
use App\Models\Product;

class BackfillOrderItemCashback extends Command
{
    protected $signature = 'backfill:cashback';
    protected $description = 'Backfill cashback_amount in order_items based on product values';

    public function handle()
    {
        $count = 0;

        OrderItem::with('product')
            ->whereNull('cashback_amount')
            ->orWhere('cashback_amount', 0)
            ->chunkById(100, function ($items) use (&$count) {
                foreach ($items as $item) {
                    $productCashback = $item->product->cashback_amount ?? 0;
                    $item->cashback_amount = $productCashback;
                    $item->save();
                    $count++;
                }
            });

        $this->info("✅ Backfilled cashback_amount for $count order_items.");
    }
}
