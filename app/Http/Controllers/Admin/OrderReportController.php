<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\WalletTransaction;
use App\Models\CashbackLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;

class OrderReportController extends Controller
{
    /**
     * Display a filtered list of orders with sales chart data.
     */
    public function index(Request $request)
    {
        $orders = Order::with(['member.user', 'items.product'])
            ->when($request->member, function ($query) use ($request) {
                $query->whereHas('member', function ($subQuery) use ($request) {
                    $subQuery->where('mobile_number', 'like', '%' . $request->member . '%')
                        ->orWhereHas('user', function ($userQuery) use ($request) {
                            $userQuery->where('name', 'like', '%' . $request->member . '%');
                        });
                });
            })
            ->when($request->status, fn($query) => $query->where('status', $request->status))
            ->when($request->from, fn($query) => $query->whereDate('created_at', '>=', $request->from))
            ->when($request->to, fn($query) => $query->whereDate('created_at', '<=', $request->to))
            ->latest()
            ->paginate(20);

        $chartRaw = Order::selectRaw('DATE(created_at) as date, SUM(total_amount) as total')
            ->when($request->from, fn($query) => $query->whereDate('created_at', '>=', $request->from))
            ->when($request->to, fn($query) => $query->whereDate('created_at', '<=', $request->to))
            ->when($request->status, fn($query) => $query->where('status', $request->status))
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        $chartData = [
            'dates' => $chartRaw->pluck('date')->map(fn($d) => Carbon::parse($d)->format('M d'))->toArray(),
            'totals' => $chartRaw->pluck('total')->map(fn($t) => round($t, 2))->toArray(),
        ];

        return view('admin.orders.index', compact('orders', 'chartData'));
    }

    /**
     * Generate PDF invoice for a specific order.
     */
    public function invoice($id)
    {
        $order = Order::with(['items.product', 'member.user'])->findOrFail($id);
        $pdf = Pdf::loadView('admin.orders.invoice', compact('order'));

        return $pdf->stream("Invoice-Order-{$order->id}.pdf");
    }

