<form action="{{ route('shop.checkout') }}" method="POST" enctype="multipart/form-data">
    @csrf

    {{-- 💼 Wallet & Cashback --}}
    <div class="row mb-4">
        <div class="col-md-6 mb-3">
            <div class="card shadow-sm border-0 bg-light rounded-4 p-3">
                <div class="d-flex justify-content-between align-items-center">
                    <span class="fw-semibold">💼 Wallet Balance</span>
                    <span class="fw-bold text-success">₱{{ number_format(auth()->user()->member->wallet_balance ?? 0, 2) }}</span>
                </div>
            </div>
        </div>
        <div class="col-md-6 mb-3">
            <div class="card shadow-sm border-0 bg-light rounded-4 p-3">
                <div class="d-flex justify-content-between align-items-center">
                    <span class="fw-semibold">🎁 Cashback to Earn</span>
                    <span class="fw-bold text-info">₱{{ number_format($totalCashback ?? 0, 2) }}</span>
                </div>
            </div>
        </div>
    </div>

    {{-- 💳 Payment Method --}}
    <div class="mb-4">
        <label for="payment_method" class="form-label fw-semibold">💳 Payment Method</label>
        <select class="form-select form-select-lg rounded-pill shadow-sm" name="payment_method" id="payment_method" required>
            <option disabled selected value="">Choose payment option</option>
            <option value="Wallet">💼 Wallet</option>
            <option value="GCash">📱 GCash</option>
            <option value="Bank">🏦 Bank Transfer</option>
            <option value="COD">💵 Cash on Delivery</option>
        </select>
    </div>

    {{-- 📱 GCash --}}
    <div id="gcashSection" class="collapse">
        <div class="card border-0 shadow-sm p-3 mb-4 bg-light rounded-4">
            <div class="text-center">
                <label class="fw-bold d-block mb-2">📲 Scan GCash QR Code</label>
                <img src="{{ asset('images/gcashQR.jpeg') }}" class="img-fluid rounded shadow mb-2" style="max-width: 180px;">
                <small class="text-muted d-block">GCash Account: <strong>LU*** CAB*</strong></small>
                <a href="{{ asset('images/gcashQR.jpeg') }}" download class="btn btn-sm btn-outline-primary mt-2 rounded-pill">
                    <i class="bi bi-download"></i> Download QR
                </a>
            </div>

            <div class="mt-4">
                <label class="form-label">💰 Amount Sent</label>
                <input type="number" class="form-control rounded-pill shadow-sm px-3 py-2" name="amount" value="{{ $subtotal }}" readonly>
                <small class="text-muted">Amount must match the total amount</small>

                <label class="form-label mt-3">📝 Reference / Notes</label>
                <input type="text" class="form-control rounded-pill shadow-sm px-3 py-2" name="gcash_note" placeholder="e.g., Ref number or time sent">
            </div>
        </div>
    </div>

    {{-- 🏦 Bank --}}
    <div id="bankSection" class="collapse">
        <div class="card border-0 shadow-sm p-3 mb-4 bg-light rounded-4">
            <label class="fw-bold mb-2">🏦 Bank Information</label>
            <div class="d-flex align-items-center mb-3">
                <img src="{{ asset('images/bdo-logo.png') }}" alt="BDO" class="me-3" style="width: 60px;">
                <div>
                    <div><strong>BDO</strong></div>
                    <div>Account Name: Hugpong Amigos</div>
                    <div>
                        Account No:
                        <span class="fw-semibold">0071 5801 3083</span>
                        <button type="button" class="btn btn-sm btn-outline-secondary ms-2 py-0 px-2 rounded-circle" onclick="navigator.clipboard.writeText('007158013083')" title="Copy Account #">
                            <i class="bi bi-clipboard"></i>
                        </button>
                    </div>
                </div>
            </div>

            <label class="form-label">💵 Amount</label>
            <input type="number" step="0.01" name="bank_amount" class="form-control rounded-pill shadow-sm px-3 py-2" value="{{ $subtotal }}" readonly>

            <label class="form-label mt-3">📝 Reference / Notes</label>
            <input type="text" name="bank_note" class="form-control rounded-pill shadow-sm px-3 py-2" placeholder="e.g., Transaction Ref">
        </div>
    </div>

    {{-- 📎 Upload Proof --}}
    <div class="mb-4" id="proofGroup">
        <label class="form-label fw-semibold">📌 Upload Payment Proof <small class="text-muted">(optional)</small></label>
        <input type="file" name="reference_image" class="form-control rounded-pill shadow-sm px-3 py-2" accept="image/*">
    </div>

    {{-- 🏷️ Promo Code --}}
    <div class="mb-4">
        <label class="form-label fw-semibold">🏷️ Promo Code</label>
        <div class="input-group">
            <input type="text" name="promo_code" id="promo_code" class="form-control rounded-start-pill" placeholder="Enter promo code">
            <button type="button" id="apply-promo" class="btn btn-primary rounded-end-pill">Apply</button>
        </div>
        <div id="promo-result" class="mt-2"></div>
    </div>

    {{-- 🚚 Delivery --}}
    <div class="mb-4">
        <label class="form-label fw-semibold">🚚 Delivery Type</label>
        <select name="delivery_type" class="form-select form-select-lg rounded-pill shadow-sm" required>
            <option disabled selected value="">Select delivery method</option>
            <option value="delivery">🚚 Delivery</option>
            <option value="pickup">🏃 Pickup</option>
        </select>
    </div>

    <div class="mb-3 delivery-fields">
        <label class="form-label fw-semibold">🏡 Delivery Address</label>
        <textarea name="delivery_address" class="form-control rounded-4 shadow-sm px-3 py-2" rows="2" placeholder="Enter your delivery address...">{{ auth()->user()->member->address ?? '' }}</textarea>
    </div>

    {{-- 📞 Contact --}}
    <div class="mb-4">
        <label class="form-label fw-semibold">📞 Contact Number</label>
        <input type="text" name="contact_number" class="form-control rounded-pill shadow-sm px-3 py-2" value="{{ auth()->user()->member->mobile ?? '' }}" placeholder="e.g., 09xxxxxxxxx" required>
    </div>

    {{-- 🗒️ Notes --}}
    <div class="mb-4">
        <label class="form-label fw-semibold">🗒️ Additional Notes</label>
        <textarea name="notes" class="form-control rounded-4 shadow-sm px-3 py-2" rows="2" placeholder="Any extra instructions? (optional)"></textarea>
    </div>

    {{-- 🧾 Totals --}}
    <div class="card shadow-sm border-0 bg-light rounded-4 p-3 mb-4" id="totals-card">
        <div class="d-flex justify-content-between">
            <span>Subtotal</span>
            <span id="subtotal-amount">₱{{ number_format($total, 2) }}</span>
        </div>
        <div class="d-flex justify-content-between" id="shipping-row">
            <span>Shipping Fee</span>
            <span>₱{{ number_format($shippingFee, 2) }}</span>
        </div>
        <div class="d-flex justify-content-between text-success" id="discount-row" style="display: none;">
            <span>🏷️ Promo Discount</span>
            <span id="discount-amount">-₱0.00</span>
        </div>
        <hr>
        <div class="d-flex justify-content-between">
            <strong class="text-success fs-5">🧾 Total</strong>
            <strong class="text-success fs-5" id="final-total">₱{{ number_format($subtotal, 2) }}</strong>
        </div>
    </div>

    {{-- ✅ Submit --}}
    <div class="d-flex justify-content-between gap-2">
        <a href="{{ route('shop.cart') }}" class="btn btn-outline-secondary w-50 rounded-pill">
            ← Back to Cart
        </a>
        <button type="submit" class="btn btn-success w-50 rounded-pill">
            ✅ Confirm and Pay
        </button>
    </div>
</form>
<br>
@include('partials.mobile-footer')
