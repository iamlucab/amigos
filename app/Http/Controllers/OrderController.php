<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\WalletTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class OrderController extends Controller
{
    /**
     * 🛒 Member creates an order
     */
    public function store(Request $request)
    {
        $member = Auth::user()->member;
        $cart = session()->get('cart', []);

        if (empty($cart)) {
            return redirect()->back()->with('error', 'Your cart is empty.');
        }

        DB::beginTransaction();

        try {
            $order = Order::create([
                'member_id'      => $member->id,
                'total_amount'   => collect($cart)->sum(fn($item) => $item['price'] * $item['quantity']),
                'status'         => 'Pending',
                'delivery_type'  => $request->delivery_type,
                'address'        => $request->address,
                'contact_number' => $request->contact_number,
                'payment_method' => $request->payment_method,
                'shipping_fee'   => $request->shipping_fee ?? 0,
                'reference_no'   => $request->reference_no,
                'remarks'        => $request->remarks,
            ]);

            foreach ($cart as $productId => $item) {
                $product = Product::findOrFail($productId);

                OrderItem::create([
                    'order_id'        => $order->id,
                    'product_id'      => $product->id,
                    'quantity'        => $item['quantity'],
                    'price'           => $product->price,
                    'cashback'        => $product->cashback,
                    'cashback_amount' => $product->cashback * $item['quantity'],
                ]);
            }

            // Deduct wallet if chosen
            if ($request->payment_method === 'Wallet') {
                $wallet = $member->wallet;
                $wallet->decrement('balance', $order->total_amount + $order->shipping_fee);

                WalletTransaction::create([
                    'wallet_id' => $wallet->id,
                    'type'      => 'debit',
                    'amount'    => $order->total_amount + $order->shipping_fee,
                    'reference' => 'Order #' . $order->id,
                    'status'    => 'Completed',
                ]);
            }

            session()->forget('cart');
            DB::commit();

            return redirect()->route('member.orders')->with('success', 'Order placed successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Order creation failed: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to place order.');
        }
    }

    /**
     * 📄 Member order listing with optional filters
     */
    public function index(Request $request)
{
    $member = Auth::user()->member;

    $orders = Order::with('items.product')
        ->where('member_id', $member->id)
        ->when($request->date_from, fn($q) => $q->whereDate('created_at', '>=', $request->date_from))
        ->when($request->date_to, fn($q) => $q->whereDate('created_at', '<=', $request->date_to))
        ->when($request->status, fn($q) => $q->where('status', $request->status))
        ->latest()
        ->get();

    // Total of all items from Delivered orders
    $totalSales = $orders->where('status', 'Delivered')
        ->flatMap->items
        ->sum(fn($item) => $item->price * $item->quantity);

    // Cashback On Hold (Pending, Processing, On the Way)
$totalCashback = $orders->where('status', '!=', 'Delivered')
    ->flatMap->items
    ->sum('cashback_amount');

    // Cashback Credited (Delivered)

$walletCashback = $orders->where('status', 'Delivered')
    ->flatMap->items
    ->sum('cashback_amount');

    
    // Paginate orders after filtering
    $paginated = $orders->forPage($request->get('page', 1), 10);

    return view('members.orders.index', [
        'orders' => new \Illuminate\Pagination\LengthAwarePaginator(
            $paginated,
            $orders->count(),
            10,
            $request->get('page', 1),
            ['path' => $request->url(), 'query' => $request->query()]
        ),
        'totalSales' => $totalSales,
        'totalCashback' => $totalCashback,
        'walletCashback' => $walletCashback,
    ]);
}



    /**
     * 📦 Show details of a specific order (member-owned)
     */
    public function show($id)
    {
        $member = Auth::user()->member;
        $order = Order::with('items.product')->where('member_id', $member->id)->findOrFail($id);

        return view('members.orders.show', compact('order'));
    }

    
    /**
     * 🚚 Track order status
     */
    public function track($id)
    {
        $member = Auth::user()->member;
        $order = Order::where('member_id', $member->id)->findOrFail($id);

        return view('members.orders.track', compact('order'));
    }

public function cancel($id)
{
    $member = Auth::user()->member;
    $order = Order::where('member_id', $member->id)
                  ->where('status', 'Pending')
                  ->findOrFail($id);

    // Restrict: only allow cancel within X minutes (see next step)
    $cutoff = now()->subMinutes(10); // change to 30 if needed
    if ($order->created_at < $cutoff) {
        return redirect()->back()->with('error', 'You can no longer cancel this order.');
    }

    DB::transaction(function () use ($order, $member) {
        $order->update([
            'status' => 'Cancelled',
            'remarks' => 'Cancelled by member on ' . now()->format('Y-m-d H:i'),
        ]);

        // Refund wallet if paid via wallet
        if ($order->payment_method === 'Wallet') {
            $refundAmount = $order->total_amount + $order->shipping_fee;
            $wallet = $member->wallet;
            $wallet->increment('balance', $refundAmount);

            WalletTransaction::create([
                'wallet_id' => $wallet->id,
                'member_id' => $member->id,
                'amount'    => $refundAmount,
                'type'      => 'refund',
                'description' => 'Refund for cancelled order #' . $order->id,
            ]);
        }
    });

    return redirect()->route('member.orders')->with('success', 'Order has been cancelled and wallet refunded.');
}


}
