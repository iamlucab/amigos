@extends('adminlte::page')
@section('title', 'Referral Bonus Logs')
@section('content')<br>
<div class="container-fluid">
    <h5 class="mb-3">Referral Bonus Logs</h5>
<form method="GET" class="row mb-3 g-2">
    <div class="col-md-3">
        <input type="text" name="sponsor" value="{{ request('sponsor') }}" class="form-control" placeholder="Sponsor Name">
    </div>
    <div class="col-md-3">
        <input type="date" name="from" value="{{ request('from') }}" class="form-control" placeholder="From">
    </div>
    <div class="col-md-3">
        <input type="date" name="to" value="{{ request('to') }}" class="form-control" placeholder="To">
    </div>
    <div class="col-md-3">
        <button class="btn btn-primary"><i class="bi bi-search"></i> Filter</button>
        <a href="{{ route('admin.referral-bonuses') }}" class="btn btn-warning">Reset</a>
    </div>
</form>
<div class="mb-2">
    <strong>Total Bonus (Released to Dashboard):</strong>
    <span class="text-success">₱{{ number_format($totalBonusAmount, 2) }}</span>
</div>
    <div class="table-responsive">
       <table id="referralTable" class="table table-bordered table-striped">
            <thead class="table-light   ">
                <tr>
                    <th>Date</th>
                    <th>Level</th>
                    <th>Sponsor</th>
                    <th>Referred Member</th>
                    <th>Amount</th>
                    <th>Description</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($logs as $log)
                    <tr>
                        <td>{{ $log->created_at->format('Y-m-d H:i') }}</td>
                        <td>{{ $log->level }}</td>
                        <td>{{ $log->member->full_name ?? 'N/A' }}</td>
                        <td>{{ $log->referredMember->full_name ?? 'N/A' }}</td>
                        <td>₱{{ number_format($log->amount, 2) }}</td>
                        <td>{{ $log->description }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center">No referral logs found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>


    <h5 class="mt-4">Top 5 Earners</h5>
<ul class="list-group mb-4">
    @foreach($topEarners as $member)
        <li class="list-group-item d-flex justify-content-between align-items-center">
<span class="d-block text-start">
    {{ $member->full_name }} <br><span class="badge bg-info text-dark">Mobile No. {{ $member->mobile_number }}</span>
</span>            <span class="badge bg-success">₱{{ number_format($member->referral_bonus_logs_sum_amount, 2) }}</span>
        </li>
    @endforeach
</ul>
<a href="{{ route('admin.referral-bonuses.export') }}" class="btn btn-success text-white mb-3">
    <i class="bi bi-filetype-csv"></i> Export CSV
</a>
   </div>
@endsection

@include('partials.mobile-footer')

@section('js')
<script>
    $(function () {
        $('#referralTable').DataTable({
            responsive: true,
            pageLength: 10,
            ordering: true,
            order: [[0, 'desc']],
            language: {
                search: "Search:",
                paginate: { previous: "‹", next: "›" }
            }
        });
    });
</script>
@endsection
