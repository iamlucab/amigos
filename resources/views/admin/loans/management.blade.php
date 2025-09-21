@extends('adminlte::page')

@section('title', 'Loan Management')

@section('content_header')
    <h1>Loan Management</h1>
@stop

@section('content')
    <!-- Quick Guide -->
    <div class="alert alert-info alert-dismissible">
        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
        <h5><i class="icon bi bi-info-circle"></i> Loan Approval Process</h5>
        <p>When you approve a loan request:</p>
        <ol>
            <li>The loan status changes to "Approved"</li>
            <li>The loan amount is automatically credited to the member's wallet</li>
            <li>A payment schedule is generated based on the loan term</li>
            <li>The member can then make payments according to the schedule</li>
        </ol>
    </div>
    <!-- Status Cards -->
    <div class="row">
        <div class="col-lg-3 col-6">
            <div class="small-box bg-warning">
                <div class="inner">
                    <h3>{{ $pendingCount }}</h3>
                    <p>Pending Requests</p>
                </div>
                <div class="icon">
                    <i class="bi bi-clock"></i>
                </div>
                <a href="{{ route('admin.loans.management', ['status' => 'Pending']) }}" class="small-box-footer">
                    View All <i class="bi bi-arrow-right-circle"></i>
                </a>
            </div>
        </div>
        
        <div class="col-lg-3 col-6">
            <div class="small-box bg-success">
                <div class="inner">
                    <h3>{{ $approvedCount }}</h3>
                    <p>Approved Loans</p>
                </div>
                <div class="icon">
                    <i class="bi bi-check-circle"></i>
                </div>
                <a href="{{ route('admin.loans.management', ['status' => 'Approved']) }}" class="small-box-footer">
                    View All <i class="bi bi-arrow-right-circle"></i>
                </a>
            </div>
        </div>
        
        <div class="col-lg-3 col-6">
            <div class="small-box bg-danger">
                <div class="inner">
                    <h3>{{ $rejectedCount }}</h3>
                    <p>Rejected Requests</p>
                </div>
                <div class="icon">
                    <i class="bi bi-x-circle"></i>
                </div>
                <a href="{{ route('admin.loans.management', ['status' => 'Rejected']) }}" class="small-box-footer">
                    View All <i class="bi bi-arrow-right-circle"></i>
                </a>
            </div>
        </div>
        
        <div class="col-lg-3 col-6">
            <div class="small-box bg-info">
                <div class="inner">
                    <h3><i class="bi bi-graph-up"></i></h3>
                    <p>Loan Reports</p>
                </div>
                <div class="icon">
                    <i class="bi bi-file-text"></i>
                </div>
                <a href="{{ route('admin.loans.reports') }}" class="small-box-footer">
                    View Reports <i class="bi bi-arrow-right-circle"></i>
                </a>
            </div>
        </div>
    </div>

    <!-- Filters Card -->
    <div class="card collapsed-card">
        <div class="card-header">
            <h3 class="card-title">
                <i class="bi bi-funnel mr-1"></i>
                Filter Options
            </h3>
            <div class="card-tools">
                <button type="button" class="btn btn-tool" data-card-widget="collapse">
                    <i class="bi bi-plus"></i>
                </button>
            </div>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.loans.management') }}" method="GET" class="row">
                <div class="col-md-3">
                    <div class="form-group">
                        <label>Status</label>
                        <select name="status" class="form-control">
                            <option value="">All Statuses</option>
                            <option value="Pending" {{ request('status') == 'Pending' ? 'selected' : '' }}>Pending</option>
                            <option value="Approved" {{ request('status') == 'Approved' ? 'selected' : '' }}>Approved</option>
                            <option value="Rejected" {{ request('status') == 'Rejected' ? 'selected' : '' }}>Rejected</option>
                            <option value="Cancelled" {{ request('status') == 'Cancelled' ? 'selected' : '' }}>Cancelled</option>
                        </select>
                    </div>
                </div>
                
                <div class="col-md-3">
                    <div class="form-group">
                        <label>Member Name</label>
                        <input type="text" name="member_name" class="form-control" value="{{ request('member_name') }}" placeholder="Search by name">
                    </div>
                </div>
                
                <div class="col-md-3">
                    <div class="form-group">
                        <label>Date From</label>
                        <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}">
                    </div>
                </div>
                
                <div class="col-md-3">
                    <div class="form-group">
                        <label>Date To</label>
                        <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}">
                    </div>
                </div>
                
                <div class="col-12 text-right">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-search mr-1"></i> Apply Filters
                    </button>
                    <a href="{{ route('admin.loans.management') }}" class="btn btn-default">
                        <i class="bi bi-arrow-counterclockwise mr-1"></i> Reset
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Loans Table -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Loan Requests</h3>
        </div>
        <div class="card-body table-responsive p-0">
            <table class="table table-hover text-nowrap">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Member</th>
                        <th>Amount</th>
                        <th>Term</th>
                        <th>Purpose</th>
                        <th>Status</th>
                        <th>Requested</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($loans as $loan)
                        <tr>
                            <td>{{ $loan->id }}</td>
                            <td>{{ $loan->member->full_name }}</td>
                            <td>₱{{ number_format($loan->amount, 2) }}</td>
                            <td>{{ $loan->term_months }} months</td>
                            <td>{{ $loan->purpose ?? '-' }}</td>
                            <td>
                                <span class="badge bg-{{ 
                                    $loan->status == 'Approved' ? 'success' : 
                                    ($loan->status == 'Rejected' ? 'danger' : 
                                    ($loan->status == 'Cancelled' ? 'secondary' : 'warning')) 
                                }}">
                                    {{ $loan->status }}
                                </span>
                            </td>
                            <td>{{ $loan->created_at->format('M d, Y') }}</td>
                            <td>
                                <div class="btn-group">
                                    <a href="{{ route('admin.loans.show', $loan->id) }}" class="btn btn-sm btn-info">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    
                                    @if($loan->status == 'Pending')
                                        <button type="button" class="btn btn-sm btn-success" data-toggle="modal" data-target="#approveModal{{ $loan->id }}">
                                            <i class="bi bi-check"></i>
                                        </button>
                                        
                                        <button type="button" class="btn btn-sm btn-danger" data-toggle="modal" data-target="#rejectModal{{ $loan->id }}">
                                            <i class="bi bi-x"></i>
                                        </button>
                                    @endif
                                </div>
                                
                                <!-- Approve Modal -->
                                <div class="modal fade" id="approveModal{{ $loan->id }}" tabindex="-1" role="dialog" aria-hidden="true">
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
                                <div class="modal fade" id="rejectModal{{ $loan->id }}" tabindex="-1" role="dialog" aria-hidden="true">
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
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center">No loan requests found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-footer clearfix">
            {{ $loans->appends(request()->query())->links() }}
        </div>
    </div>
@stop

@section('css')
    <link rel="stylesheet" href="/css/admin_custom.css">
@stop

@section('js')
    <script>
        $(function () {
            // Initialize tooltips
            $('[data-toggle="tooltip"]').tooltip();
            
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