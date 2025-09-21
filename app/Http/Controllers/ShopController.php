<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Order;
use App\Models\WalletTransaction;
use App\Models\Setting;
use Illuminate\Http\Request;

class ShopController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::query()->with('category');

        // Search functionality
        if ($search = $request->input('q')) {
            $query->where('name', 'like', '%' . $search . '%')
                  ->orWhere('description', 'like', '%' . $search . '%');
        }

        // Category filtering
        if ($categoryId = $request->input('category')) {
            $query->where('category_id', $categoryId);
        }

        // Load More functionality - get products with offset
        $perPage = 12; // Show 12 products per load
        $page = $request->input('page', 1);
        $offset = ($page - 1) * $perPage;

        $products = $query->where('active', 1)
                         ->orderBy('created_at', 'desc')
                         ->offset($offset)
                         ->limit($perPage)
                         ->get();

        // Get total count for "Load More" button logic
        $totalProducts = $query->where('active', 1)->count();
        $hasMore = ($offset + $perPage) < $totalProducts;

        // Get all categories for filtering
        $categories = \App\Models\Category::orderBy('name')->get();

        // If it's an AJAX request (Load More), return JSON
        if ($request->ajax()) {
            return response()->json([
                'products' => $products,
                'hasMore' => $hasMore,
                'html' => view('shop.partials.product-grid', compact('products'))->render()
            ]);
        }

        return view('shop.index', compact('products', 'categories', 'hasMore', 'totalProducts'));
    }

    public function show(Product $product)
    {
        return view('shop.show', compact('product'));
    }

    public function order(Request $request, Product $product)
    {
        $cart = session()->get('cart', []);
        
        // Check if request is JSON (from fetch API)
        if ($request->isJson()) {
            $data = $request->json()->all();
            $quantity = isset($data['quantity']) ? (int)$data['quantity'] : 1;
        } else {
            // Regular form submission
            $quantity = (int)$request->input('quantity', 1);
        }
        
        // Ensure quantity is at least 1
        $quantity = max(1, $quantity);
        
        // If product already exists in cart, update quantity
        if (isset($cart[$product->id])) {
            $cart[$product->id]['quantity'] += $quantity;
        } else {
            // Otherwise add new item
            $cart[$product->id] = [
                'id' => $product->id,
                'name' => $product->name,
                'price' => $product->hasDiscount() ? $product->getDiscountedPrice() : $product->price,
                'original_price' => $product->price,
                'cashback' => $product->cashback_amount ?? 0,
                'thumbnail' => $product->thumbnail,
                'quantity' => $quantity,
                'has_discount' => $product->hasDiscount(),
                'discount_amount' => $product->hasDiscount() ? $product->getDiscountAmount() : 0,
            ];
        }

        session()->put('cart', $cart);
        
        // Return JSON response if request is JSON
        if ($request->isJson() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Product added to cart.',
                'cart_count' => collect($cart)->sum('quantity')
            ]);
        }

        return redirect()->back()->with('success', 'Product added to cart.');
    }

    public function cart(Request $request)
    {
        if ($request->isMethod('post')) {
            $cart = session()->get('cart', []);
            $id = $request->input('increase') ?? $request->input('decrease');

            if (isset($cart[$id])) {
                if ($request->has('increase')) {
                    $cart[$id]['quantity'] += 1;
                } elseif ($request->has('decrease')) {
                    $cart[$id]['quantity'] = max(1, $cart[$id]['quantity'] - 1);
                }
                session()->put('cart', $cart);
            }

            return redirect()->route('shop.cart');
        }

        $cart = session()->get('cart', []);
        $total = collect($cart)->sum(fn($item) => $item['price'] * $item['quantity']);

        return view('shop.cart', compact('cart', 'total'));
    }

    public function remove($id)
    {
        $cart = session()->get('cart', []);
        if (isset($cart[$id])) {
            unset($cart[$id]);
            session()->put('cart', $cart);
        }

        return redirect()->route('shop.cart')->with('success', 'Item removed from cart.');
    }

    public function checkoutPage()
    {
        $cart = session('cart', []);
        $member = auth()->user()->member;

        $total = collect($cart)->sum(fn($item) => $item['price'] * $item['quantity']);
        $totalCashback = collect($cart)->sum(fn($item) => ($item['cashback'] ?? 0) * $item['quantity']);

        $shippingFeeSetting = Setting::where('key', 'shipping_fee')->first();
        $shippingFee = $shippingFeeSetting ? floatval($shippingFeeSetting->value) : 0;

        $subtotal = $total + $shippingFee;

        return view('shop.checkout', compact(
            'cart',
            'total',
            'subtotal',
            'totalCashback',
            'shippingFee'
        ));
    }

    public function checkout(Request $request)
    {
        $rules = [
            'payment_method'     => 'required|in:Wallet,GCash,Bank,COD',
            'delivery_type'      => 'required|in:delivery,pickup',
            'contact_number'     => 'required|string|max:20',
            'reference_image'    => 'nullable|image|max:2048',
            'promo_code'         => 'nullable|string|max:50',
        ];

        if ($request->delivery_type === 'delivery') {
            $rules['delivery_address'] = 'required|string|max:255';
        }

        $request->validate($rules);

        $member = auth()->user()->member;
        $cart = session('cart', []);

        if (empty($cart)) {
            return back()->with('error', 'Your cart is empty.');
        }

        $shippingFeeSetting = Setting::where('key', 'shipping_fee')->first();
        $shippingFee = $shippingFeeSetting ? floatval($shippingFeeSetting->value) : 0;

        $total = collect($cart)->sum(fn($item) => $item['price'] * $item['quantity']);
        if ($request->delivery_type === 'delivery') {
            $total += $shippingFee;
        }

        $subtotal = $total;
        $totalCashback = collect($cart)->sum(fn($item) => ($item['cashback'] ?? 0) * $item['quantity']);
        
        // Apply promo code if provided
        $promoDiscount = 0;
        if ($request->filled('promo_code')) {
            $promoCode = $request->promo_code;
            
            // Check if any product in cart has this promo code
            foreach ($cart as $item) {
                $product = Product::find($item['id']);
                if ($product && $product->promo_code === $promoCode) {
                    // Calculate discount for this product
                    $itemTotal = $item['price'] * $item['quantity'];
                    
                    if ($product->discount_type === 'percentage') {
                        $itemDiscount = $itemTotal * ($product->discount_value / 100);
                    } else {
                        // Fixed amount discount
                        $itemDiscount = $product->discount_value * $item['quantity'];
                    }
                    
                    $promoDiscount += $itemDiscount;
                }
            }
            
            // Apply the discount to subtotal
            if ($promoDiscount > 0) {
                $subtotal -= $promoDiscount;
            }
        }

        if ($request->payment_method === 'Wallet') {
            $wallet = $member->wallet;

            if (!$wallet || $wallet->balance < $total) {
                return back()->with('error', 'Insufficient wallet balance.');
            }

            $wallet->balance -= $total;
            $wallet->save();

            WalletTransaction::create([
                'wallet_id' => $wallet->id,
                'member_id' => $member->id,
                'amount'    => -$total,
                'type'      => 'payment',
                'notes'     => 'Order paid via wallet at checkout',
            ]);
        }

        $filename = null;
        if ($request->hasFile('reference_image')) {
            $filename = $request->file('reference_image')->store('payments', 'public');
        }

        $order = Order::create([
            'member_id'         => $member->id,
            'total_amount'      => $subtotal, // Use subtotal which includes shipping and promo discounts
            'total_cashback'    => $totalCashback,
            'payment_method'    => $request->payment_method,
            'reference_image'   => $filename,
            'delivery_type'     => $request->delivery_type,
            'delivery_address'  => $request->delivery_type === 'pickup' ? null : $request->delivery_address,
            'contact_number'    => $request->contact_number,
            'status'            => 'Pending',
            'cashback_given'    => false,
            'promo_code'        => $request->promo_code,
            'promo_discount'    => $promoDiscount,
        ]);

        foreach ($cart as $item) {
            $order->items()->create([
                'product_id' => $item['id'],
                'name'       => $item['name'],
                'price'      => $item['price'],
                'quantity'   => $item['quantity'],
                'cashback'   => $item['cashback'] ?? 0,
                'cashback_amount' => ($item['cashback'] ?? 0) * $item['quantity'],
                'status'     => 'Pending',
            ]);

            $product = Product::find($item['id']);
            if ($product) {
                $product->decrement('stock_quantity', $item['quantity']);
            }
        }

        session()->forget('cart');

        return redirect()->route('orders.index')->with('success', 'Order placed successfully!');
    }


    public function updateQuantity(Request $request, $id)
    {
        $cart = session()->get('cart', []);
        $action = $request->input('action');

        if (!isset($cart[$id])) {
            return redirect()->back()->with('error', 'Item not found in cart.');
        }

        if ($action === 'increase') {
            $cart[$id]['quantity'] += 1;
        } elseif ($action === 'decrease') {
            $cart[$id]['quantity'] = max(1, $cart[$id]['quantity'] - 1);
        }

        session()->put('cart', $cart);

        return redirect()->route('shop.cart')->with('success', 'Cart updated successfully.');
    }

    public function validatePromoCode(Request $request)
    {
        $promoCode = $request->input('promo_code');
        $cart = session('cart', []);
        
        if (empty($cart)) {
            return response()->json([
                'valid' => false,
                'message' => 'Cart is empty'
            ]);
        }

        // Check if any product in cart has this promo code
        $validProduct = null;
        $totalDiscount = 0;
        
        foreach ($cart as $item) {
            $product = Product::find($item['id']);
            if ($product && $product->promo_code === $promoCode) {
                $validProduct = $product;
                
                // Calculate discount for this product
                $itemTotal = $item['price'] * $item['quantity'];
                
                if ($product->discount_type === 'percentage') {
                    $itemDiscount = $itemTotal * ($product->discount_value / 100);
                } else {
                    // Fixed amount discount
                    $itemDiscount = $product->discount_value * $item['quantity'];
                }
                
                $totalDiscount += $itemDiscount;
            }
        }

        if ($validProduct) {
            return response()->json([
                'valid' => true,
                'message' => 'Promo code applied successfully!',
                'discount' => $totalDiscount,
                'discount_type' => $validProduct->discount_type,
                'discount_value' => $validProduct->discount_value
            ]);
        }

        return response()->json([
            'valid' => false,
            'message' => 'Invalid promo code or not applicable to items in cart'
        ]);
    }
}
