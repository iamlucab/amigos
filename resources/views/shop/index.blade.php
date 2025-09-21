@extends('adminlte::page')
@section('title', 'Shop')

{{-- E-Bili Theme Styling --}}
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet" integrity="sha512-iecdLmaskl7CVkqkXNQ/ZH/XLlvWZOJyj7Yy7tcenmpD1ypASozpmT/E0iPtmFIB46ZmdtAc9eNBvH0H/ZpiBw==" crossorigin="anonymous" referrerpolicy="no-referrer">
<link rel="stylesheet" href="{{ asset('css/ebili-theme.css') }}">

<style>
    /* Category Filter Styles */
    .category-filter-container {
        background: linear-gradient(135deg, var(--primary-blue), var(--secondary-blue));
        border-radius: 15px;
        padding: 15px;
        box-shadow: 0 4px 15px rgba(0, 132, 254, 0.2);
        margin: 0 auto;
        max-width: 800px;
    }

    .category-scroll {
        display: flex;
        gap: 10px;
        overflow-x: auto;
        padding: 5px;
        scrollbar-width: thin;
        scrollbar-color: var(--primary-gold) transparent;
    }

    .category-scroll::-webkit-scrollbar {
        height: 6px;
    }

    .category-scroll::-webkit-scrollbar-track {
        background: rgba(255, 255, 255, 0.1);
        border-radius: 3px;
    }

    .category-scroll::-webkit-scrollbar-thumb {
        background: var(--accent-color);
        border-radius: 3px;
    }

    .category-item {
        display: flex;
        flex-direction: column;
        align-items: center;
        padding: 12px 16px;
        background: rgba(255, 255, 255, 0.1);
        border: 2px solid transparent;
        border-radius: 12px;
        cursor: pointer;
        transition: all 0.3s ease;
        min-width: 80px;
        text-align: center;
        color: white;
        backdrop-filter: blur(10px);
    }

    .category-item:hover {
        background: rgba(255, 255, 255, 0.2);
        border-color: var(--accent-color);
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(11, 47, 108, 0.3);
    }

    .category-item.active {
        background: var(--accent-color);
        color: white;
        border-color: var(--accent-color);
        font-weight: bold;
        box-shadow: 0 4px 15px rgba(11, 47, 108, 0.4);
    }

    .category-item i {
        font-size: 1.2rem;
        margin-bottom: 5px;
    }

    .category-item span {
        font-size: 0.85rem;
        font-weight: 500;
        white-space: nowrap;
    }

    /* Products Info */
    .products-info .badge {
        font-size: 0.9rem;
        background: linear-gradient(135deg, var(--primary-blue), var(--secondary-blue)) !important;
        border: 2px solid var(--accent-color);
    }

    /* Load More Button */
    .load-more-btn {
        background: linear-gradient(135deg, var(--primary-blue), var(--secondary-blue));
        border: 2px solid var(--accent-color);
        color: white;
        padding: 12px 30px;
        font-size: 1.1rem;
        font-weight: 600;
        border-radius: 25px;
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
    }

    .load-more-btn:hover {
        background: linear-gradient(135deg, var(--secondary-blue), var(--primary-blue));
        border-color: var(--accent-color);
        color: white;
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(11, 47, 108, 0.3);
    }

    .load-more-btn:active {
        transform: translateY(0);
    }

    .load-more-btn:disabled {
        opacity: 0.7;
        cursor: not-allowed;
        transform: none;
    }

    .load-more-btn .spinner-border {
        width: 1rem;
        height: 1rem;
    }

    /* Mobile Responsiveness */
    @media (max-width: 768px) {
        .category-filter-container {
            margin: 0 10px;
            padding: 10px;
        }

        .category-item {
            min-width: 70px;
            padding: 10px 12px;
        }

        .category-item i {
            font-size: 1rem;
        }

        .category-item span {
            font-size: 0.75rem;
        }

        .load-more-btn {
            padding: 10px 25px;
            font-size: 1rem;
        }
    }
</style>

@section('content_header')
<div class="text-center mb-4 fade-in">
    <h2 class="fw-bold mb-2" style="color: var(--primary-blue);">
        <i class="bi bi-shop me-2"></i> Amigos '98 Marketplace
    </h2>
    <p class="slogan mb-0" style="font-size: 0.9rem;">Buy to Save, Share to Earn</p>
</div>
@stop

@section('content')
<div class="container-fluid p-2">
    {{-- Search bar --}}
    <div class="mb-4">
        <form method="GET" class="fade-in" id="searchForm">
            <div class="input-group" style="max-width: 500px; margin: 0 auto;">
                <input type="text" name="q" class="form-control" placeholder="🔍 Search products..."
                       value="{{ request('q') }}" style="border-radius: 25px 0 0 25px; border-right: none;">
                <button class="btn btn-primary" type="submit" style="border-radius: 0 25px 25px 0;">
                    <i class="bi bi-search"></i>
                </button>
            </div>
            <input type="hidden" name="category" value="{{ request('category') }}">
        </form>
    </div>


