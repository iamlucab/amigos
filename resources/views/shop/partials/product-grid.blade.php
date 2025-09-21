@foreach($products as $product)
    <div class="col-6 col-sm-4 col-md-3 col-lg-2">
        <div class="product-card fade-in" data-category-id="{{ $product->category_id }}">
            {{-- Thumbnail --}}
            @if($product->thumbnail)
                <a data-fancybox="thumb-{{ $product->id }}" href="{{ asset('storage/' . $product->thumbnail) }}">
                    <img src="{{ asset('storage/' . $product->thumbnail) }}"
                         class="product-image"
                         alt="{{ $product->name }}">
                </a>
            @else
                <div class="product-placeholder">
                    <div>
                        <i class="bi bi-box-seam fs-1 mb-2"></i>
                        <div>{{ $product->name }}</div>
                    </div>
                </div>
            @endif

            <div class="p-3">
                <a href="{{ route('shop.show', $product) }}"> <h6 class="fw-bold mb-2 text-truncate" title="{{ $product->name }}">{{ $product->name }}</h6>

                <p class="text-muted small mb-2 text-truncate" title="{{ $product->description }}">
                    {{ Str::limit($product->description, 50) }}
                </p>
</a>
                <div class="mt-auto">
                    {{-- Price Display with Discount --}}
                    @if($product->hasDiscount())
                        <div class="d-flex align-items-center gap-2 mb-2">
                            <span class="price-tag">₱{{ number_format($product->getDiscountedPrice(), 2) }}</span>
                            <small class="text-muted text-decoration-line-through">₱{{ number_format($product->price, 2) }}</small>
                            <span class="badge bg-danger">-{{ $product->getDiscountPercentage() }}%</span>
                        </div>
                    @else
                        <div class="price-tag mb-2">₱{{ number_format($product->price, 2) }}</div>
                    @endif
                    
                    <span class="badge bg-info text-dark rounded-pill mb-2">
                        <i class="bi bi-coin me-1"></i>Cashback: ₱{{ number_format($product->cashback_amount, 2) }}
                    </span>
                    <small class="text-muted d-block mb-2">Level 1 to Level {{ $product->cashback_max_level }}</small>

                    <a href="{{ route('shop.show', $product) }}"
                       class="btn btn-sm btn-primary w-100"
                       title="View Product">
                        <i class="bi bi-eye me-1"></i> View Item
                    </a>
                </div>
            </div>
        </div>
    </div>
@endforeach