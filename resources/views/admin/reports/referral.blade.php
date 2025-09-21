@extends('adminlte::page')

@section('title', 'Referral Bonus Report')

@section('content_header')
    <h5>Referral Bonus Report</h5>
@stop

@section('content')
<div class="container-fluid">
    <div class="mb-3">
        <form method="GET" action="{{ route('referral.report') }}" class="row g-2">
            <div class="col-md-3">
                <label>From:</label>
                <input type="date" name="from" class="form-control" value="{{ request('from') }}">
            </div>
            <div class="col-md-3">
                <label>To:</label>
                <input type="date" name="to" class="form-control" value="{{ request('to') }}">
            </div>
            <div class="col-md-3 align-self-end">
                <button type="submit" class="btn btn-primary">Filter</button>
                <a href="{{ route('referral.report') }}" class="btn btn-secondary text-white"> Reset</a>
            </div>
        </form>
    </div>

    <div class="card">
        <div class="card-body table-responsive">
            <table id="referralTable" class="table table-bordered table-striped">
                <thead style="background-color: #e3f2fd;">
                    <tr>
                        <th>#</th>
                        <th>Recipient</th>
                        <th>Bonus Level</th>
                        <th>Amount</th>
                        <th>Description</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $totals = ['Level 1' => 0, 'Level 2' => 0, 'Level 3' => 0];
                    @endphp
                    @foreach ($transactions as $index => $transaction)
                        @php
                            $level = '-';
                            if (str_contains($transaction->description, 'Direct')) {
                                $level = 'Level 1';
                                $totals['Level 1'] += $transaction->amount;
                            } elseif (str_contains($transaction->description, '2nd')) {
                                $level = 'Level 2';
                                $totals['Level 2'] += $transaction->amount;
                            } elseif (str_contains($transaction->description, '3rd')) {
                                $level = 'Level 3';
                                $totals['Level 3'] += $transaction->amount;
                            }
                        @endphp
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $transaction->wallet->member->full_name ?? 'Unknown' }}</td>
                            <td>{{ $level }}</td>
                            <td>₱{{ number_format($transaction->amount, 2) }}</td>
                            <td>{{ $transaction->description }}</td>
                            <td>{{ $transaction->created_at->format('M d, Y h:i A') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

 @php
    $totalBonus = $totals['Level 1'] + $totals['Level 2'] + $totals['Level 3'];
@endphp

<div class="mt-3">
    <h5><strong>Total Bonuses by Level:</strong></h5>
    <ul>
        <li>🎯 Level 1 (Direct): ₱{{ number_format($totals['Level 1'], 2) }}</li>
        <li>🎯 Level 2 (2nd): ₱{{ number_format($totals['Level 2'], 2) }}</li>
        <li>🎯 Level 3 (3rd): ₱{{ number_format($totals['Level 3'], 2) }}</li>
    </ul>
    <hr>
    <h5><strong>🧾 Total Bonus Disbursed: ₱{{ number_format($totalBonus, 2) }}</strong></h5>
</div>

</div>
@stop

@section('css')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
@stop

@section('js')
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
    <script>
        $(function () {
            $('#referralTable').DataTable({
                paging: true,
                searching: true,
                ordering: true,
                responsive: true,
            });
        });
    </script>
@stop

@include('partials.mobile-footer')