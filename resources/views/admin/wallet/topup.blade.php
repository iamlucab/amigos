@extends('adminlte::page')

@section('title', 'Wallet Top-up / Refund')

@section('content')<br>
<div class="container-fluid px-2">
    <div class="row justify-content-center">
        <div class="col-12 col-md-10 col-lg-8">

            {{-- Header --}}
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h4 class="mb-0"><i class="bi bi-wallet2 text-primary"></i> Wallet Top-up / Refund</h4>
            </div>

            {{-- Flash --}}
            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            @if($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- Search Form --}}
            <div class="card shadow rounded-3 mb-4">
                <div class="card-body">
                    <form method="GET" action="{{ route('admin.wallet.topup') }}">
                        <div class="form-group mb-3">
                            <label for="mobile_number" class="font-weight-bold">Search Member by Mobile #</label>
                            <input type="text" name="mobile_number" class="form-control form-control-lg" value="{{ request('mobile_number') }}" placeholder="e.g. 09171234567" required>
                        </div>
                        <button type="submit" class="btn btn-primary btn-block btn-lg">
                            🔍 Search Wallet
                        </button>
                    </form>
                </div>
            </div>

            {{-- Show form only if member is loaded --}}
            @if(isset($member) && $member)
            <div class="card shadow-sm rounded-3 mb-4">
                <div class="card-header bg-primary text-white">
                    <strong class="text-white" >Top-up / Refund for {{ $member->full_name }}</strong>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.wallet.topup.store') }}" onsubmit="return confirm('Confirm this wallet transaction?');">
                        @csrf
                        <input type="hidden" name="mobile_number" value="{{ $member->mobile_number }}">

                        <div class="form-group">
                            <label for="wallet_type">Wallet Type</label>
                            <select name="wallet_type" class="form-control" required>
                                <option value="main">Main Wallet</option>
                                <option value="cashback">Cashback Wallet</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="type">Transaction Type</label>
                            <select name="type" class="form-control" required>
                                <option value="topup">Top-up</option>
                                <option value="refund">Refund</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="amount">Amount (₱)</label>
                            <input type="number" step="0.01" min="1" name="amount" class="form-control form-control-lg" required>
                        </div>

                        <div class="form-group">
                            <label for="note">Note (Optional)</label>
                            <textarea name="note" rows="2" class="form-control" placeholder="Reason or remark..."></textarea>
                        </div>

                        <button type="submit" class="btn btn-success btn-block btn-lg">
                            💰 Submit Transaction
                        </button>
                    </form>
                </div>
            </div>
            @endif

            {{-- Recent Transactions --}}
            <div class="card shadow-sm rounded-3 mb-5">
                <div class="card-header bg-secondary text-white">
           <strong class="text-white"><i class="bi bi-clock-history"></i> Recent Wallet Transactions</strong>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-sm table-striped table-hover mb-0">
                            <thead class="thead-light">
                                <tr>
                                    <th>Date</th>
                                    <th>Member</th>
                                    <th>Type</th>
                                    <th>Amount</th>
                                    <th>Description</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($transactions ?? [] as $txn)
                                    <tr>
                                        <td>{{ $txn->created_at->format('Y-m-d H:i') }}</td>
                                        <td>{{ $txn->wallet->member->full_name ?? '-' }}</td>
                                        <td>
                                            <span class="badge badge-{{ $txn->type === 'debit' ? 'danger' : 'success' }}">
                                                {{ ucfirst($txn->type) }}
                                            </span>
                                        </td>
                                        <td>₱{{ number_format($txn->amount, 2) }}</td>
                                        <td>{{ $txn->description }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center text-muted">No recent transactions found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection
@include('partials.mobile-footer')