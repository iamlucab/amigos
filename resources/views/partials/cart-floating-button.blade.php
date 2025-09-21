
<!-- Floating Cart Button that triggers the drawer -->
<a class="btn btn-warning position-fixed rounded-circle shadow"
   data-bs-toggle="offcanvas"
   href="{{ route('shop.cart') }}"
   role="button"
   aria-controls="cartDrawer"
   style="bottom: 20px; right: 20px; z-index: 1050;">
    <i class="bi bi-cart"></i>
    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" id="cart-count">
        {{ count(session('cart', [])) }}
    </span>
</a> 

<!-- Offcanvas Drawer for Cart -->  