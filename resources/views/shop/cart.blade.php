@extends('adminlte::page')
@section('title', 'Your Cart')

@section('content')
<div class="container-fluid px-2"><br>
    <h5 class="mb-3"><i class="bi bi-cart me-1"></i> Your Shopping Cart</h5>

    @if(count($cart) > 0)
        <!-- Mobile-Optimized Cart Items -->
        <div class="cart-items-container">
            @php
                $total = 0;
                $totalCashback = 0;
            @endphp
            @foreach($cart as $id => $item)
                @php
                    $subtotal = $item['price'] * $item['quantity'];
                    $total += $subtotal;
                    $totalCashback += ($item['cashback'] ?? 0) * $item['quantity'];
                @endphp
                <div class="cart-item-card mb-3">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body p-3">
                            <div class="row align-items-center">
                                <!-- Product Image & Info -->
                                <div class="col-4 col-md-3">
                                    @if(!empty($item['thumbnail']))
                                        <img src="{{ asset('storage/' . $item['thumbnail']) }}"
                                             alt="{{ $item['name'] }}"
                                             class="cart-product-image">
                                    @else
                                        <div class="cart-product-placeholder">
                                            <i class="bi bi-image"></i>
                                        </div>
                                    @endif
                                </div>
                                
                                <!-- Product Details -->
                                <div class="col-8 col-md-9">
                                    <div class="d-flex flex-column h-100">
                                        <!-- Product Name & Remove Button -->
                                        <div class="d-flex justify-content-between align-items-start mb-2">
                                            <div class="flex-grow-1">
                                                <h6 class="cart-product-name mb-1">{{ $item['name'] }}</h6>
                                                <small class="text-success">
                                                    <i class="bi bi-cash-coin"></i> Cashback: ₱{{ number_format($item['cashback'] ?? 0, 2) }}
                                                </small>
                                            </div>
                                            <form action="{{ route('shop.cart.remove', $id) }}" method="POST" class="ms-2">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger rounded-circle p-1"
                                                        style="width: 32px; height: 32px;">
                                                    <i class="bi bi-trash" style="font-size: 0.8rem;"></i>
                                                </button>
                                            </form>
                                        </div>
                                        
                                        <!-- Price & Quantity Controls -->
                                        <div class="d-flex justify-content-between align-items-center">
                                            <!-- Quantity Controls -->
                                            <div class="quantity-controls">
                                                <form action="{{ route('shop.cart.update', $id) }}" method="POST" class="d-inline-flex align-items-center">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button name="action" value="decrease" class="btn btn-sm btn-outline-secondary rounded-circle"
                                                            style="width: 32px; height: 32px; font-size: 0.9rem;">−</button>
                                                    <span class="mx-3 fw-bold quantity-display">{{ $item['quantity'] }}</span>
                                                    <button name="action" value="increase" class="btn btn-sm btn-outline-secondary rounded-circle"
                                                            style="width: 32px; height: 32px; font-size: 0.9rem;">+</button>
                                                </form>
                                            </div>
                                            
                                            <!-- Price Info -->
                                            <div class="text-end">
                                                <div class="cart-unit-price">₱{{ number_format($item['price'], 2) }} each</div>
                                                <div class="cart-subtotal">₱{{ number_format($subtotal, 2) }}</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Mobile-Optimized Cart Summary -->
        <div class="cart-summary">
            <div class="summary-row">
                <span><i class="bi bi-calculator me-2"></i>Subtotal:</span>
                <span class="fw-bold">₱{{ number_format($total, 2) }}</span>
            </div>
            <div class="summary-row cashback">
                <span><i class="bi bi-cash-coin me-2"></i>Total Cashback:</span>
                <span class="fw-bold">₱{{ number_format($totalCashback, 2) }}</span>
            </div>
            <div class="summary-row wallet">
                <span><i class="bi bi-wallet2 me-2"></i>Wallet Balance:</span>
                <span class="fw-bold text-success">₱{{ number_format(auth()->user()->member->wallet_balance ?? 0, 2) }}</span>
            </div>
            <div class="summary-row">
                <span class="text-primary"><i class="bi bi-tag me-2"></i>Total Amount:</span>
                <span class="fw-bold text-primary fs-5">₱{{ number_format($total, 2) }}</span>
            </div>
        </div>

        <!-- Mobile-Optimized Action Buttons -->
        <div class="cart-actions">
            <a href="{{ route('shop.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-2"></i>Continue Shopping
            </a>
            <a href="{{ route('shop.checkout.page') }}" class="btn btn-primary">
                <i class="bi bi-credit-card me-2"></i>Proceed to Checkout
            </a>
        </div>

        <!-- Modal Summary -->
        <div class="modal fade" id="checkoutModal" tabindex="-1" aria-labelledby="checkoutModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-scrollable">
                <div class="modal-content rounded-4">
                    <div class="modal-header">
                        <h5 class="modal-title">🧾 Order Summary</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        @foreach($cart as $item)
                            <div class="d-flex justify-content-between mb-2">
                                <div>
                                    <strong>{{ $item['name'] }}</strong>
                                    <br><small>{{ $item['quantity'] }} × ₱{{ number_format($item['price'], 2) }}</small>
                                </div>
                                <span>₱{{ number_format($item['price'] * $item['quantity'], 2) }}</span>
                            </div>
                        @endforeach
                        <hr>
                        <div class="d-flex justify-content-between fw-bold">
                            <span>Total</span>
                            <span>₱{{ number_format($total, 2) }}</span>
                        </div>
                        <div class="d-flex justify-content-between text-muted">
                            <span>Cashback</span>
                            <span>₱{{ number_format($totalCashback, 2) }}</span>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span>Wallet Balance</span>
                            <span class="text-success">₱{{ number_format(auth()->user()->member->wallet_balance ?? 0, 2) }}</span>
                        </div>
                    </div>
                    <div class="modal-footer">
    <form action="{{ route('shop.checkout') }}" method="POST" enctype="multipart/form-data" class="w-100">
        @csrf

        <div class="mb-3">
            <label for="payment_method" class="form-label">Payment Method</label>
            <select class="form-select" name="payment_method" id="payment_method" required>
                <option value="Wallet">Wallet</option>
                <option value="GCash">GCash</option>
                <option value="Bank">Bank Transfer</option>
                <option value="COD">Cash on Delivery</option>
            </select>
        </div>

        <div class="mb-3">
            <label for="reference_image" class="form-label">Payment Proof (Optional)</label>
            <input type="file" name="reference_image" class="form-control">
        </div>

        <div class="mb-3">
            <label for="delivery_type" class="form-label">Delivery Type</label>
            <select name="delivery_type" class="form-select" required>
                <option value="delivery">Delivery</option>
                <option value="pickup">Pickup</option>
            </select>
        </div>

        <div class="mb-3">
            <label for="delivery_address" class="form-label">Delivery Address</label>
            <textarea name="delivery_address" class="form-control" rows="2" required>{{ auth()->user()->member->address ?? '' }}</textarea>
        </div>

        <div class="mb-3">
            <label for="contact_number" class="form-label">Contact Number</label>
            <input type="text" name="contact_number" class="form-control" value="{{ auth()->user()->member->mobile ?? '' }}" required>
        </div>

        <button type="submit" class="btn btn-success w-100 rounded-pill">✅ Confirm and Pay</button>
    </form>
