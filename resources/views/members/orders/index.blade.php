@extends('adminlte::page')
@section('title', 'My Purchase History')

@section('css')
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.dataTables.min.css">
<style>
    @media (max-width: 768px) {
        td, th {
            white-space: nowrap;
        }

        .mobile-card {
            background: #ffffff;
            padding: 0.75rem;
            margin-bottom: 0.75rem;
            border-radius: 0.75rem;
            box-shadow: 0 2px 8px rgba(0,0,0,0.06);
            transition: 0.3s ease;
        }

        .mobile-card:hover {
            transform: scale(1.02);
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }

        .mobile-card img {
            width: 48px;
            height: 48px;
            object-fit: cover;
            border-radius: 0.5rem;
        }
    }
</style>
@endsection

@section('content')
<div class="container-fluid"><br>
    <h5 class="mb-3 text-primary fw-bold"> My Purchase History</h4>

    {{-- Filters --}}
    <form method="GET" class="row gy-2 gx-2 mb-4 align-items-end">
        <div class="col-sm-6 col-md-3">
            <label class="form-label small">From</label>
            <input type="date" name="from" value="{{ request('from') }}" class="form-control rounded-pill shadow-sm">
        </div>

        <div class="col-sm-6 col-md-3">
            <label class="form-label small">To</label>
            <input type="date" name="to" value="{{ request('to') }}" class="form-control rounded-pill shadow-sm">
        </div>

        <div class="col-sm-6 col-md-3">
            <label class="form-label small">Status</label>
            <select name="status" class="form-select rounded-pill shadow-sm">
                <option value="">All Status</option>
                <option value="Pending" {{ request('status') == 'Pending' ? 'selected' : '' }}>Pending</option>
                <option value="Processing" {{ request('status') == 'Processing' ? 'selected' : '' }}>Processing</option>
                <option value="On the Way" {{ request('status') == 'On the Way' ? 'selected' : '' }}>On the Way</option>
                <option value="Delivered" {{ request('status') == 'Delivered' ? 'selected' : '' }}>Delivered</option>
            </select>
        </div>

        <div class="col-sm-6 col-md-3">
            <label class="form-label small">Payment Method</label>
            <select name="payment_method" class="form-select rounded-pill shadow-sm">
                <option value="">All Methods</option>
                <option value="Wallet" {{ request('payment_method') == 'Wallet' ? 'selected' : '' }}>Wallet</option>
                <option value="GCash" {{ request('payment_method') == 'GCash' ? 'selected' : '' }}>GCash</option>
                <option value="Bank" {{ request('payment_method') == 'Bank' ? 'selected' : '' }}>Bank</option>
                <option value="COD" {{ request('payment_method') == 'COD' ? 'selected' : '' }}>COD</option>
            </select>
        </div>

        <div class="col-sm-6 col-md-3">
            <label class="form-label small">Quick Range</label>
            <select name="date_range" class="form-select rounded-pill shadow-sm">
                <option value="">-- Select Range --</option>
                <option value="last_7_days" {{ request('date_range') == 'last_7_days' ? 'selected' : '' }}>Last 7 Days</option>
                <option value="this_month" {{ request('date_range') == 'this_month' ? 'selected' : '' }}>This Month</option>
                <option value="last_month" {{ request('date_range') == 'last_month' ? 'selected' : '' }}>Last Month</option>
            </select>
        </div>

        <div class="col-sm-6 col-md-3 d-flex gap-2">
            <button class="btn btn-primary rounded-pill shadow-sm w-100" type="submit">
                <i class="bi bi-search"></i> Search
            </button>
            <a href="{{ route('orders.index') }}" class="btn btn-info rounded-pill shadow-sm w-100 text-white">
                Reset
            </a>
        </div>
    </form>

    {{-- Summary Cards --}}
    <div class="row mb-4">
        <div class="col-md-4 mb-2">
            <div class="card bg-gradient-success text-white rounded-4 shadow">
                <div class="card-body py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>

                            
                            <div class="fw-bold text-white">Total Purchases</div>
                            <small class="text-white-50">Orders you've paid for</small>
                        </div>
                        <div class="fs-5">₱{{ number_format($totalSales ?? 0, 2) }}</div>
                    </div>
                </div>
            </div>
        </div>

       @php
    $cashbackOnHold = 0;
@endphp

@foreach($orders as $order)
    @foreach($order->items as $item)
        @if($order->status != 'Delivered')
            @php
                $cashbackOnHold += $item->cashback * $item->quantity;
            @endphp
        @endif
    @endforeach
@endforeach

<div class="col-md-4 mb-2">
    <div class="card bg-gradient-info text-white rounded-4 shadow">
        <div class="card-body py-3">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <div class="fw-bold text-white">Cashback On Hold</div>
                    <small class="text-white-50">Pending credit</small>
                </div>
                <div class="fs-5">₱{{ number_format($cashbackOnHold, 2) }}</div>
            </div>
        </div>
    </div>
</div>

@php
    $walletCashback = 0;
@endphp

@foreach($orders as $order)
    @if($order->status == 'Delivered')
        @foreach($order->items as $item)
            @php
                $walletCashback += $item->cashback * $item->quantity;
            @endphp
        @endforeach
    @endif
@endforeach

<div class="col-md-4 mb-2">
    <div class="card bg-gradient-warning text-dark rounded-4 shadow">
        <div class="card-body py-3">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <div class="fw-bold text-white ">Cashback Credited</div>
                    <small class="text-muted">Cashback earned</small>
                </div>
                <div class="fs-5">₱{{ number_format($walletCashback, 2) }}</div>
            </div>
        </div>
    </div>
