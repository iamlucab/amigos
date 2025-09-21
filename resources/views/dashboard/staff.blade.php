@extends('layouts.adminlte-base')
@section('title', 'Staff Dashboard')


<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
<link rel="stylesheet" href="{{ asset('css/ebili-theme.css') }}">

@section('content_header')
<div class="text-center mb-4 fade-in">
    {{-- Logo Container --}}
    <div class="logo-container mx-auto mb-3" style="width: 80px; height: 80px;">
        <img src="{{ asset('storage/icons/ebili-logo.png') }}" alt="eBILI Logo" style="width: 60px; height: 60px; object-fit: contain;">
    </div>
    
    <h2 class="fw-bold mb-2" style="color: var(--primary-purple);">Staff Dashboard</h2>
    <p class="slogan mb-0" style="font-size: 0.9rem;">Support Your Amigos '98 Online</p>
</div>
@stop

@section('content')
<div class="container-fluid">
    {{-- ✅ Summary Cards --}}
    <div class="row mb-4">
        <div class="col-md-3 mb-3">
            <div class="card fade-in">
                <div class="card-body text-center">
                    <div class="d-flex align-items-center justify-content-center mb-3">
                        <div class="rounded-circle d-flex align-items-center justify-content-center me-3"
                             style="width: 60px; height: 60px; background: linear-gradient(135deg, #17a2b8 0%, #20c997 100%);">
                            <i class="bi bi-box-seam fa-2x text-white"></i>
                        </div>
                        <div class="text-start">
                            <h3 class="fw-bold mb-0" style="color: var(--primary-purple);">{{ $myProductsCount }}</h3>
                            <p class="text-muted mb-0">My Products</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card fade-in">
                <div class="card-body text-center">
                    <div class="d-flex align-items-center justify-content-center mb-3">
                        <div class="rounded-circle d-flex align-items-center justify-content-center me-3"
                             style="width: 60px; height: 60px; background: linear-gradient(135deg, #28a745 0%, #20c997 100%);">
                            <i class="bi bi-check-circle fa-2x text-white"></i>
                        </div>
                        <div class="text-start">
                            <h3 class="fw-bold mb-0" style="color: var(--primary-purple);">{{ $activeProductsCount }}</h3>
                            <p class="text-muted mb-0">Active Products</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card fade-in">
                <div class="card-body text-center">
                    <div class="d-flex align-items-center justify-content-center mb-3">
                        <div class="rounded-circle d-flex align-items-center justify-content-center me-3"
                             style="width: 60px; height: 60px; background: linear-gradient(135deg, #dc3545 0%, #fd7e14 100%);">
                            <i class="bi bi-x-circle fa-2x text-white"></i>
                        </div>
                        <div class="text-start">
                            <h3 class="fw-bold mb-0" style="color: var(--primary-purple);">{{ $myProductsCount - $activeProductsCount }}</h3>
                            <p class="text-muted mb-0">In-Active Products</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card fade-in">
                <div class="card-body text-center">
                    <div class="d-flex align-items-center justify-content-center mb-3">
                        <div class="rounded-circle d-flex align-items-center justify-content-center me-3"
                             style="width: 60px; height: 60px; background: linear-gradient(135deg, #e83e8c 0%, #fd7e14 100%);">
                            <i class="bi bi-boxes fa-2x text-white"></i>
                        </div>
                        <div class="text-start">
                            <h3 class="fw-bold mb-0" style="color: var(--primary-purple);">{{ $totalStock }}</h3>
                            <p class="text-muted mb-0">Total Stock</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ✅ Recent Products Section --}}
    @if($recentProducts->count() > 0)
    <div class="row mb-4">
        <div class="col-12">
            <div class="card fade-in">
                <div class="card-header">
                    <h5 class="card-title mb-0">Recent Products</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Product</th>
                                    <th>Category</th>
                                    <th>Price</th>
                                    <th>Stock</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($recentProducts as $product)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            @if($product->thumbnail)
                                                <img src="{{ asset('storage/' . $product->thumbnail) }}"
                                                     alt="{{ $product->name }}"
                                                     class="rounded me-2"
                                                     style="width: 30px; height: 30px; object-fit: cover;">
                                            @endif
                                            <span>{{ Str::limit($product->name, 30) }}</span>
                                        </div>
                                    </td>
                                    <td>{{ $product->category->name ?? 'N/A' }}</td>
                                    <td>₱{{ number_format($product->price, 2) }}</td>
                                    <td>
                                        <span class="badge badge-{{ $product->stock_quantity > 0 ? 'success' : 'danger' }}">
                                            {{ $product->stock_quantity }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge badge-{{ $product->active ? 'success' : 'secondary' }}">
                                            {{ $product->active ? 'Active' : 'Inactive' }}
                                        </span>
                                    </td>
                                    <td>
                                        <a href="{{ route('staff.products.edit', $product) }}"
                                           class="btn btn-sm btn-outline-primary">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="text-center mt-3">
                        <a href="{{ route('staff.products.index') }}" class="btn btn-primary">
                            <i class="bi bi-box-seam"></i> View All My Products
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- ✅ Staff Action Icons --}}
    <div class="mb-4">
        <h4 class="section-title text-center">Product Management</h4>
        <div class="row text-center">
            <div class="col-12 col-md-12 mb-4">
                <a href="{{ route('staff.products.index') }}" class="text-decoration-none">
                    <div class="card fade-in p-4 h-100">
                        <i class="bi bi-box-seam fa-3x mb-3" style="color: #e83e8c;"></i>
                        <div class="fw-bold" style="color: var(--primary-purple);">My Products</div>
                        <small class="text-muted">Manage your products</small>
                    </div>
                </a>
            </div>
        </div>
    </div>
</div>
@stop
