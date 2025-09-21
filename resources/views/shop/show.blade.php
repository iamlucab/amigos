@extends('adminlte::page')
@section('title', $product->name)

@push('css')
<style>
    .gallery-thumb.active {
        border: 2px solid #2d5cda;
    }
    
    /* Uniform button sizing for mobile */
    @media (max-width: 767px) {
        .product-action-btn {
            padding: 12px 8px;
            font-size: 0.85rem;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        
        .product-action-btn .btn-text {
            display: block;
            font-size: 0.75rem;
        }
        
        .product-action-btn .btn-icon {
            font-size: 1rem;
        }
    }
    
    /* Ensure buttons have uniform height */
    .btn-uniform {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        height: 50px;
        padding: 5px;
    }
    
    .btn-uniform .btn-icon {
        font-size: 1.2rem;
        line-height: 1;
    }
    
    .btn-uniform .btn-text {
        font-size: 0.7rem;
        line-height: 1.2;
        margin-top: 2px;
    }
</style>
@endpush

@section('content')
<div class="container-fluid px-2">
    <div class="row justify-content-center">
        <div class="col-12 col-md-10 col-lg-10">
            <div class="card shadow-sm border-0 rounded-4">
                <div class="row g-0 flex-column flex-md-row">

                    {{-- Left: Main Image --}}
                    <div class="col-md-6">
                        <div class="position-relative h-100">
                            <img id="mainImage"
                                 src="{{ asset('storage/' . $product->thumbnail) }}"
                                 alt="{{ $product->name }}"
                                 class="img-fluid w-100 h-100 rounded-top-4 rounded-md-start-4"
                                 style="object-fit: cover; cursor: zoom-in;"
                                 data-bs-toggle="modal" data-bs-target="#imageModal">
                        </div>
                    </div>

                    {{-- Right: Product Info --}}
                    <div class="col-md-6">
                        <div class="card-body">
                            <h4 class="fw-bold mb-2">{{ $product->name }}</h4>
                            <p class="text-muted">{!! nl2br(e($product->description)) !!}</p>

                            <div class="mb-3">
                                {{-- Price Display with Discount --}}
                                @if($product->hasDiscount())
                                    <div class="price-section mb-3">
                                        <div class="d-flex align-items-center gap-2 mb-2">
                                            <span class="badge bg-danger fs-5 rounded-pill">
                                                <span class="text-decoration-line-through">₱{{ number_format($product->price, 2) }}</span>
                                            </span>
                                            <span class="badge bg-success fs-3 rounded-pill">
                                                ₱{{ number_format($product->getDiscountedPrice(), 2) }}
                                            </span>
                                        </div>
                                        <div class="mb-2">
                                            <span class="badge bg-warning text-dark fs-6 rounded-pill">
                                                🏷️ {{ $product->getDiscountPercentage() }}% OFF - You Save ₱{{ number_format($product->getDiscountAmount(), 2) }}
                                            </span>
                                        </div>
                                        @if($product->promo_code)
                                            <div class="mb-2">
                                                <span class="badge bg-primary fs-6 rounded-pill">
                                                    🎫 Promo Code: {{ $product->promo_code }}
                                                </span>
                                            </div>
                                        @endif
                                    </div>
                                @else
                                    <span class="badge bg-success fs-3 rounded-pill">₱{{ number_format($product->price, 2) }}</span>
                                @endif
                                
                                <span class="badge bg-info text-dark fs-6 rounded-pill">
                                    Cashback: ₱{{ number_format($product->cashback_amount, 2) }}
                                </span>
                            </div>
                            
                            <div class="mb-3">
                                <small class="text-muted d-block">
                                    Cashback distributed from Level 1 to Level {{ $product->cashback_max_level }}
                                </small>
                            </div>

                            @if(!empty($product->attributes))
                                <div class="mb-2">
                                    <strong>Available Size:</strong>
                                    <span class="badge bg-secondary rounded-pill">{{ $product->attributes }}</span>
                                </div>
                            @endif

                            {{-- Gallery --}}
                            @php
                                // Ensure gallery is properly handled
                                $gallery = $product->gallery ?? [];
                                // Make sure it's an array
                                if (!is_array($gallery)) {
                                    $gallery = [];
                                }
                                // Add thumbnail to the beginning of gallery if it exists
                                if ($product->thumbnail) {
                                    array_unshift($gallery, $product->thumbnail);
                                }
                            @endphp

                            @if(count($gallery) > 1)
                                <div class="mb-3">
                                    <label class="fw-bold">Product gallery</label>
                                    <div class="d-flex flex-wrap gap-2">
                                        @foreach ($gallery as $image)
                                            @if($image)
                                                <img src="{{ asset('storage/' . $image) }}"
                                                     data-full="{{ asset('storage/' . $image) }}"
                                                     class="img-thumbnail gallery-thumb"
                                                     style="height: 70px; width: 70px; object-fit: cover; cursor: pointer; border-radius: 50%; box-shadow: 0 0 5px rgba(0,0,0,0.15);"
                                                     alt="Product gallery image">
                                            @endif
                                        @endforeach
                                    </div>
                                </div>
                            @endif

                            {{-- Add to Cart --}}
                            <form id="add-to-cart-form" data-url="{{ route('shop.order', $product) }}">
                                @csrf
                                <input type="hidden" id="csrf-token" value="{{ csrf_token() }}">

                                {{-- Quantity --}}
                                <div class="mb-3">
                                    <label for="quantity" class="form-label">Quantity</label>
                                    <div class="input-group rounded-pill overflow-hidden shadow-sm" style="max-width: 200px;">
                                        <button type="button" class="btn btn-light border text-success" id="decreaseQty"><i class="bi bi-dash"></i></button>
                                        <input type="number" id="quantity" name="quantity" class="form-control text-center border-0" value="1" min="1" required>
                                        <button type="button" class="btn btn-light border text-success" id="increaseQty"><i class="bi bi-plus"></i></button>
                                    </div>
                                </div>

                                {{-- Subtotal --}}
                                <div class="mb-3">
                                    <span class="fw-bold">Subtotal:</span>
                                    <span id="subtotal">₱{{ number_format($product->hasDiscount() ? $product->getDiscountedPrice() : $product->price, 2) }}</span>
                                </div>

                                {{-- Button Row --}}
                                <div class="row g-2 mt-4">
                                    <div class="col-4">
                                        <button type="submit" class="btn btn-success w-100 rounded-pill btn-uniform">
                                            <i class="bi bi-cart-plus btn-icon"></i>
                                            <span class="btn-text">Add to Cart</span>
                                        </button>
                                    </div>
                                    <div class="col-4">
                                        <a href="{{ route('shop.cart') }}" class="btn btn-warning w-100 rounded-pill btn-uniform">
                                            <i class="bi bi-cart btn-icon"></i>
                                            <span class="btn-text">My Cart</span>
                                        </a>
                                    </div>
                                    <div class="col-4">
                                        <a href="{{ route('shop.index') }}" class="btn btn-secondary w-100 rounded-pill btn-uniform">
                                            <i class="bi bi-arrow-left btn-icon"></i>
                                            <span class="btn-text">Back</span>
                                        </a>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Floating Cart Button --}}
    @include('partials.cart-floating-button')

    {{-- Zoom Modal --}}
    <div class="modal fade" id="imageModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-fullscreen-sm-down modal-lg">
            <div class="modal-content bg-transparent border-0">
                <div class="modal-body text-center p-0">
                    <img id="modalImage" src="" class="img-fluid rounded-4" style="max-height: 80vh; object-fit: contain;">
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@include('partials.mobile-footer')