</div>

                </div>
            </div>
        </div>

    @else
        <!-- Mobile-Optimized Empty Cart State -->
        <div class="empty-cart">
            <i class="bi bi-cart-x"></i>
            <h5>Your cart is empty</h5>
            <p class="text-muted">Looks like you haven't added any items to your cart yet.</p>
            <a href="{{ route('shop.index') }}" class="btn btn-primary text-white  rounded-pill px-4">
                <i class="bi bi-bag me-2 text-white"></i><br>Start Shopping
            </a>
        </div>
    @endif
</div>
@endsection
@include('partials.mobile-footer')

{{-- Mobile Cart Styling --}}
<style>
/* Cart Items Container */
.cart-items-container {
    max-width: 100%;
    overflow-x: hidden;
}

/* Cart Item Card */
.cart-item-card {
    transition: all 0.3s ease;
}

.cart-item-card:hover {
    transform: translateY(-2px);
}

.cart-item-card .card {
    border-radius: 15px !important;
    border: 1px solid rgba(111, 66, 193, 0.1) !important;
    background: white !important;
}

/* Product Image */
.cart-product-image {
    width: 100%;
    height: 80px;
    object-fit: cover;
    border-radius: 10px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

.cart-product-placeholder {
    width: 100%;
    height: 80px;
    background: linear-gradient(135deg, var(--light-bg) 0%, var(--primary-blue) 100%);
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 1.5rem;
}

/* Product Name */
.cart-product-name {
    font-weight: 600 !important;
    color: var(--primary-blue) !important;
    font-size: 0.95rem !important;
    line-height: 1.3 !important;
    margin-bottom: 0.25rem !important;
}

/* Quantity Controls */
.quantity-controls {
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.quantity-display {
    font-size: 1.1rem;
    color: var(--primary-blue);
    min-width: 30px;
    text-align: center;
}

/* Price Display */
.cart-unit-price {
    font-size: 0.8rem;
    color: #6c757d;
    margin-bottom: 0.25rem;
}

.cart-subtotal {
    font-weight: 700;
    color: var(--primary-blue);
    font-size: 1.1rem;
}

/* Mobile Responsive Adjustments */
@media (max-width: 576px) {
    .cart-product-image,
    .cart-product-placeholder {
        height: 70px;
    }
    
    .cart-product-name {
        font-size: 0.9rem !important;
    }
    
    .quantity-display {
        font-size: 1rem;
        min-width: 25px;
    }
    
    .cart-subtotal {
        font-size: 1rem;
    }
    
    .cart-unit-price {
        font-size: 0.75rem;
    }
    
    /* Ensure buttons are touch-friendly */
    .quantity-controls .btn {
        min-width: 32px !important;
        min-height: 32px !important;
    }
}

@media (max-width: 480px) {
    .container-fluid {
        padding-left: 0.75rem !important;
        padding-right: 0.75rem !important;
    }
    
    .cart-item-card .card-body {
        padding: 0.75rem !important;
    }
    
    .cart-product-image,
    .cart-product-placeholder {
        height: 60px;
    }
    
    .cart-product-name {
        font-size: 0.85rem !important;
    }
}

/* Summary Section Mobile Optimization */
.cart-summary {
    background: white;
    border-radius: 15px;
    padding: 1.5rem;
    box-shadow: 0 4px 15px rgba(111, 66, 193, 0.1);
    border: 1px solid rgba(111, 66, 193, 0.1);
    margin-top: 1rem;
}

.cart-summary .summary-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 0.5rem 0;
    border-bottom: 1px solid rgba(111, 66, 193, 0.1);
}

.cart-summary .summary-row:last-child {
    border-bottom: none;
    font-weight: 700;
    font-size: 1.1rem;
    color: var(--primary-purple);
    padding-top: 1rem;
    margin-top: 0.5rem;
    border-top: 2px solid rgba(111, 66, 193, 0.2);
}

.cart-summary .summary-row.cashback {
    color: #28a745;
}

.cart-summary .summary-row.wallet {
    color: var(--primary-purple);
}

/* Action Buttons Mobile */
.cart-actions {
    display: flex;
    flex-direction: column;
    gap: 0.75rem;
    margin-top: 1.5rem;
}

@media (min-width: 576px) {
    .cart-actions {
        flex-direction: row;
        justify-content: space-between;
    }
}

.cart-actions .btn {
    flex: 1;
    padding: 0.75rem 1.5rem;
    font-weight: 600;
    border-radius: 25px;
    transition: all 0.3s ease;
}

.cart-actions .btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(0,0,0,0.2);
}

