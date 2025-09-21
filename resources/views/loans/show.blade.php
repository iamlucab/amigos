@extends('adminlte::page')

@section('title', 'Loan Details')

@section('content_header')
    <h1>Loan Breakdown</h1>
@endsection

@section('content')
<div class="card">
    <div class="card-body">
        <h5><strong>Loan Amount:</strong> ₱{{ number_format($loan->amount, 2) }}</h5>
        <p><strong>Purpose:</strong> {{ $loan->purpose ?? '—' }}</p>
        <p><strong>Status:</strong> 
            @if($loan->status === 'Approved')
                <span class="badge badge-success">Approved</span>
            @elseif($loan->status === 'Rejected')
                <span class="badge badge-danger">Rejected</span>
            @elseif($loan->status === 'Cancelled')
                <span class="badge badge-secondary">Cancelled</span>
            @else
                <span class="badge badge-warning">Pending</span>
            @endif
        </p>
        <p><strong>Term:</strong> {{ $loan->term_months }} months</p>
        <p><strong>Interest:</strong> {{ $loan->interest_rate }}%</p>
        <p><strong>Total Payable:</strong> ₱{{ number_format($loan->amount * (1 + $loan->interest_rate / 100), 2) }}</p>
        <p><strong>Monthly Due:</strong> ₱{{ number_format($loan->monthly_payment, 2) }}</p>
    </div>
</div>

@if($loan->status === 'Approved')
    <div class="card mt-3">
        <div class="card-header">
            <h5 class="mb-0">Installment Tracker</h5>
        </div>
        <div class="card-body p-0">
            <table class="table table-striped mb-0">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Due Date</th>
                        <th>Amount</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($loan->payments as $payment)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ \Carbon\Carbon::parse($payment->due_date)->format('M d, Y') }}</td>
                            <td>₱{{ number_format($payment->amount, 2) }}</td>
                            <td>
                                @if($payment->is_paid)
                                    <span class="badge badge-success">Paid</span>
                                    @if($payment->payment_method && $payment->payment_method != 'Wallet' && !$payment->is_verified)
                                        <span class="badge badge-warning">Pending Verification</span>
                                    @endif
                                @else
                                    @if($payment->due_date < now())
                                        <span class="badge badge-danger">Overdue</span>
                                    @else
                                        <span class="badge badge-warning">Pending</span>
                                    @endif
                                @endif
                            </td>
                            <td>
                                @if(!$payment->is_paid)
                                    <button class="btn btn-sm btn-primary" data-toggle="modal" data-target="#paymentModal{{ $payment->id }}">
                                        <i class="bi bi-cash-wave mr-1"></i> Pay Now
                                    </button>
                                    
                                    @include('loans.payment-modal', ['payment' => $payment])
                                @elseif($payment->payment_method && !$payment->is_verified && $payment->payment_method != 'Wallet')
                                    <span class="badge badge-info">
                                        <i class="bi bi-clock mr-1"></i> Awaiting Verification
                                    </span>
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="text-center">No payment records.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endif
@endsection
