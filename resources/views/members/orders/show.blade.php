@extends('adminlte::page')

@section('title', 'Order Details')

@section('content')
<div class="container mt-4">
    <div class="card shadow-sm rounded">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">Order #{{ $order->id }}</h5>
        </div>

        <div class="card-body">
            <p><strong>Status:</strong> {{ $order->status }}</p>
            <p><strong>Placed On:</strong> {{ $order->created_at->format('F d, Y h:i A') }}</p>
            <p><strong>Delivery Type:</strong> {{ $order->delivery_type }}</p>
            <p><strong>Address:</strong> {{ $order->address }}</p>
            <p><strong>Contact:</strong> {{ $order->contact_number }}</p>

            <hr>

            <h6>Items:</h6>
            <ul class="list-group">
                @foreach ($order->items as $item)
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <span>{{ $item->product->name }} x {{ $item->quantity }}</span>
                        <span>₱{{ number_format($item->price * $item->quantity, 2) }}</span>
                    </li>
                @endforeach
            </ul>

            <hr>
            <p><strong>Shipping Fee:</strong> ₱{{ number_format($order->shipping_fee, 2) }}</p>
            <p><strong>Total:</strong> ₱{{ number_format($order->total_amount + $order->shipping_fee, 2) }}</p>
        </div>
    </div>
</div>
@endsection
