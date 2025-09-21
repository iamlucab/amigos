@extends('adminlte::page')

@section('title', 'Reward Winners')

@section('content_header')
    <h5>Reward Winners</h5>
@stop

@section('content')
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="card">
        <div class="card-header">
            <strong>Winners History</strong>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover table-striped reward-winners-table">
                    <thead class="d-none d-md-table-header-group">
                        <tr>
                            <th>Program Title</th>
                            <th>Prize</th>
                            <th>Winner</th>
                            <th>Date of Draw</th>
                            <th>Marked Ineligible Until</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($winners as $winner)
                            <tr>
                                <td data-label="Program Title">{{ optional($winner->program)->title ?? 'N/A' }}</td>
                                <td data-label="Prize">{{ optional($winner->program)->description ?? 'N/A' }}</td>
                                <td data-label="Winner">{{ optional($winner->member)->full_name ?? 'N/A' }}</td>
                                <td data-label="Date of Draw">{{ $winner->created_at->format('F d, Y') }}</td>
                                <td data-label="Ineligible Until">{{ \Carbon\Carbon::parse($winner->created_at)->addMonths(3)->format('F d, Y') }}</td>
                                <td data-label="Status">
                                    <form action="{{ route('admin.rewards.status.update', $winner->id) }}" method="POST">
                                        @csrf
                                        @method('PATCH')
                                        <select name="status" class="form-control form-control-sm" onchange="this.form.submit()">
                                            <option value="unclaimed" {{ $winner->status === 'unclaimed' ? 'selected' : '' }}>Unclaimed</option>
                                            <option value="redeemed" {{ $winner->status === 'redeemed' ? 'selected' : '' }}>Redeemed</option>
                                            <option value="expired" {{ $winner->status === 'expired' ? 'selected' : '' }}>Expired</option>
                                        </select>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted">No winners recorded yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@stop
@include('partials.mobile-footer')
@push('css')
<style>
    @media (max-width: 768px) {
        .reward-winners-table thead {
            display: none;
        }
        .reward-winners-table tr {
            display: block;
            margin-bottom: 1rem;
            border: 1px solid #dee2e6;
            border-radius: 0.5rem;
            padding: 0.5rem;
        }
        .reward-winners-table td {
            display: block;
            text-align: right;
            padding-left: 50%;
            position: relative;
            border: none;
            border-bottom: 1px solid #e9ecef;
        }
        .reward-winners-table td::before {
            content: attr(data-label);
            position: absolute;
            left: 0;
            top: 0;
            width: 50%;
            padding-left: 15px;
            font-weight: bold;
            text-align: left;
            white-space: nowrap;
        }
    }
</style>
@endpush
