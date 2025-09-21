@extends('adminlte::page')

@section('title', 'Loan Details')

@section('content_header')
    <h1>
        Loan Details
        <a href="{{ route('admin.loans.management') }}" class="btn btn-sm btn-secondary float-right">
            <i class="bi bi-arrow-left mr-1"></i> Back to List
        </a>
    </h1>
@stop

@section('content')
    <div class="row">
        <!-- Loan Information -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Loan Information</h3>
                </div>
                <div class="card-body">
                    <table class="table table-bordered">
                        <tr>
                            <th style="width: 40%">Loan ID</th>
                            <td>{{ $loan->id }}</td>
                        </tr>
                        <tr>
                            <th>Status</th>
                            <td>
                                <span class="badge bg-{{ 
                                    $loan->status == 'Approved' ? 'success' : 
                                    ($loan->status == 'Rejected' ? 'danger' : 
                                    ($loan->status == 'Cancelled' ? 'secondary' : 'warning')) 
                                }}">
                                    {{ $loan->status }}
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <th>Amount</th>
                            <td>₱{{ number_format($loan->amount, 2) }}</td>
                        </tr>
                        <tr>
                            <th>Interest Rate</th>
                            <td>{{ $loan->interest_rate }}%</td>
                        </tr>
                        <tr>
                            <th>Term</th>
                            <td>{{ $loan->term_months }} months</td>
                        </tr>
                        <tr>
                            <th>Monthly Payment</th>
                            <td>₱{{ number_format($loan->monthly_payment, 2) }}</td>
                        </tr>
                        <tr>
                            <th>Total Payable</th>
                            <td>₱{{ number_format($loan->amount * (1 + $loan->interest_rate / 100), 2) }}</td>
                        </tr>
                        <tr>
                            <th>Purpose</th>
                            <td>{{ $loan->purpose ?? 'Not specified' }}</td>
                        </tr>
                        <tr>
                            <th>Requested Date</th>
                            <td>{{ $loan->created_at->format('M d, Y h:i A') }}</td>
                        </tr>
                        @if($loan->status == 'Approved')
                        <tr>
                            <th>Approval Date</th>
                            <td>{{ $loan->approved_at ? Carbon\Carbon::parse($loan->approved_at)->format('M d, Y h:i A') : 'N/A' }}</td>
                        </tr>
                        @elseif($loan->status == 'Rejected')
                        <tr>
                            <th>Rejection Date</th>
                            <td>{{ $loan->rejected_at ? Carbon\Carbon::parse($loan->rejected_at)->format('M d, Y h:i A') : 'N/A' }}</td>
                        </tr>
                        <tr>
                            <th>Rejection Reason</th>
                            <td>{{ $loan->rejection_reason ?? 'No reason provided' }}</td>
                        </tr>
                        @endif
                    </table>
                </div>
            </div>
        </div>
        
        <!-- Member Information -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Member Information</h3>
                </div>
                <div class="card-body">
                    <table class="table table-bordered">
                        <tr>
                            <th style="width: 40%">Member ID</th>
                            <td>{{ $loan->member->id }}</td>
                        </tr>
                        <tr>
                            <th>Full Name</th>
                            <td>{{ $loan->member->full_name }}</td>
                        </tr>
                        <tr>
                            <th>Mobile Number</th>
                            <td>{{ $loan->member->mobile_number }}</td>
                        </tr>
                        <tr>
                            <th>Email</th>
                            <td>{{ $loan->member->user->email ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <th>Wallet Balance</th>
                            <td>₱{{ number_format($loan->member->wallet->balance ?? 0, 2) }}</td>
                        </tr>
                    </table>
                </div>
            </div>
            
            <!-- Action Buttons -->
            @if($loan->status == 'Pending')
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Actions</h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-6">
                            <button type="button" class="btn btn-success btn-block" data-toggle="modal" data-target="#approveModal">
                                <i class="bi bi-check mr-1"></i> Approve Loan
                            </button>
                        </div>
                        <div class="col-6">
                            <button type="button" class="btn btn-danger btn-block" data-toggle="modal" data-target="#rejectModal">
                                <i class="bi bi-x mr-1"></i> Reject Loan
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
    
    <!-- Payment Schedule -->
    @if($loan->status == 'Approved')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Payment Schedule</h3>
        </div>
        <div class="card-body table-responsive p-0">
            <table class="table table-hover text-nowrap">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Due Date</th>
                        <th>Amount</th>
                        <th>Status</th>
                        <th>Payment Method</th>
                        <th>Verification Status</th>
                        <th>Paid Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $totalPaid = 0;
                        $totalDue = $loan->amount * (1 + $loan->interest_rate / 100);
                    @endphp
                    
                    @forelse($loan->payments as $index => $payment)
                        @php
                            $totalPaid += $payment->is_paid ? $payment->amount : 0;
                            $isPastDue = !$payment->is_paid && $payment->due_date < now();
                        @endphp
                        <tr class="{{ $isPastDue ? 'table-danger' : '' }}">
                            <td>{{ $index + 1 }}</td>
                            <td>{{ \Carbon\Carbon::parse($payment->due_date)->format('M d, Y') }}</td>
                            <td>₱{{ number_format($payment->amount, 2) }}</td>
                            <td>
                                @if($payment->is_paid)
                                    <span class="badge bg-success">Paid</span>
                                @else
                                    <span class="badge bg-{{ $isPastDue ? 'danger' : 'warning' }}">
                                        {{ $isPastDue ? 'Overdue' : 'Pending' }}
                                    </span>
                                @endif
                            </td>
                            <td>{{ $payment->payment_method ?? 'N/A' }}</td>
                            <td>
                                @if($payment->is_paid)
                                    @if($payment->is_verified)
                                        <span class="badge bg-success">Verified</span>
                                    @else
                                        <span class="badge bg-warning">Pending Verification</span>
                                    @endif
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                            <td>{{ $payment->paid_at ? \Carbon\Carbon::parse($payment->paid_at)->format('M d, Y') : '—' }}</td>
                            <td>
                                @if(!$payment->is_paid)
                                    <button type="button" class="btn btn-sm btn-primary" data-toggle="modal" data-target="#paymentModal{{ $payment->id }}">
                                        <i class="bi bi-cash-wave mr-1"></i> Record Payment
                                    </button>
                                @elseif($payment->is_paid && !$payment->is_verified && $payment->payment_method != 'Wallet')
                                    <button type="button" class="btn btn-sm btn-success" data-toggle="modal" data-target="#verifyModal{{ $payment->id }}">
                                        <i class="bi bi-check-circle mr-1"></i> Verify Payment
                                    </button>
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                                
                                <!-- Payment Modal -->
                                <div class="modal fade" id="paymentModal{{ $payment->id }}" tabindex="-1" role="dialog" aria-hidden="true">
                                    <div class="modal-dialog" role="document">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title">Record Payment</h5>
                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                    <span aria-hidden="true">&times;</span>
                                                </button>
                                            </div>
                                            <form action="{{ route('admin.payment.store') }}" method="POST" enctype="multipart/form-data">
                                                @csrf
                                                <input type="hidden" name="payment_id" value="{{ $payment->id }}">
                                                <div class="modal-body">
                                                    <div class="form-group">
                                                        <label>Amount</label>
                                                        <input type="number" step="0.01" name="amount" class="form-control" value="{{ $payment->amount }}" required>
                                                    </div>
                                                    
                                                    <div class="form-group">
                                                        <label>Payment Method</label>
                                                        <select name="payment_method" class="form-control" required>
                                                            <option value="Wallet">Wallet</option>
                                                            <option value="GCash">GCash</option>
                                                            <option value="Bank">Bank Transfer</option>
                                                            <option value="Others">Others</option>
                                                        </select>
                                                    </div>
                                                    
                                                    <div class="form-group payment-details" style="display: none;">
                                                        <label>Reference Number</label>
                                                        <input type="text" name="reference_number" class="form-control">
                                                    </div>
                                                    
                                                    <div class="form-group payment-details" style="display: none;">
                                                        <label>Payment Proof</label>
                                                        <input type="file" name="payment_proof" class="form-control-file">
                                                        <small class="form-text text-muted">Upload a screenshot or photo of the payment receipt.</small>
                                                    </div>
                                                    
                                                    <div class="form-group">
                                                        <label>Payment Date</label>
                                                        <input type="date" name="paid_at" class="form-control" value="{{ date('Y-m-d') }}" required>
                                                    </div>
                                                    
                                                    <div class="form-group">
                                                        <label>Notes</label>
                                                        <textarea name="notes" class="form-control" rows="2"></textarea>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                                                    <button type="submit" class="btn btn-primary">Record Payment</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Verification Modal -->
                                @if($payment->is_paid && !$payment->is_verified && $payment->payment_method != 'Wallet')
                                <div class="modal fade" id="verifyModal{{ $payment->id }}" tabindex="-1" role="dialog" aria-hidden="true">
                                    <div class="modal-dialog" role="document">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title">Verify Payment</h5>
                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                    <span aria-hidden="true">&times;</span>
                                                </button>
                                            </div>
                                            <div class="modal-body">
                                                <p><strong>Payment Method:</strong> {{ $payment->payment_method }}</p>
                                                <p><strong>Reference Number:</strong> {{ $payment->reference_number ?? 'N/A' }}</p>
                                                <p><strong>Amount:</strong> ₱{{ number_format($payment->amount, 2) }}</p>
                                                <p><strong>Paid Date:</strong> {{ \Carbon\Carbon::parse($payment->paid_at)->format('M d, Y') }}</p>
                                                
                                                @if($payment->payment_proof)
                                                <div class="form-group">
                                                    <label>Payment Proof:</label>
                                                    <div>
                                                        <img src="{{ asset('storage/' . $payment->payment_proof) }}" class="img-fluid" style="max-height: 300px;">
                                                    </div>
                                                </div>
                                                @endif
                                                
                                                <div class="alert alert-warning">
                                                    <i class="bi bi-exclamation-triangle mr-1"></i>
                                                    Please verify that the payment has been received before approving.
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <form action="{{ route('admin.payment.verify', $payment->id) }}" method="POST">
                                                    @csrf
                                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                                                    <button type="submit" class="btn btn-success">Verify Payment</button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center">No payment schedule available.</td>
                        </tr>
                    @endforelse
                </tbody>
                <tfoot>
                    <tr class="bg-light">
                        <th colspan="2">Summary</th>
                        <th>₱{{ number_format($totalDue, 2) }}</th>
                        <th colspan="2">
                            Total Paid: ₱{{ number_format($totalPaid, 2) }}
                        </th>
                        <th colspan="3">
                            Remaining: ₱{{ number_format($totalDue - $totalPaid, 2) }}
                        </th>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
    @endif
    
    <!-- Approve Modal -->
    @if($loan->status == 'Pending')
    <div class="modal fade" id="approveModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Approve Loan Request</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to approve this loan request?</p>
                    <p><strong>Member:</strong> {{ $loan->member->full_name }}</p>
                    <p><strong>Amount:</strong> ₱{{ number_format($loan->amount, 2) }}</p>
                    <p><strong>Term:</strong> {{ $loan->term_months }} months</p>
                    <p><strong>Monthly Payment:</strong> ₱{{ number_format($loan->monthly_due, 2) }}</p>
                    
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle mr-1"></i>
                        Approving this loan will credit the amount to the member's wallet and generate a payment schedule.
                    </div>
                </div>
                <div class="modal-footer">
                    <form action="{{ route('admin.loans.approve', $loan->id) }}" method="POST">
                        @csrf
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-success">Approve Loan</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Reject Modal -->
    <div class="modal fade" id="rejectModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Reject Loan Request</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="{{ route('admin.loans.reject', $loan->id) }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <p>Are you sure you want to reject this loan request?</p>
                        <p><strong>Member:</strong> {{ $loan->member->full_name }}</p>
                        <p><strong>Amount:</strong> ₱{{ number_format($loan->amount, 2) }}</p>
                        
                        <div class="form-group">
                            <label for="rejection_reason">Reason for Rejection</label>
                            <textarea name="rejection_reason" id="rejection_reason" class="form-control" rows="3" required></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-danger">Reject Loan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endif
@stop

@section('css')
    <link rel="stylesheet" href="/css/admin_custom.css">
@stop

@section('js')
    <script>
        $(function () {
            // Show/hide payment details based on payment method
            $('select[name="payment_method"]').on('change', function() {
                if ($(this).val() === 'Wallet') {
                    $('.payment-details').hide();
                } else {
                    $('.payment-details').show();
                }
            });
            
            // Flash messages
            @if(session('success'))
                toastr.success("{{ session('success') }}");
            @endif
            
            @if(session('error'))
                toastr.error("{{ session('error') }}");
            @endif
        });
    </script>
@stop