{{-- Category Carousel --}}
<div class="mb-4">
    <h4 class="section-title text-center">Categories</h4>
    <div class="category-carousel">
        <div class="category-item {{ !request('category') ? 'active' : '' }}" data-category-id="all">
            <i class="bi bi-grid-3x3-gap-fill mb-2" style="font-size: 1.5rem;"></i>
      <div class="fw-bold text-white">All</div>
        </div>
        @foreach($categories as $category)
            <div class="category-item {{ request('category') == $category->id ? 'active' : '' }}" data-category-id="{{ $category->id }}">
                @if($category->image)
                    <img src="{{ asset('storage/' . $category->image) }}" alt="{{ $category->name }}"
                         style="width: 40px; height: 40px; object-fit: cover; border-radius: 50%;" class="mb-2">
                @else
                    <i class="bi bi-tag-fill mb-2" style="font-size: 1.5rem;"></i>
                @endif
                <div class="fw-bold">{{ $category->name }}</div>
            </div>
        @endforeach
    </div>
</div>


    {{-- Products Count Info --}}
    @if($products->count() > 0)
        <div class="products-info mb-3 fade-in">
            <div class="text-center">
                <span class="badge bg-primary rounded-pill px-3 py-2">
                    <i class="bi bi-box-seam me-1"></i>
                    Showing {{ $products->count() }} of {{ $totalProducts }} products
                </span>
            </div>
        </div>
    @endif

    {{-- Products Grid --}}
    @if($products->count())
        <div class="row g-3 mb-4" id="productsGrid">
            @include('shop.partials.product-grid', ['products' => $products])
        </div>

        {{-- Load More Button --}}
        @if($hasMore)
            <div class="text-center mt-4 mb-4 fade-in">
                <button class="btn btn-outline-primary text-white btn-lg load-more-btn" id="loadMoreBtn" data-page="2">
                    <i class="bi bi-plus-circle text-white me-2"></i> <span class="btn-text">Load More Products</span>
                    <div class="spinner-border spinner-border-sm ms-2 d-none" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </button>
            </div>
        @endif
    @else
        <div class="alert alert-info text-center fade-in">
            <i class="bi bi-search fs-1 mb-3" style="color: var(--primary-blue);"></i>
            <h5 class="fw-bold" style="color: var(--primary-blue);">No Products Found</h5>
            <p class="mb-0">Try adjusting your search terms or browse all products.</p>
            @if(request('q') || request('category'))
                <div class="mt-3">
                    <a href="{{ route('shop.index') }}" class="btn btn-outline-primary">
                        <i class="bi bi-list me-1"></i>View All Products
                    </a>
                </div>
            @endif
        </div>
    @endif

    @include('partials.cart-floating-button')
</div>


@endsection

