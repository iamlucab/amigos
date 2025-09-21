@extends('adminlte::page')

@section('title', 'Loan Payment Schedule')

@section('content_header')
    <h1>Loan Payment Breakdown</h1>
@stop

@section('content')
<!-- 🧾 Loan Info Summary -->
<div class="card">
    <div class="card-header">
        <strong>Member:</strong> {{ $loan->member->full_name }}<br>
        <strong>Loan Amount:</strong> ₱{{ number_format($loan->amount, 2) }}<br>
        <strong>Term:</strong> {{ $loan->term_months }} months<br>
        <strong>Interest Rate:</strong> {{ $loan->interest_rate }}%<br>
        <strong>Total Payable:</strong> ₱{{ number_format($loan->amount * (1 + $loan->interest_rate / 100), 2) }}<br>
        <strong>Monthly Payment:</strong> ₱{{ number_format($loan->monthly_due, 2) }}
    </div>
</div>

<!-- 📅 Payment Schedule Table -->
<div class="card mt-3">
    <div class="card-header">
        Payment Schedule
        
    </div>

    <div class="card-body table-responsive p-0">
        <table class="table table-hover text-sm">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Due Date</th>
                    <th>Monthly Due</th>
                    <th>Paid Amount</th>
                    <th>Balance</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $startDate = \Carbon\Carbon::parse($loan->created_at)->startOfMonth();
                @endphp

                @for ($i = 1; $i <= $loan->term_months; $i++)
                    @php
                        $dueDate = $startDate->copy()->addMonths($i);
                        $monthlyDue = $loan->monthly_due;
                        $payments = $loan->payments->filter(function($p) use ($dueDate) {
                            return \Carbon\Carbon::parse($p->paid_at)->month === $dueDate->month &&
                                   \Carbon\Carbon::parse($p->paid_at)->year === $dueDate->year;
                        });

                        $paid = $payments->sum('amount');
                        $balance = max(0, $monthlyDue - $paid);

                        $status = 'Unpaid';
                        if ($paid >= $monthlyDue) {
                            $status = 'Paid';
                        } elseif ($paid > 0) {
                            $status = 'Partial';
                        }
                    @endphp

                    <tr>
                        <td>{{ $i }}</td>
                        <td>{{ $dueDate->format('F Y') }}</td>
                        <td>₱{{ number_format($monthlyDue, 2) }}</td>
                        <td>₱{{ number_format($paid, 2) }}</td>
                        <td>₱{{ number_format($balance, 2) }}</td>
                       <td>
    <span class="badge 
        @if($status === 'Paid') badge-success
        @elseif($status === 'Partial') badge-warning
        @else badge-danger
        @endif">{{ $status }}</span>

    {{-- Per Month Manual Pay Button --}}
 @if($status !== 'Paid')
    <button 
        class="btn btn-sm btn-info"
        data-toggle="modal"
        data-target="#manualPaymentModal"
        data-amount="{{ $balance }}"
        data-date="{{ $dueDate->format('Y-m-d') }}"
        data-due="{{ $dueDate->format('Y-m-d') }}"
        title="Record Payment">
        <i class="bi bi-wallet2"></i>
    </button>
@endif

</td>
                    </tr>
                @endfor
            </tbody>
        </table>
    </div>
</div>
<!-- 📝 Manual Payment Modal -->
<div class="modal fade" id="manualPaymentModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <form action="{{ route('admin.payment.store') }}" method="POST">
            @csrf
            <input type="hidden" name="loan_id" value="{{ $loan->id }}">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Record a Manual Payment</h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>

                <div class="modal-body">
                    <label>Amount Paid</label>
                    <input type="number" step="0.01" name="amount" id="manualAmount" class="form-control" required>

                    <label class="mt-2">Paid Date</label>
                    <input type="date" name="paid_at" id="manualDate" class="form-control" value="{{ date('Y-m-d') }}">

                 <label class="mt-2">Due Date</label>
<input type="date" name="due_date" id="manualDueDate" class="form-control" readonly required>
                    <label class="mt-2">Reference / Notes</label>
                    <input type="text" name="note" class="form-control">
                </div>

                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Record Payment</button>
                </div>
            </div>
        </form>
    </div>
</div>


@stop
@section('js')
<script>
    $('#manualPaymentModal').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget);
        var amount = button.data('amount');
        var date = button.data('date');

        var modal = $(this);
        modal.find('#manualAmount').val(amount);
        modal.find('#manualDate').val(date);
        modal.find('#manualDueDate').val(date); // ✅ Sets the hidden input
    });
</script>

@endsection


@section('js')
    <script>
        @if(session('success'))
            toastr.success("{{ session('success') }}");
        @endif

        @if(session('error'))
            toastr.error("{{ session('error') }}");
        @endif

        @if(session('info'))
            toastr.info("{{ session('info') }}");
        @endif
    </script>
@endsection



@include('partials.footer')