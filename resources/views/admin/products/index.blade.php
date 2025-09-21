@extends('adminlte::page')

@section('title', 'Manage Products (Grid)')

@section('content')
<div class="container-fluid p-2">

    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="mb-0">🛒 Manage Products (Grid)</h5>
        <a href="{{ route('admin.products.create') }}" class="btn btn-primary rounded-pill">
            <i class="bi bi-plus me-1"></i> Add Product
        </a>
    </div>

    {{-- Success Alert --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show rounded-pill" role="alert">
            {{ session('success') }}
        </div>
    @endif

    {{-- Filter --}}
    <form method="GET" class="mb-3">
        <div class="row g-2">
            <div class="col-md-4 col-sm-6">
                <select name="category_id" class="form-select form-select-sm rounded-pill" onchange="this.form.submit()">
                    <option value="">🔍 Filter by Category</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}" {{ $selectedCategoryId == $category->id ? 'selected' : '' }}>
                            {{ $category->name }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>
    </form>

    {{-- Grid --}}
    <div class="row g-3">
        @forelse($products as $product)
            <div class="col-6 col-sm-4 col-md-3 col-lg-2">
                <div class="card h-100 shadow-sm border-0 rounded-4">

                    {{-- Thumbnail --}}
                    @if($product->thumbnail)
                        <a data-fancybox="thumb-{{ $product->id }}" href="{{ asset('storage/' . $product->thumbnail) }}">
                            <img src="{{ asset('storage/' . $product->thumbnail) }}"
                                 class="card-img-top p-2 rounded-4"
                                 style="height: 130px; object-fit: cover;"
                                 alt="{{ $product->name }}">
                        </a>
                    @else
                        <div class="text-center py-4 bg-light rounded-top-4">
                            <i class="bi bi-box-seam fa-3x text-muted"></i>
                        </div>
                    @endif

                    {{-- Body --}}
                    <div class="card-body p-2 d-flex flex-column">
                        <h6 class="card-title fw-bold mb-1 text-truncate" title="{{ $product->name }}">
                            {{ $product->name }}
                        </h6>

                        <small class="text-muted d-block text-truncate" title="{{ $product->description }}">
                            {{ Str::limit($product->description, 50) }}
                        </small>

                        <div class="mt-2">
                            <span class="badge bg-success rounded-pill">₱{{ number_format($product->price, 2) }}</span>
                            <span class="badge bg-info text-dark rounded-pill">
                                ₱{{ number_format($product->cashback_amount, 2) }}
                            </span>
                            <small class="text-muted d-block">Level 1 to Level {{ $product->cashback_max_level }}</small>
                        </div>

                        <div class="mt-1">
                            <span class="badge bg-secondary text-truncate">
                                {{ $product->category->name ?? 'Uncategorized' }}
                            </span>
                            <span class="badge bg-primary rounded-pill">
                                Stock: {{ $product->stock_quantity ?? 0 }}
                            </span>
                        </div>

                        {{-- Actions --}}
                        <div class="d-flex gap-1 mt-2">
                            <a href="{{ route('admin.products.show', $product) }}"
                               class="btn btn-sm btn-outline-info rounded-pill flex-fill" title="View">
                                <i class="bi bi-eye"></i> View
                            </a>
                            <a href="{{ route('admin.products.edit', $product) }}"
                               class="btn btn-sm btn-outline-warning rounded-pill flex-fill" title="Edit">
                                <i class="bi bi-pencil"></i> Edit
                            </a>

                            <form action="{{ route('admin.products.destroy', $product) }}"
                                  method="POST" class="flex-fill"
                                  onsubmit="return confirm('Are you sure you want to delete this product?');">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger rounded-pill flex-fill" title="Delete">
                                    <i class="bi bi-trash"></i> Delete
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="alert alert-info text-center rounded-pill">
                    No products found{{ $selectedCategoryId ? ' in this category.' : '.' }}
                </div>
            </div>
        @endforelse
    </div>

</div>
@endsection
@include('partials.mobile-footer')
