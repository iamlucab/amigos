@extends('adminlte::page')

@section('title', 'Track Order')

@section('content')
<div class="container py-3">
    <div class="card shadow-sm">
        <div class="card-header bg-info text-white">
            <h5>Tracking Order #{{ $order->id }}</h5>
        </div>
        <div class="card-body">
            <div class="progress mb-3">
                @php
                    $status = $order->status;
                    $progress = match($status) {
                        'Pending' => 25,
                        'Processing' => 50,
                        'On the Way' => 75,
                        'Delivered' => 100,
                        default => 0
                    };
                @endphp
                <div class="progress-bar progress-bar-striped bg-success" role="progressbar" style="width: {{ $progress }}%">
                    {{ $status }}
                </div>
            </div>

            <p><strong>Current Status:</strong> {{ $status }}</p>
            <p><strong>Last Update:</strong> {{ $order->updated_at->diffForHumans() }}</p>
            <a href="{{ route('member.orders') }}" class="btn btn-secondary">Back to Orders</a>
        </div>
    </div>
</div>
@endsection