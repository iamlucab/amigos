<!-- 🛒 Cart Modal (Scrollable with Editable Quantity) -->
<div class="modal fade" id="cartModal" tabindex="-1" aria-labelledby="cartModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content rounded-4">
            <div class="modal-header border-0">
                <h5 class="modal-title fw-bold" id="cartModalLabel">🛒 Your Cart</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body" style="max-height: 65vh; overflow-y: auto;">
                @php $cart = session('cart', []); @endphp

                @if(count($cart) > 0)
                    @foreach($cart as $id => $item)
                        <div class="d-flex align-items-center justify-content-between border-bottom pb-3 mb-3">
                            <div class="d-flex align-items-center gap-3">
                                @if(!empty($item['thumbnail']))
                                    <img src="{{ asset('storage/' . $item['thumbnail']) }}" class="rounded" style="width: 50px; height: 50px; object-fit: cover;">
                                @endif
                                <div>
                                    <div class="fw-semibold">{{ $item['name'] }}</div>
                                    <small class="text-success">Cashback: ₱{{ number_format($item['cashback'], 2) }}</small>

                                    <!-- Quantity input -->
                                    <div class="input-group input-group-sm mt-1" style="max-width: 120px;">
                                        <button class="btn btn-outline-secondary btn-sm qty-decrease" data-id="{{ $id }}">−</button>
                                        <input type="number" class="form-control text-center qty-input" data-id="{{ $id }}" min="1" value="{{ $item['quantity'] }}">
                                        <button class="btn btn-outline-secondary btn-sm qty-increase" data-id="{{ $id }}">+</button>
                                    </div>
                                    <div><small>₱<span class="line-subtotal" data-id="{{ $id }}">{{ number_format($item['price'] * $item['quantity'], 2) }}</span></small></div>
                                </div>
                            </div>

                            <!-- Remove -->
                            <form action="{{ route('shop.cart.remove', $id) }}" method="POST" onsubmit="return confirm('Remove item?')">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                            </form>
                        </div>
                    @endforeach

                    <div class="text-end fw-bold fs-5">
                        Total: ₱<span id="modal-cart-total">{{ number_format(collect($cart)->sum(fn($item) => $item['price'] * $item['quantity']), 2) }}</span>
                    </div>

                    <form action="{{ route('shop.checkout') }}" method="POST" class="mt-3">
                        @csrf
                        <button class="btn btn-success w-100 rounded-pill">Proceed to Checkout</button>
                    </form>
                @else
                    <p class="text-muted text-center">🛒 Your cart is empty.</p>
                @endif
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    function updateCartUI(id, newQty) {
        const price = parseFloat(document.querySelector(`.qty-input[data-id='${id}']`).dataset.price);
        document.querySelector(`.line-subtotal[data-id='${id}']`).textContent = (price * newQty).toFixed(2);

        let total = 0;
        document.querySelectorAll('.qty-input').forEach(input => {
            const qty = parseInt(input.value);
            const p = parseFloat(input.dataset.price);
            total += qty * p;
        });
        document.getElementById('modal-cart-total').textContent = total.toFixed(2);
    }

    document.querySelectorAll('.qty-increase').forEach(btn => {
        btn.addEventListener('click', function () {
            const id = this.dataset.id;
            const input = document.querySelector(`.qty-input[data-id='${id}']`);
            input.value = parseInt(input.value) + 1;
            updateCartUI(id, parseInt(input.value));
        });
    });

    document.querySelectorAll('.qty-decrease').forEach(btn => {
        btn.addEventListener('click', function () {
            const id = this.dataset.id;
            const input = document.querySelector(`.qty-input[data-id='${id}']`);
            if (parseInt(input.value) > 1) {
                input.value = parseInt(input.value) - 1;
                updateCartUI(id, parseInt(input.value));
            }
        });
    });

    document.querySelectorAll('.qty-input').forEach(input => {
        input.addEventListener('input', function () {
            const id = this.dataset.id;
            const val = parseInt(this.value);
            if (val >= 1) {
                updateCartUI(id, val);
            }
        });
    });
});
</script>
@endpush