</div>



    </div>

   {{-- Desktop Table --}}
<div class="d-none d-md-block table-responsive" style="max-height: 70vh; overflow-y: auto;">
    <table class="table table-hover align-middle text-center" id="ordersTable">
        <thead class="sticky-top shadow-sm">
            <tr>
                <th>Date</th>
                <th>Product</th>
                <th>Description</th>
                <th>Qty</th>
                <th>Price</th>
                <th>Total</th>
                <th>Cashback</th>
                <th>Method</th>
                <th>Status</th>
                <th>Proof</th>
                <th>Notes</th>
            </tr>
        </thead>
        <tbody>
            @foreach($orders as $order)
                @foreach($order->items as $item)
                    <tr>
                        <td class="text-muted small">{{ $order->created_at->format('Y-m-d H:i') }}</td>
                        
                        <td>
                            @if($item->product->thumbnail)
                                <img src="{{ asset('storage/' . $item->product->thumbnail) }}" 
                                     class="img-thumbnail shadow-sm" 
                                     style="width: 50px; height: 50px; object-fit: cover;" 
                                     loading="lazy">
                            @endif
                        </td>
                        
                        <td class="fw-semibold">{{ $item->product->name }}</td>
                        <td>{{ $item->quantity }}</td>
                        <td class="text-success fw-bold">₱{{ number_format($item->price, 2) }}</td>
                        <td class="fw-bold">₱{{ number_format($item->price * $item->quantity, 2) }}</td>
                        <td class="text-primary">₱{{ number_format($item->cashback * $item->quantity, 2) }}</td>
                        <td><span class="badge bg-secondary">{{ $order->payment_method }}</span></td>

                        <td>
                            @php
                                $badgeColor = match($order->status) {
                                    'Pending' => 'warning',
                                    'Processing' => 'info',
                                    'On the Way' => 'primary',
                                    'Delivered' => 'success',
                                    default => 'secondary',
                                };
                            @endphp
                            <span class="badge bg-{{ $badgeColor }} px-3 py-2">{{ $order->status }}</span>
                        </td>

                        <td>
                            @if($order->reference_image)
                                <a href="{{ asset('storage/' . $order->reference_image) }}" target="_blank">
                                    <img src="{{ asset('storage/' . $order->reference_image) }}" 
                                         class="rounded border shadow-sm" 
                                         style="width: 40px; height: 40px; object-fit: cover;" 
                                         loading="lazy">
                                </a>
                            @endif
                        </td>
                        
                        <td class="text-muted small">{{ $order->gcash_note ?? $order->note }}</td>
                    </tr>
                @endforeach
            @endforeach
        </tbody>
    </table>
</div>

<style>
    /* Optional extra polish */
    #ordersTable td, 
    #ordersTable th {
        vertical-align: middle;
        white-space: nowrap;
    }
    #ordersTable tbody tr:hover {
        background-color: #f8f9fa;
        transition: background-color 0.2s ease-in-out;
    }
</style>

    {{-- Mobile View --}}
    <div class="d-md-none">
        @foreach($orders as $order)
            @foreach($order->items as $item)
                <div class="mobile-card">
                    <div class="d-flex align-items-center mb-2">
                        @if($item->product->thumbnail)
                            <img src="{{ asset('storage/' . $item->product->thumbnail) }}" class="me-2 shadow-sm" loading="lazy">
                        @endif
                        <div>
                            <strong>{{ $item->product->name }}</strong><br>
                            <small class="text-muted">{{ $order->created_at->format('Y-m-d H:i') }}</small>
                        </div>
                    </div>
                    <div><strong>Qty:</strong> {{ $item->quantity }}</div>
                    <div><strong>Price:</strong> ₱{{ number_format($item->price, 2) }}</div>
                    <div><strong>Total:</strong> ₱{{ number_format($item->price * $item->quantity, 2) }}</div>
                    <div><strong>Cashback:</strong> ₱{{ number_format($item->cashback * $item->quantity, 2) }}</div>
                    <div><strong>Method:</strong> {{ $order->payment_method }}</div>
                    <div><strong>Status:</strong> <span class="badge bg-{{ $order->status == 'Pending' ? 'warning' : 'success' }}">{{ $order->status }}</span></div>
                    @if($order->reference_image)
                        <div class="mt-2">
                            <a href="{{ asset('storage/' . $order->reference_image) }}" target="_blank">
                                <img src="{{ asset('storage/' . $order->reference_image) }}" width="60" height="60" class="rounded shadow-sm" loading="lazy">
                            </a>
                        </div>
                    @endif
                    <div><strong>Note:</strong> {{ $order->gcash_note ?? $order->note }}</div>
                </div>
            @endforeach
        @endforeach
    </div>

    {{-- Pagination --}}
    <div class="mt-3">
        {{ $orders->withQueryString()->links() }}
    </div>
</div>
@endsection

@section('js')
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script>
    $(document).ready(function () {
        $('#ordersTable').DataTable({
            paging: false,
            info: false,
            searching: false,
            ordering: false,
            responsive: true
        });
    });

    // Auto-submit when changing date range
    document.addEventListener('DOMContentLoaded', function () {
        const rangeSelect = document.querySelector('select[name="date_range"]');
        if (rangeSelect) {
            rangeSelect.addEventListener('change', function () {
                this.form.submit();
            });
        }
    });
</script>
@endsection
@include('partials.mobile-footer')