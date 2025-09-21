@extends('adminlte::page')

@section('title', 'Payment History')

@section('content_header')
    <h1>Payment History</h1>
@endsection

@section('content')
    <div class="card">
        <div class="card-body table-responsive">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Loan Amount</th>
                        <th>Due Date</th>
                        <th>Amount</th>
                        <th>Status</th>
                        <th>Paid At</th>
                        <th>Note</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($payments as $payment)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>₱{{ number_format($payment->loan->amount, 2) }}</td>
                            <td>{{ \Carbon\Carbon::parse($payment->due_date)->format('M d, Y') }}</td>
                            <td>₱{{ number_format($payment->amount, 2) }}</td>
                            <td>
                                @if($payment->is_paid)
                                    <span class="badge badge-success">Paid</span>
                                @else
                                    <span class="badge badge-warning">Pending</span>
                                @endif
                            </td>
                            <td>{{ $payment->paid_at ? \Carbon\Carbon::parse($payment->paid_at)->format('M d, Y') : '—' }}</td>
                            <td>{{ $payment->note ?? '—' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted">No payment history available.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
