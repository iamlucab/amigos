@extends('adminlte::page')

@section('title', 'Wallet History')

@section('css')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
<style>
    /* Minimal table look for PWA */
    table {
        background: #fff;
    }
    table thead {
        position: sticky;
        top: 0;
        background: #fff;
        z-index: 10;
    }
    table th {
        font-weight: 600;
        font-size: 0.9rem;
        border-bottom: 2px solid #f0f0f0;
    }
    table td {
        vertical-align: middle;
        font-size: 0.88rem;
    }
    /* Mobile card style */
    .mobile-transaction-card {
        border-radius: 10px;
        border: 1px solid #f1f1f1;
        background: #fff;
    }
    .mobile-transaction-card .badge {
        font-size: 0.75rem;
        padding: 0.35em 0.6em;
    }
    /* Light status badges */
    .badge-main {
        background-color: #e0f0ff;
        color: #007bff;
    }
    .badge-cashback {
        background-color: #e6ffed;
        color: #28a745;
    }
</style>
@endsection

@section('content')
<div class="container-fluid px-2">
    <div class="card rounded-3 shadow-sm border-0">
        <div class="card-header bg-white border-0 pb-0">
            <h6 class="fw-bold mb-2">💰 Wallet Transaction History</h6>
        </div>

        <div class="card-body">

            {{-- Filter Form --}}
            <form method="GET" class="mb-3">
                <div class="row g-2">
                    <div class="col-6 col-md-3">
                        <select name="type" class="form-control">
                            <option value="">All Wallets</option>
                            <option value="main" {{ request('type') == 'main' ? 'selected' : '' }}>Main Wallet</option>
                            <option value="cashback" {{ request('type') == 'cashback' ? 'selected' : '' }}>Cashback Wallet</option>
                        </select>
                    </div>
                    <div class="col-6 col-md-3">
                        <input type="date" name="start_date" value="{{ request('start_date') }}" class="form-control">
                    </div>
                    <div class="col-6 col-md-3">
                        <input type="date" name="end_date" value="{{ request('end_date') }}" class="form-control">
                    </div>
                    <div class="col-6 col-md-3">
                        <button class="btn btn-warning w-100">Search</button>
                    </div>
                </div>
            </form>

            {{-- Desktop Table --}}
            <div class="table-responsive d-none d-md-block">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Wallet</th>
                            <th>Description</th>
                            <th class="text-end">Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($transactions as $tx)
                            <tr>
                                <td>{{ \Carbon\Carbon::parse($tx->created_at)->format('Y-m-d H:i') }}</td>
                                <td>
                                    <span class="badge {{ $tx->wallet_type == 'main' ? 'badge-main' : 'badge-cashback' }}">
                                        {{ ucfirst($tx->wallet_type) }}
                                    </span>
                                </td>
                                <td>{{ $tx->description }}</td>
                                <td class="text-end {{ $tx->amount < 0 ? 'text-danger' : 'text-success' }}">
                                    ₱{{ number_format($tx->amount, 2) }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center text-muted py-3">No transactions found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Mobile Card Layout --}}
            <div class="d-block d-md-none">
                @forelse ($transactions as $tx)
                    <div class="mobile-transaction-card p-3 mb-2">
                        <div class="d-flex justify-content-between align-items-center">
                            <small class="text-muted">{{ \Carbon\Carbon::parse($tx->created_at)->format('Y-m-d H:i') }}</small>
                            <span class="badge {{ $tx->wallet_type == 'main' ? 'badge-main' : 'badge-cashback' }}">
                                {{ ucfirst($tx->wallet_type) }}
                            </span>
                        </div>
                        <div class="fw-semibold mt-2">{{ $tx->description }}</div>
                        <div class="text-end mt-1 {{ $tx->amount < 0 ? 'text-danger' : 'text-success' }}">
                            ₱{{ number_format($tx->amount, 2) }}
                        </div>
                    </div>
                @empty
                    <p class="text-muted text-center">No transactions found.</p>
                @endforelse
            </div>

            {{-- Pagination --}}
            <div class="mt-3">
                {{ $transactions->links() }}
            </div>
        </div>
    </div>
</div>
@endsection

@section('js')
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
@if(session('success'))
<script>
    document.addEventListener('DOMContentLoaded', function () {
        toastr.success("{{ session('success') }}", "Success", { timeOut: 3000, progressBar: true });
    });
</script>
@endif
@if(session('error'))
<script>
    document.addEventListener('DOMContentLoaded', function () {
        toastr.error("{{ session('error') }}", "Error", { timeOut: 5000, progressBar: true });
    });
</script>
@endif
@endsection

@include('partials.mobile-footer')