@section('css')
<style>
    /* CSS Variables for E-Bili Theme */
    :root {
        --primary-blue: #0084fe;
        --secondary-blue: #0b2f6c;
        --accent-color: #0b2f6c;
        --light-bg: #f2f4f8;
        --dark-text: #000000;
    }

    /* SUPER STRONG Enhanced pagination styling - Override everything! */
    .content-wrapper .pagination,
    .pagination-wrapper .pagination,
    nav .pagination,
    .fade-in .pagination {
        justify-content: center !important;
        margin: 2rem 0 !important;
        gap: 8px !important;
        display: flex !important;
        flex-wrap: wrap !important;
    }

    .content-wrapper .pagination .page-item,
    .pagination-wrapper .pagination .page-item,
    nav .pagination .page-item,
    .fade-in .pagination .page-item {
        margin: 0 !important;
        margin-left: 0 !important;
        margin-right: 0 !important;
    }

    .content-wrapper .pagination .page-link,
    .pagination-wrapper .pagination .page-link,
    nav .pagination .page-link,
    .fade-in .pagination .page-link,
    .pagination li a,
    .pagination li span {
        border-radius: 12px !important;
        border: 2px solid rgba(0, 132, 254, 0.2) !important;
        color: #0084fe !important;
        font-family: 'Poppins', sans-serif !important;
        font-weight: 600 !important;
        padding: 12px 16px !important;
        min-width: 48px !important;
        text-align: center !important;
        transition: all 0.3s ease !important;
        background: white !important;
        background-color: white !important;
        box-shadow: 0 2px 8px rgba(0, 132, 254, 0.1) !important;
        text-decoration: none !important;
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
        margin-left: 0 !important;
        margin-right: 4px !important;
    }

    .content-wrapper .pagination .page-link:hover,
    .pagination-wrapper .pagination .page-link:hover,
    nav .pagination .page-link:hover,
    .fade-in .pagination .page-link:hover,
    .pagination li a:hover,
    .pagination li span:hover {
        background: linear-gradient(135deg, #f2f4f8 0%, rgba(0, 132, 254, 0.1) 100%) !important;
        background-color: #f2f4f8 !important;
        border-color: #0084fe !important;
        color: #0084fe !important;
        transform: translateY(-2px) !important;
        box-shadow: 0 4px 15px rgba(0, 132, 254, 0.2) !important;
        text-decoration: none !important;
    }

    .content-wrapper .pagination .page-item.active .page-link,
    .pagination-wrapper .pagination .page-item.active .page-link,
    nav .pagination .page-item.active .page-link,
    .fade-in .pagination .page-item.active .page-link,
    .pagination li.active a,
    .pagination li.active span {
        background: linear-gradient(135deg, #0084fe 0%, #0b2f6c 100%) !important;
        background-color: #0084fe !important;
        border-color: #0084fe !important;
        color: white !important;
        transform: translateY(-2px) !important;
        box-shadow: 0 4px 15px rgba(0, 132, 254, 0.3) !important;
    }

    .content-wrapper .pagination .page-item.disabled .page-link,
    .pagination-wrapper .pagination .page-item.disabled .page-link,
    nav .pagination .page-item.disabled .page-link,
    .fade-in .pagination .page-item.disabled .page-link,
    .pagination li.disabled a,
    .pagination li.disabled span {
        background: #f8f9fa !important;
        background-color: #f8f9fa !important;
        border-color: #dee2e6 !important;
        color: #6c757d !important;
        cursor: not-allowed !important;
        transform: none !important;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1) !important;
    }

    /* Previous/Next button styling - SUPER SPECIFIC */
    .content-wrapper .pagination .page-item:first-child .page-link,
    .content-wrapper .pagination .page-item:last-child .page-link,
    .pagination-wrapper .pagination .page-item:first-child .page-link,
    .pagination-wrapper .pagination .page-item:last-child .page-link,
    nav .pagination .page-item:first-child .page-link,
    nav .pagination .page-item:last-child .page-link,
    .fade-in .pagination .page-item:first-child .page-link,
    .fade-in .pagination .page-item:last-child .page-link,
    .pagination li:first-child a,
    .pagination li:first-child span,
    .pagination li:last-child a,
    .pagination li:last-child span {
        font-weight: 700 !important;
        padding: 12px 20px !important;
        background: linear-gradient(135deg, #0084fe 0%, #0b2f6c 100%) !important;
        background-color: #0084fe !important;
        color: white !important;
        border-color: #0084fe !important;
    }

    .content-wrapper .pagination .page-item:first-child .page-link:hover,
    .content-wrapper .pagination .page-item:last-child .page-link:hover,
    .pagination-wrapper .pagination .page-item:first-child .page-link:hover,
    .pagination-wrapper .pagination .page-item:last-child .page-link:hover,
    nav .pagination .page-item:first-child .page-link:hover,
    nav .pagination .page-item:last-child .page-link:hover,
    .fade-in .pagination .page-item:first-child .page-link:hover,
    .fade-in .pagination .page-item:last-child .page-link:hover,
    .pagination li:first-child a:hover,
    .pagination li:first-child span:hover,
    .pagination li:last-child a:hover,
    .pagination li:last-child span:hover {
        background: linear-gradient(135deg, #0b2f6c 0%, #0084fe 100%) !important;
        background-color: #0b2f6c !important;
        color: white !important;
        transform: translateY(-2px) !important;
        box-shadow: 0 4px 15px rgba(11, 47, 108, 0.3) !important;
    }

    /* Pagination container styling */
    .pagination-wrapper {
        background: white !important;
        border-radius: 20px !important;
        padding: 20px !important;
        box-shadow: 0 4px 15px rgba(111, 66, 193, 0.1) !important;
        border: 1px solid rgba(111, 66, 193, 0.1) !important;
        margin: 2rem auto !important;
        max-width: fit-content !important;
    }

    /* Mobile responsive pagination */
    @media (max-width: 768px) {
        .content-wrapper .pagination .page-link,
        .pagination-wrapper .pagination .page-link,
        nav .pagination .page-link,
        .fade-in .pagination .page-link,
        .pagination li a,
        .pagination li span {
            padding: 10px 12px !important;
            min-width: 40px !important;
            font-size: 0.9rem !important;
        }

        .content-wrapper .pagination .page-item:first-child .page-link,
        .content-wrapper .pagination .page-item:last-child .page-link,
        .pagination-wrapper .pagination .page-item:first-child .page-link,
        .pagination-wrapper .pagination .page-item:last-child .page-link,
        .pagination li:first-child a,
        .pagination li:first-child span,
        .pagination li:last-child a,
        .pagination li:last-child span {
            padding: 10px 16px !important;
        }

        .content-wrapper .pagination,
        .pagination-wrapper .pagination,
        nav .pagination,
        .fade-in .pagination {
            gap: 4px !important;
            margin: 1.5rem 0 !important;
        }

        .pagination-wrapper {
            padding: 15px !important;
            margin: 1.5rem auto !important;
        }
    }

    /* Pagination info text */
    .pagination-info {
        text-align: center !important;
        color: #0084fe !important;
        font-family: 'Poppins', sans-serif !important;
        font-weight: 500 !important;
        margin-bottom: 1rem !important;
        font-size: 0.9rem !important;
    }

    /* Override ALL possible conflicting styles */
    .pagination .page-link:focus,
    .pagination li a:focus,
    .pagination li span:focus {
        box-shadow: 0 0 0 0.2rem rgba(111, 66, 193, 0.25) !important;
        outline: none !important;
    }

    /* Remove default margins */
    .pagination .page-item + .page-item .page-link,
    .pagination li + li a,
    .pagination li + li span {
        margin-left: 0 !important;
    }

    /* Force the styling on all pagination elements */
    .pagination * {
        font-family: 'Poppins', sans-serif !important;
    }
</style>
@endsection
@include('partials.mobile-footer')
@section('js')
<script>
$(document).ready(function() {
    let currentPage = 2;
    let isLoading = false;

    // Category filter functionality
    $('.category-item').on('click', function() {
        const categoryId = $(this).data('category-id');
        const currentSearch = $('input[name="q"]').val();

        // Update active state
        $('.category-item').removeClass('active');
        $(this).addClass('active');

        // Update hidden input and submit form
        if (categoryId === 'all') {
            $('input[name="category"]').val('');
        } else {
            $('input[name="category"]').val(categoryId);
        }
        $('#searchForm').submit();
    });

    // Load More functionality
    $('#loadMoreBtn').on('click', function() {
        if (isLoading) return;

        isLoading = true;
        const btn = $(this);
        const spinner = btn.find('.spinner-border');
        const icon = btn.find('.bi-plus-circle');
        const btnText = btn.find('.btn-text');

        // Show loading state
        btn.prop('disabled', true);
        spinner.removeClass('d-none');
        icon.addClass('d-none');
        btnText.addClass('d-none');

        // Get current filters
        const searchQuery = $('input[name="q"]').val();
        const categoryId = $('input[name="category"]').val();

        // Make AJAX request
        $.ajax({
            url: '{{ route("shop.index") }}',
            method: 'GET',
            data: {
                page: currentPage,
                q: searchQuery,
                category: categoryId,
                ajax: 1
            },
            success: function(response) {
                if (response.success && response.html) {
                    // Append new products to grid
                    $('#productsGrid').append(response.html);

                    // Update page counter
                    currentPage++;
                    btn.data('page', currentPage);

                    // Update products count
                    const currentCount = $('#productsGrid .col-6').length;
                    $('.products-info .badge').html(
                        '<i class="bi bi-box-seam me-1"></i>Showing ' + currentCount + ' of ' + response.totalProducts + ' products'
                    );

                    // Hide button if no more products
                    if (!response.hasMore) {
                        btn.closest('.text-center').fadeOut();
                    }

                    // Initialize Fancybox for new images
                    if (typeof $.fancybox !== 'undefined') {
                        $('[data-fancybox]').fancybox({
                            buttons: ['zoom', 'close'],
                            loop: false,
                            protect: true
                        });
                    }

                    // Add fade-in animation to new products
                    $('#productsGrid .col-6:nth-last-child(-n+' + response.productsCount + ')').addClass('fade-in');
                }
            },
            error: function(xhr, status, error) {
                console.error('Error loading more products:', error);

                // Show error message
                const alertHtml = `
                    <div class="alert alert-warning alert-dismissible fade show mt-3" role="alert">
                        <i class="bi bi-exclamation-triangle me-2"></i>
                        Failed to load more products. Please try again.
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                `;
                btn.closest('.text-center').after(alertHtml);
            },
            complete: function() {
                // Reset loading state
                isLoading = false;
                btn.prop('disabled', false);
                spinner.addClass('d-none');
                icon.removeClass('d-none');
                btnText.removeClass('d-none');
            }
        });
    });

    // Initialize Fancybox for existing images
    if (typeof $.fancybox !== 'undefined') {
        $('[data-fancybox]').fancybox({
            buttons: ['zoom', 'close'],
            loop: false,
            protect: true
        });
    }

    // Smooth scroll for category selection
    $('.category-item').on('click', function() {
        $('html, body').animate({
            scrollTop: $('#productsGrid').offset().top - 100
        }, 500);
    });
});
</script>
@stop
