@extends('adminlte::page')

@section('title', 'Loan History')

@section('content_header')
    <h5>Loan History</h5>
@endsection

@section('content')
    <div class="card d-none d-md-block">
        <div class="card-body table-responsive">
            <table id="loanHistoryTable" class="table table-bordered table-striped table-sm">
                <thead class="thead-light">
                    <tr>
                        <th>#</th>
                        <th>Amount</th>
                        <th>Purpose</th>
                        <th>Term</th>
                        <th>Monthly Due</th>
                        <th>Status</th>
                        <th>Requested At</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($loans as $loan)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>₱{{ number_format($loan->amount, 2) }}</td>
                            <td>{{ $loan->purpose ?? '-' }}</td>
                            <td>{{ $loan->term_months }} months</td>
                            <td>₱{{ number_format($loan->monthly_due, 2) }}</td>
                            <td>
                                @if($loan->status === 'Approved')
                                    <span class="badge badge-success">Approved</span>
                                @elseif($loan->status === 'Rejected')
                                    <span class="badge badge-danger">Rejected</span>
                                @elseif($loan->status === 'Cancelled')
                                    <span class="badge badge-secondary">Cancelled</span>
                                @else
                                    <span class="badge badge-warning">Pending</span>
                                @endif
                            </td>
                            <td>{{ $loan->created_at->format('M d, Y h:i A') }}</td>
                            <td>
                                @if($loan->status === 'Pending')
                                    <form action="{{ route('loans.cancel', $loan->id) }}" method="POST" onsubmit="return confirm('Cancel this request?')" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-sm btn-danger">
                                            <i class="bi bi-x"></i> Cancel
                                        </button>
                                    </form>
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    {{-- Mobile Card View --}}
    <div class="d-md-none">
        @forelse($loans as $loan)
            <div class="card mb-3 shadow-sm">
                <div class="card-body p-3">
                    <h5 class="mb-1">₱{{ number_format($loan->amount, 2) }}</h5>
                    <p class="mb-1"><strong>Purpose:</strong> {{ $loan->purpose ?? '-' }}</p>
                    <p class="mb-1"><strong>Term:</strong> {{ $loan->term_months }} months</p>
                    <p class="mb-1"><strong>Monthly Due:</strong> ₱{{ number_format($loan->monthly_due, 2) }}</p>
                    <p class="mb-1">
                        <strong>Status:</strong>
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
                    <p class="mb-1"><strong>Requested:</strong> {{ $loan->created_at->format('M d, Y h:i A') }}</p>
                    @if($loan->status === 'Pending')
                        <form action="{{ route('loans.cancel', $loan->id) }}" method="POST" onsubmit="return confirm('Cancel this request?')">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-sm btn-danger w-100 mt-2">
                                <i class="bi bi-x"></i> Cancel
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        @empty
            <div class="alert alert-info text-center">No loan requests yet.</div>
        @endforelse
    </div>
@endsection


@include('partials.mobile-footer')
@section('css')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap4.min.css">
@endsection

@section('js')
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap4.min.js"></script>
    <script>
        $(function () {
            $('#loanHistoryTable').DataTable({
                responsive: false,
                autoWidth: false,
                order: [[6, 'desc']]
            });
        });
    </script>
@endsection
