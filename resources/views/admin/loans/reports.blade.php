@extends('adminlte::page')

@section('title', 'Loan Reports')

@section('content_header')
    <h1>Loan Reports</h1>
@stop

@section('content')
    <!-- Filters Card -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">
                <i class="bi bi-funnel mr-1"></i>
                Report Filters
            </h3>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.loans.reports') }}" method="GET" class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label>Date From</label>
                        <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}">
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="form-group">
                        <label>Date To</label>
                        <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}">
                    </div>
                </div>
                
                <div class="col-md-4 d-flex align-items-end">
                    <div class="form-group w-100">
                        <button type="submit" class="btn btn-primary btn-block">
                            <i class="bi bi-search mr-1"></i> Generate Report
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Summary Statistics -->
    <div class="row">
        <div class="col-md-4">
            <div class="info-box bg-info">
                <span class="info-box-icon"><i class="bi bi-cash-wave"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Total Loaned</span>
                    <span class="info-box-number">₱{{ number_format($totalLoaned, 2) }}</span>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="info-box bg-success">
                <span class="info-box-icon"><i class="bi bi-percent"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Total Interest</span>
                    <span class="info-box-number">₱{{ number_format($totalInterest, 2) }}</span>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="info-box bg-warning">
                <span class="info-box-icon"><i class="bi bi-coin"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Total Payable</span>
                    <span class="info-box-number">₱{{ number_format($totalPayable, 2) }}</span>
                </div>
            </div>
        </div>
        
        <div class="col-md-6">
            <div class="info-box bg-primary">
                <span class="info-box-icon"><i class="bi bi-check-circle"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Total Paid</span>
                    <span class="info-box-number">₱{{ number_format($totalPaid, 2) }}</span>
                </div>
            </div>
        </div>
        
        <div class="col-md-6">
            <div class="info-box bg-danger">
                <span class="info-box-icon"><i class="bi bi-exclamation-circle"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Total Outstanding</span>
                    <span class="info-box-number">₱{{ number_format($totalOutstanding, 2) }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Loans Table -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Approved Loans</h3>
            <div class="card-tools">
                <button type="button" class="btn btn-tool" data-card-widget="collapse">
                    <i class="bi bi-dash"></i>
                </button>
                <button type="button" class="btn btn-sm btn-primary" onclick="window.print()">
                    <i class="bi bi-printer mr-1"></i> Print Report
                </button>
            </div>
        </div>
        <div class="card-body table-responsive p-0">
            <table class="table table-hover text-nowrap">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Member</th>
                        <th>Amount</th>
                        <th>Interest</th>
                        <th>Term</th>
                        <th>Monthly</th>
                        <th>Paid</th>
                        <th>Outstanding</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($loans as $loan)
                        @php
                            $totalPayable = $loan->amount * (1 + ($loan->interest_rate / 100));
                            $totalPaid = $loan->payments->where('is_paid', true)->sum('amount');
                            $outstanding = $totalPayable - $totalPaid;
                            
                            // Calculate payment status
                            $paymentStatus = 'Current';
                            $latestPayment = $loan->payments->sortByDesc('due_date')->where('is_paid', false)->first();
                            
                            if ($latestPayment && $latestPayment->due_date < now()) {
                                $paymentStatus = 'Overdue';
                            }
                            
                            if ($outstanding <= 0) {
                                $paymentStatus = 'Paid';
                            }
                        @endphp
                        <tr>
                            <td>{{ $loan->id }}</td>
                            <td>{{ $loan->member->full_name }}</td>
                            <td>₱{{ number_format($loan->amount, 2) }}</td>
                            <td>₱{{ number_format($loan->amount * ($loan->interest_rate / 100), 2) }}</td>
                            <td>{{ $loan->term_months }} months</td>
                            <td>₱{{ number_format($loan->monthly_payment, 2) }}</td>
                            <td>₱{{ number_format($totalPaid, 2) }}</td>
                            <td>₱{{ number_format($outstanding, 2) }}</td>
                            <td>
                                <span class="badge bg-{{ 
                                    $paymentStatus == 'Paid' ? 'success' : 
                                    ($paymentStatus == 'Overdue' ? 'danger' : 'info') 
                                }}">
                                    {{ $paymentStatus }}
                                </span>
                            </td>
                            <td>
                                <a href="{{ route('admin.loans.show', $loan->id) }}" class="btn btn-sm btn-info">
                                    <i class="bi bi-eye"></i> Details
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="text-center">No approved loans found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@stop

@section('css')
    <link rel="stylesheet" href="/css/admin_custom.css">
    <style>
        @media print {
            .main-header, .main-sidebar, .card-tools, .card-header, form, .no-print {
                display: none !important;
            }
            
            .content-wrapper {
                margin-left: 0 !important;
                padding: 0 !important;
            }
            
            .card {
                box-shadow: none !important;
                border: none !important;
            }
            
            .card-body {
                padding: 0 !important;
            }
            
            body {
                padding: 2cm;
            }
            
            h1 {
                text-align: center;
                margin-bottom: 20px;
            }
        }
    </style>
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