/* Empty Cart State */
.empty-cart {
    text-align: center;
    padding: 3rem 1rem;
    background: white;
    border-radius: 15px;
    box-shadow: 0 4px 15px rgba(111, 66, 193, 0.1);
    border: 1px solid rgba(111, 66, 193, 0.1);
}

.empty-cart i {
    font-size: 4rem;
    color: var(--primary-purple);
    margin-bottom: 1rem;
    opacity: 0.7;
}

.empty-cart h5 {
    color: var(--primary-purple);
    margin-bottom: 1rem;
    font-weight: 600;
}

.empty-cart p {
    color: #6c757d;
    margin-bottom: 2rem;
    font-size: 0.95rem;
}

.empty-cart .btn {
    padding: 0.75rem 2rem;
    font-weight: 600;
}

/* Mobile specific adjustments for summary */
@media (max-width: 576px) {
    .cart-summary {
        padding: 1rem;
        margin-top: 0.75rem;
    }
    
    .cart-summary .summary-row {
        padding: 0.4rem 0;
        font-size: 0.9rem;
    }
    
    .cart-summary .summary-row:last-child {
        font-size: 1rem;
        padding-top: 0.75rem;
        margin-top: 0.25rem;
    }
    
    .empty-cart {
        padding: 2rem 1rem;
    }
    
    .empty-cart i {
        font-size: 3rem;
    }
}
</style>

@push('js')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const paymentMethod = document.getElementById('payment_method');
        const proofGroup = document.querySelector('input[name="reference_image"]').closest('.mb-3');

        function toggleProof() {
            const value = paymentMethod.value;
            if (value === 'Wallet' || value === 'COD') {
                proofGroup.style.display = 'none';
            } else {
                proofGroup.style.display = 'block';
            }
        }

        paymentMethod.addEventListener('change', toggleProof);
        toggleProof(); // run on page load
    });
</script>
@endpush