@section('js')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('add-to-cart-form');
    const qtyInput = document.getElementById('quantity');
    const increaseBtn = document.getElementById('increaseQty');
    const decreaseBtn = document.getElementById('decreaseQty');
    const subtotalEl = document.getElementById('subtotal');
    const unitPrice = {{ $product->hasDiscount() ? $product->getDiscountedPrice() : $product->price }};
    const csrfToken = document.getElementById('csrf-token').value;

    function updateSubtotal() {
        const qty = parseInt(qtyInput.value) || 1;
        subtotalEl.textContent = '₱' + (unitPrice * qty).toFixed(2);
    }

    increaseBtn.addEventListener('click', () => {
        qtyInput.value = parseInt(qtyInput.value || 1) + 1;
        updateSubtotal();
    });

    decreaseBtn.addEventListener('click', () => {
        const current = parseInt(qtyInput.value || 1);
        if (current > 1) {
            qtyInput.value = current - 1;
            updateSubtotal();
        }
    });

    qtyInput.addEventListener('input', updateSubtotal);
    updateSubtotal();

    form.addEventListener('submit', function (e) {
        e.preventDefault();
        const quantity = qtyInput.value;

        fetch(form.dataset.url, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ quantity: quantity })
        }).then(response => {
            if (response.ok) {
                Swal.fire({
                    icon: 'success',
                    title: 'Added to Cart!',
                    text: 'Product successfully added.',
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 2500,
                    timerProgressBar: true
                });

                const cartCountEl = document.getElementById('cart-count');
                if (cartCountEl) {
                    let count = parseInt(cartCountEl.textContent || '0');
                    cartCountEl.textContent = count + parseInt(quantity);
                }
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Failed!',
                    text: 'Could not add product to cart.',
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 2500
                });
            }
        }).catch(error => {
            console.error(error);
            Swal.fire({
                icon: 'error',
                title: 'Error!',
                text: 'Something went wrong.',
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 2500
            });
        });
    });

    // Gallery thumbnail switching
    document.querySelectorAll('.gallery-thumb').forEach(img => {
        img.addEventListener('click', () => {
            document.getElementById('mainImage').src = img.dataset.full;
            document.querySelectorAll('.gallery-thumb').forEach(i => i.classList.remove('active'));
            img.classList.add('active');
        });
    });

    // Zoom modal sync
    document.getElementById('imageModal').addEventListener('show.bs.modal', function () {
        document.getElementById('modalImage').src = document.getElementById('mainImage').src;
    });
});
</script>
@endsection
