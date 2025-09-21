@extends('adminlte::page')
@section('title', 'Checkout')

@section('adminlte_css_pre')
<meta name="csrf-token" content="{{ csrf_token() }}">
@endsection

<style>
    .input-uniform {
        height: 50px;
        padding: 10px 16px;
    }
</style>

@section('content')
<div class="container-fluid px-2"><br>
    <h5 class="mb-3"><i class="bi bi-receipt me-1"></i> Order Summary</h5>

    {{-- Order Summary Card --}}
    <div class="card rounded-4 shadow-sm p-3 mb-4">
        @foreach($cart as $item)
            <div class="d-flex justify-content-between mb-2">
                <div>
                    <strong>{{ $item['name'] }}</strong><br>
                    <small>{{ $item['quantity'] }} × ₱{{ number_format($item['price'], 2) }}</small>
                </div>
                <span>₱{{ number_format($item['price'] * $item['quantity'], 2) }}</span>
            </div>
        @endforeach
        <hr>
        <div class="d-flex justify-content-between fw-bold">
            <span>Sub-Total</span>
            <span>₱{{ number_format($total, 2) }}</span>
        </div>

        <div id="shippingRow" class="d-flex justify-content-between text-muted">
            <span>Shipping Fee</span>
            <span>₱{{ number_format($shippingFee, 2) }}</span>
        </div>

        <div class="d-flex justify-content-between text-muted">
            <strong><span>Total</span></strong>
            <strong><span>₱{{ number_format($subtotal, 2) }}</span></strong>
        </div>

        
    </div>

  @include('shop.checkout-form')

</div>
@endsection

@push('js')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const methodSelect = document.getElementById('payment_method');
        const gcash = document.getElementById('gcashSection');
        const bank = document.getElementById('bankSection');
        const proof = document.getElementById('proofGroup');
        const submitBtn = document.querySelector('button[type="submit"]');
        const deliveryType = document.querySelector('[name="delivery_type"]');
        const shippingRow = document.getElementById('shippingRow');

        const walletBalance = parseFloat({{ auth()->user()->member->wallet_balance ?? 0 }});
        const totalAmount = parseFloat({{ $total }});

        function updatePaymentUI() {
            const val = methodSelect.value;
            gcash.classList.remove('show');
            bank.classList.remove('show');
            proof.style.display = 'none';
            submitBtn.disabled = false;
            submitBtn.classList.remove('btn-danger');
            submitBtn.textContent = '✅ Confirm and Pay';

            if (val === 'GCash') {
                gcash.classList.add('show');
                proof.style.display = 'block';
            } else if (val === 'Bank') {
                bank.classList.add('show');
                proof.style.display = 'block';
            } else if (val === 'Wallet' && walletBalance < totalAmount) {
                submitBtn.disabled = true;
                submitBtn.classList.add('btn-danger');
                submitBtn.textContent = '❌ Insufficient Wallet Balance';
            }
        }

        function toggleShippingRow() {
            if (!shippingRow) return;
            const deliveryFields = document.querySelector('.delivery-fields');
            
            if (deliveryType.value === 'pickup') {
                shippingRow.classList.add('d-none');
                if (deliveryFields) {
                    deliveryFields.classList.add('d-none');
                    deliveryFields.querySelector('textarea').removeAttribute('required');
                }
            } else {
                shippingRow.classList.remove('d-none');
                if (deliveryFields) {
                    deliveryFields.classList.remove('d-none');
                    deliveryFields.querySelector('textarea').setAttribute('required', 'required');
                }
            }
        }

        methodSelect.addEventListener('change', updatePaymentUI);
        deliveryType.addEventListener('change', toggleShippingRow);

        // Promo code handling
        const promoCodeInput = document.getElementById('promo_code');
        const applyPromoBtn = document.getElementById('apply-promo');
        const promoResult = document.getElementById('promo-result');
        const discountRow = document.getElementById('discount-row');
        const discountAmount = document.getElementById('discount-amount');
        const finalTotal = document.getElementById('final-total');
        const subtotalAmount = document.getElementById('subtotal-amount');
        
        let currentDiscount = 0;
        const originalSubtotal = {{ $total }};
        const shippingFee = {{ $shippingFee }};
        
        function updateTotals() {
            const deliveryType = document.querySelector('[name="delivery_type"]').value;
            let newTotal = originalSubtotal;
            
            // Add shipping fee if delivery is selected
            if (deliveryType === 'delivery') {
                newTotal += shippingFee;
            }
            
            // Apply discount
            newTotal -= currentDiscount;
            
            // Update display
            finalTotal.textContent = '₱' + newTotal.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2});
            
            // Update payment amount fields
            const gcashAmount = document.getElementById('gcash_amount');
            const bankAmount = document.getElementById('bank_amount');
            if (gcashAmount) gcashAmount.value = newTotal.toFixed(2);
            if (bankAmount) bankAmount.value = newTotal.toFixed(2);
        }
        
        if (applyPromoBtn) {
            applyPromoBtn.addEventListener('click', function() {
                const promoCode = promoCodeInput.value.trim();
                if (!promoCode) {
                    promoResult.innerHTML = '<span class="text-danger">Please enter a promo code</span>';
                    return;
                }
                
                // Show loading state
                applyPromoBtn.disabled = true;
                applyPromoBtn.textContent = 'Validating...';
                promoResult.innerHTML = '<span class="text-info">Validating promo code...</span>';
                
                // Make AJAX call to validate promo code
                fetch('{{ route("shop.validate-promo") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({
                        promo_code: promoCode
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.valid) {
                        // Apply discount
                        currentDiscount = data.discount;
                        discountAmount.textContent = '-₱' + currentDiscount.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2});
                        discountRow.style.display = 'flex';
                        promoResult.innerHTML = '<span class="text-success">✅ ' + data.message + '</span>';
                        updateTotals();
                    } else {
                        // Reset discount
                        currentDiscount = 0;
                        discountRow.style.display = 'none';
                        promoResult.innerHTML = '<span class="text-danger">❌ ' + data.message + '</span>';
                        updateTotals();
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    promoResult.innerHTML = '<span class="text-danger">❌ Error validating promo code</span>';
                })
                .finally(() => {
                    applyPromoBtn.disabled = false;
                    applyPromoBtn.textContent = 'Apply';
                });
            });
        }
        
        // Update totals when delivery type changes
        deliveryType.addEventListener('change', updateTotals);
        
        updatePaymentUI();
        toggleShippingRow();
    });
</script>


@endpush
