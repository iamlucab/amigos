@extends('adminlte::page')

@section('title', 'My Products - Staff')

@section('content_header')
    <h1>My Products</h1>
    <p class="text-muted">Manage products you have created</p>
@stop

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title" style="color:white !important">Products I Created</h3>
                    <div class="card-tools">
                        <a href="{{ route('staff.products.create') }}" class="btn btn-primary btn-sm">
                            <i class="bi bi-plus"></i> Add New Product
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible">
                            <button type="button" class="close" data-dismiss="alert">&times;</button>
                            {{ session('success') }}
                        </div>
                    @endif
                    
                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible">
                            <button type="button" class="close" data-dismiss="alert">&times;</button>
                            {{ session('error') }}
                        </div>
                    @endif

                    @if($products->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>Image</th>
                                        <th>Name</th>
                                        <th>Category</th>
                                        <th>Price</th>
                                        <th>Stock</th>
                                        <th>Cashback</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($products as $product)
                                        <tr>
                                            <td>
                                                @if($product->thumbnail)
                                                    <img src="{{ asset('storage/' . $product->thumbnail) }}" 
                                                         alt="{{ $product->name }}" 
                                                         class="img-thumbnail" 
                                                         style="width: 50px; height: 50px; object-fit: cover;">
                                                @else
                                                    <div class="bg-light d-flex align-items-center justify-content-center" 
                                                         style="width: 50px; height: 50px;">
                                                        <i class="bi bi-image text-muted"></i>
                                                    </div>
                                                @endif
                                            </td>
                                            <td>
                                                <strong>{{ $product->name }}</strong>
                                                @if($product->description)
                                                    <br><small class="text-muted">{{ Str::limit($product->description, 50) }}</small>
                                                @endif
                                            </td>
                                            <td>{{ $product->category->name ?? 'N/A' }}</td>
                                            <td>
                                                @if($product->hasDiscount())
                                                    <span class="text-success">₱{{ number_format($product->getDiscountedPrice(), 2) }}</span>
                                                    <br><small class="text-muted text-decoration-line-through">₱{{ number_format($product->price, 2) }}</small>
                                                @else
                                                    ₱{{ number_format($product->price, 2) }}
                                                @endif
                                            </td>
                                            <td>
                                                <span class="badge badge-{{ $product->stock_quantity > 0 ? 'success' : 'danger' }}">
                                                    {{ $product->stock_quantity }}
                                                </span>
                                            </td>
                                            <td>
                                                ₱{{ number_format($product->cashback_amount, 2) }}
                                                <br><small class="text-muted">L1-L{{ $product->cashback_max_level }}</small>
                                            </td>
                                            <td>
                                                <span class="badge badge-{{ $product->active ? 'success' : 'secondary' }}">
                                                    {{ $product->active ? 'Active' : 'Inactive' }}
                                                </span>
                                            </td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <a href="{{ route('staff.products.show', $product) }}"
                                                       class="btn btn-info btn-sm" title="View">
                                                        <i class="bi bi-eye"></i>
                                                    </a>
                                                    <a href="{{ route('staff.products.edit', $product) }}"
                                                       class="btn btn-warning btn-sm" title="Edit">
                                                        <i class="bi bi-pencil"></i>
                                                    </a>
                                                    <form action="{{ route('staff.products.toggle-status', $product) }}"
                                                          method="POST"
                                                          style="display: inline;">
                                                        @csrf
                                                        @method('PATCH')
                                                        <button type="submit"
                                                                class="btn btn-{{ $product->active ? 'secondary' : 'success' }} btn-sm"
                                                                title="{{ $product->active ? 'Deactivate' : 'Activate' }}">
                                                            <i class="bi bi-{{ $product->active ? 'pause' : 'play' }}"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="bi bi-box-seam text-muted" style="font-size: 3rem;"></i>
                            <h4 class="text-muted mt-3">No Products Yet</h4>
                            <p class="text-muted">You haven't created any products yet.</p>
                            <a href="{{ route('staff.products.create') }}" class="btn btn-primary">
                                <i class="bi bi-plus"></i> Create Your First Product
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@stop

@section('css')
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
@stop

@section('js')
    <script>
        // Auto-hide success alerts after 5 seconds
        setTimeout(function() {
            $('.alert-success').fadeOut();
        }, 5000);
    </script>
@stop

@include('partials.mobile-footer')