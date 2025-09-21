{{-- resources/views/partials/cart-floating-button.blade.php --}}
<a href="{{ route('shop.cart') }}" class="btn btn-warning position-fixed rounded-circle shadow"
   style="bottom: 20px; right: 20px; z-index: 1050;">
    <i class="bi bi-cart"></i>
    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" id="cart-count">
        {{ count(session('cart', [])) }}
    </span>
</a>
