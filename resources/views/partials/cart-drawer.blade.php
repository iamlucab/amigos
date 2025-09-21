{{-- Cart Drawer --}}
<div class="offcanvas offcanvas-end" tabindex="-1" id="cartDrawer" aria-labelledby="cartDrawerLabel">
    <div class="offcanvas-header">
        <h5 class="offcanvas-title" id="cartDrawerLabel"><i class="bi bi-cart me-2"></i>Your Cart</h5>
        <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body p-3">
        @php $cart = session('cart', []); @endphp

        @if(count($cart) > 0)
            <ul class="list-group mb-3">
                @foreach($cart as $id => $item)
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <div class="d-flex align-items-center">
                            <img src="{{ asset('storage/' . $item['thumbnail']) }}" alt="thumb"
                                 class="me-2 rounded" style="width: 50px; height: 50px; object-fit: cover;">
                            <div>
                                <strong>{{ $item['name'] }}</strong><br>
                                <small>Qty: {{ $item['quantity'] }}</small>
                            </div>
                        </div>
                        <span>₱{{ number_format($item['price'] * $item['quantity'], 2) }}</span>
                    </li>
                @endforeach
            </ul>

            <div class="text-end mb-3">
                <strong>Total:</strong> ₱{{ number_format(collect($cart)->sum(fn($item) => $item['price'] * $item['quantity']), 2) }}
            </div>

            <a href="{{ route('shop.cart') }}" class="btn btn-success w-100 rounded-pill">
                🧾 View Full Cart / Checkout
            </a>
        @else
            <p class="text-center text-muted">Your cart is empty.</p>
        @endif
    </div>
</div>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Update cart count in the floating button
        const cartCount = document.getElementById('cart-count');
        const cartItems = @json(session('cart', []));
        cartCount.textContent = Object.keys(cartItems).length;

        // Show cart drawer when floating button is clicked
        document.querySelector('.btn-warning').addEventListener('click', function() {
            const cartDrawer = new bootstrap.Offcanvas(document.getElementById('cartDrawer'));
            cartDrawer.show();
        });
    });

    if (window.location.hash === '#cartDrawer') {
    const cartDrawer = document.getElementById('cartDrawer');
    if (cartDrawer) {
        const bsOffcanvas = new bootstrap.Offcanvas(cartDrawer);
        bsOffcanvas.show();
    }
}
</script>