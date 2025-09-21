@extends('adminlte::page')

@section('title', 'Product Details - Admin')

@section('content_header')
    <h6>Product Details</h6>
    <p class="text-muted">View product information</p>
@stop

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">{{ $product->name }}</h3>
                    <div class="card-tools">
                        <a href="{{ route('admin.products.index') }}" class="btn btn-secondary btn-sm">
                            <i class="bi bi-arrow-left"></i> Back to Products
                        </a>
                        <a href="{{ route('admin.products.edit', $product) }}" class="btn btn-primary btn-sm">
                            <i class="bi bi-pencil"></i> Edit Product
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            {{-- Product Image --}}
                            @if($product->thumbnail)
                                <div class="text-center mb-4">
                                    <img src="{{ asset('storage/' . $product->thumbnail) }}" 
                                         alt="{{ $product->name }}" 
                                         class="img-fluid rounded"
                                         style="max-height: 300px;">
                                </div>
                            @endif

                            {{-- Gallery --}}
                            @if($product->gallery && count($product->gallery) > 0)
                                <div class="mb-4">
                                    <h5>Gallery</h5>
                                    <div class="row">
                                        @foreach($product->gallery as $image)
                                            <div class="col-4 mb-2">
                                                <img src="{{ asset('storage/' . $image) }}" 
                                                     alt="Gallery Image" 
                                                     class="img-fluid rounded">
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        </div>

                        <div class="col-md-6">
                            {{-- Product Details --}}
                            <table class="table table-borderless">
                                <tr>
                                    <th width="30%">Name:</th>
                                    <td>{{ $product->name }}</td>
                                </tr>
                                <tr>
                                    <th>Description:</th>
                                    <td>{!! nl2br(e($product->description)) ?: '<span class="text-muted">No description</span>' !!}</td>
                                </tr>
                                <tr>
                                    <th>Category:</th>
                                    <td>{{ $product->category->name ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <th>Unit:</th>
                                    <td>{{ $product->unit->name ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <th>Price:</th>
                                    <td>
                                        @if($product->hasDiscount())
                                            <span class="text-success h5">₱{{ number_format($product->getDiscountedPrice(), 2) }}</span>
                                            <br><small class="text-muted text-decoration-line-through">₱{{ number_format($product->price, 2) }}</small>
                                            <span class="badge badge-danger">{{ $product->getDiscountPercentage() }}% OFF</span>
                                        @else
                                            <span class="h5">₱{{ number_format($product->price, 2) }}</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>Stock Quantity:</th>
                                    <td>
                                        <span class="badge badge-{{ $product->stock_quantity > 0 ? 'success' : 'danger' }} badge-lg">
                                            {{ $product->stock_quantity }}
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Cashback:</th>
                                    <td>
                                        ₱{{ number_format($product->cashback_amount, 2) }}
                                        <br><small class="text-muted">Levels 1-{{ $product->cashback_max_level }}</small>
                                    </td>
                                </tr>
                                @if($product->attributes)
                                <tr>
                                    <th>Attributes:</th>
                                    <td>{{ is_array($product->attributes) ? implode(', ', $product->attributes) : $product->attributes }}</td>
                                </tr>
                                @endif
                                @if($product->promo_code)
                                <tr>
                                    <th>Promo Code:</th>
                                    <td><code>{{ $product->promo_code }}</code></td>
                                </tr>
                                @endif
                                <tr>
                                    <th>Created:</th>
                                    <td>{{ $product->created_at->format('M d, Y h:i A') }}</td>
                                </tr>
                                <tr>
                                    <th>Updated:</th>
                                    <td>{{ $product->updated_at->format('M d, Y h:i A') }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    {{-- Cashback Breakdown --}}
                    @if($product->cashback_amount > 0)
                    <div class="row mt-4">
                        <div class="col-12">
                            <h5>Cashback Distribution</h5>
                            <div class="table-responsive">
                                <table class="table table-sm table-bordered">
                                    <thead>
                                        <tr>
                                            @for($i = 1; $i <= $product->cashback_max_level; $i++)
                                                <th class="text-center">Level {{ $i }}</th>
                                            @endfor
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            @foreach($product->getAllCashbacks() as $level => $amount)
                                                <td class="text-center">₱{{ number_format($amount, 2) }}</td>
                                            @endforeach
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
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

@include('partials.mobile-footer')