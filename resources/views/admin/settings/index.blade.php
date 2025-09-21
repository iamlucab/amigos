@extends('adminlte::page')
@section('title', 'Site Settings')

@section('content')
<div class="container-fluid"><br>
    <h5 class="mb-4">🛠️ Site Settings</h5>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <form method="POST" action="{{ route('admin.settings.update') }}">
        @csrf
        @method('PUT')

        {{-- Shipping Fee --}}
        <div class="mb-4">
            <label for="shipping_fee" class="form-label fw-bold">🚚 Shipping Fee (₱)</label>
            <input type="number" name="shipping_fee" id="shipping_fee" class="form-control rounded-pill shadow-sm"
                   value="{{ old('shipping_fee', $settings['shipping_fee'] ?? 0) }}" step="0.01" min="0" required>
        </div>

        {{-- Future Promo Code Section --}}
        <div class="mb-4">
            <label for="promo_note" class="form-label fw-bold">🎁 Promo Info</label>
            <input type="text" name="promo_note" id="promo_note" class="form-control rounded-pill"
                   placeholder="e.g., 10% OFF for July" value="{{ old('promo_note', $settings['promo_note'] ?? '') }}">
        </div>

        {{-- Future Discount Placeholder --}}
        <div class="mb-4">
            <label for="discount_rate" class="form-label fw-bold">💸 Global Discount (%)</label>
            <input type="number" name="discount_rate" id="discount_rate" class="form-control rounded-pill"
                   placeholder="e.g., 5" value="{{ old('discount_rate', $settings['discount_rate'] ?? '') }}" min="0" max="100">
        </div>

        {{-- Wallet Transfer Fee --}}
        <div class="mb-4">
            <label for="wallet_transfer_fee" class="form-label fw-bold">💳 Wallet Transfer Fee (%)</label>
            <input type="number" name="wallet_transfer_fee" id="wallet_transfer_fee" class="form-control rounded-pill shadow-sm"
                   value="{{ old('wallet_transfer_fee', $settings['wallet_transfer_fee'] ?? 10) }}" step="0.01" min="0" max="100" required>
            <small class="form-text text-muted">Fee percentage for transferring from Cashback Wallet to Main Wallet</small>
        </div>

        {{-- Membership Code Price --}}
        <div class="mb-4">
            <label for="membership_code_price" class="form-label fw-bold">🏷️ Membership Code Price (₱)</label>
            <input type="number" name="membership_code_price" id="membership_code_price" class="form-control rounded-pill shadow-sm"
                   value="{{ old('membership_code_price', $settings['membership_code_price'] ?? 100) }}" step="0.01" min="0" required>
            <small class="form-text text-muted">Price per membership code for members to purchase</small>
        </div>

        <button type="submit" class="btn btn-success rounded-pill px-4">
            💾 Save Settings
        </button>
    </form>
</div>

{{-- 📱 Reusable Mobile Footer --}}
@include('partials.mobile-footer')

@endsection