    /**
     * Update the status of an order and apply cashback if delivered.
     */
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|string|in:Pending,Processing,On the Way,Delivered',
        ]);

        $order = Order::with(['items.product', 'member.cashbackWallet'])->findOrFail($id);

        if ($order->status === 'Delivered') {
            return back()->with('warning', 'Order is already marked as Delivered.');
        }

        DB::transaction(function () use ($order, $request) {
            $order->status = $request->status;
            $order->save();

            // Handle cashback when order is delivered
            if ($order->status === 'Delivered') {
                foreach ($order->items as $item) {
                    $product = $item->product;
                    $member = $order->member;
                    $totalItemCashback = $product->cashback_amount * $item->quantity;
                    
                    // Log the total cashback for this item
                    CashbackLog::create([
                        'member_id' => $member->id,
                        'order_id' => $order->id,
                        'product_id' => $product->id,
                        'amount' => $totalItemCashback,
                        'description' => 'Cashback from Order #' . $order->id,
                    ]);
                    
                    // Credit the member's cashback wallet
                    if ($totalItemCashback > 0 && $member->cashbackWallet) {
                        // Credit the member's cashback wallet
                        $member->cashbackWallet->increment('balance', $totalItemCashback);
                        
                        // Create transaction record
                        WalletTransaction::create([
                            'wallet_id' => $member->cashbackWallet->id,
                            'member_id' => $member->id,
                            'type' => 'cashback',
                            'amount' => $totalItemCashback,
                            'description' => "Cashback earned from purchase of {$product->name}",
                            'source' => 'product_cashback',
                        ]);
                    }
                    
                    // Distribute cashback to upline based on product configuration
                    $sponsor = $member->sponsor;
                    $level = 1;
                    
                    // Get all cashback amounts for this product
                    $cashbacks = $product->getAllCashbacks();
                    
                    // Distribute to upline
                    while ($sponsor && $level <= $product->cashback_max_level) {
                        $cashbackAmount = ($cashbacks[$level] ?? 0) * $item->quantity;
                        
                        if ($cashbackAmount > 0 && $sponsor->cashbackWallet) {
                            $levelText = $level == 1 ? "Direct" : "{$level}nd level";
                            
                            // Credit the sponsor's cashback wallet
                            $sponsor->cashbackWallet->increment('balance', $cashbackAmount);
                            
                            // Create transaction record
                            WalletTransaction::create([
                                'wallet_id' => $sponsor->cashbackWallet->id,
                                'member_id' => $sponsor->id,
                                'type' => 'cashback',
                                'amount' => $cashbackAmount,
                                'description' => "{$levelText} product cashback from {$member->full_name}'s purchase of {$product->name}",
                                'source' => 'product_cashback',
                            ]);
                            
                            // Log the cashback distribution
                            CashbackLog::create([
                                'member_id' => $sponsor->id,
                                'order_id' => $order->id,
                                'product_id' => $product->id,
                                'amount' => $cashbackAmount,
                                'description' => "{$levelText} product cashback from {$member->full_name}'s purchase",
                                'level' => $level,
                            ]);
                        }
                        
                        // Move up to the next sponsor
                        $sponsor = $sponsor->sponsor;
                        $level++;
                    }
                }
            }
        });

        return back()->with('success', 'Order status updated successfully.');
    }

    /**
     * Update the status of an individual order item.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function updateItemStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|string|in:Pending,In process,On the Way,Delivered,Cancelled',
        ]);

        $orderItem = OrderItem::with(['order.member.cashbackWallet', 'product'])->findOrFail($id);
        $oldStatus = $orderItem->status;
        $newStatus = $request->status;
        
        // Update the item status
        $orderItem->status = $newStatus;
        $orderItem->save();
        
        // Handle cashback when item is delivered
        if ($newStatus === 'Delivered' && $oldStatus !== 'Delivered') {
            $this->processCashbackForItem($orderItem);
        }
        
        // Check if all items in the order are delivered or cancelled
        $order = $orderItem->order;
        $allItemsProcessed = $order->items()
            ->whereNotIn('status', ['Delivered', 'Cancelled'])
            ->count() === 0;
            
        if ($allItemsProcessed) {
            $order->status = 'Delivered';
            $order->save();
        }
        
        return back()->with('success', "Item status updated to {$newStatus}.");
    }
    
    /**
     * Process cashback for a delivered order item.
     *
     * @param  \App\Models\OrderItem  $orderItem
     * @return void
     */
    private function processCashbackForItem($orderItem)
    {
        $product = $orderItem->product;
        $order = $orderItem->order;
        $member = $order->member;
        $totalItemCashback = $product->cashback_amount * $orderItem->quantity;
        
        // Log the total cashback for this item
        CashbackLog::create([
            'member_id' => $member->id,
            'order_id' => $order->id,
            'product_id' => $product->id,
            'amount' => $totalItemCashback,
            'description' => "Cashback from Order #{$order->id} - {$product->name}",
        ]);
        
        // Credit the member's cashback wallet
        if ($totalItemCashback > 0 && $member->cashbackWallet) {
            // Credit the member's cashback wallet
            $member->cashbackWallet->increment('balance', $totalItemCashback);
            
            // Create transaction record
            WalletTransaction::create([
                'wallet_id' => $member->cashbackWallet->id,
                'member_id' => $member->id,
                'type' => 'cashback',
                'amount' => $totalItemCashback,
                'description' => "Cashback earned from purchase of {$product->name}",
                'source' => 'product_cashback',
            ]);
        }
        
        // Distribute cashback to upline based on product configuration
        $sponsor = $member->sponsor;
        $level = 1;
        
        // Get all cashback amounts for this product
        $cashbacks = $product->getAllCashbacks();
        
        // Distribute to upline
        while ($sponsor && $level <= $product->cashback_max_level) {
            $cashbackAmount = ($cashbacks[$level] ?? 0) * $orderItem->quantity;
            
            if ($cashbackAmount > 0 && $sponsor->cashbackWallet) {
                $levelText = $level == 1 ? "Direct" : "{$level}nd level";
                
                // Credit the sponsor's cashback wallet
                $sponsor->cashbackWallet->increment('balance', $cashbackAmount);
                
                // Create transaction record
                WalletTransaction::create([
                    'wallet_id' => $sponsor->cashbackWallet->id,
                    'member_id' => $sponsor->id,
                    'type' => 'cashback',
                    'amount' => $cashbackAmount,
                    'description' => "{$levelText} product cashback from {$member->full_name}'s purchase of {$product->name}",
                    'source' => 'product_cashback',
                ]);
                
                // Log the cashback distribution
                CashbackLog::create([
                    'member_id' => $sponsor->id,
                    'order_id' => $order->id,
                    'product_id' => $product->id,
                    'amount' => $cashbackAmount,
                    'description' => "{$levelText} product cashback from {$member->full_name}'s purchase",
                    'level' => $level,
                ]);
            }
            
            // Move up to the next sponsor
            $sponsor = $sponsor->sponsor;
            $level++;
        }
    }
}
