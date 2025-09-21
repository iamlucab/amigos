@extends('adminlte::page')

@section('title', 'Loan Approvals')
@section('css')
    <link rel="stylesheet" href="{{ asset('vendor/adminlte/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('vendor/adminlte/plugins/datatables-responsive/css/responsive.bootstrap4.min.css') }}">
    <style>
        @media (max-width: 768px) {
            table.dataTable.dtr-inline.collapsed > tbody > tr > td:first-child::before {
                top: 14px;
                left: 6px;
            }

            table.dataTable td {
                display: block;
                width: 100%;
                text-align: left !important;
                border-bottom: 1px solid #dee2e6;
                padding-left: 45% !important;
                position: relative;
            }

            table.dataTable td:before {
                content: attr(data-label);
                position: absolute;
                left: 10px;
                top: 10px;
                white-space: nowrap;
                font-weight: bold;
                color: #6c757d;
            }

            table.dataTable tr {
                margin-bottom: 1rem;
                border-bottom: 2px solid #ddd;
            }

            table thead {
                display: none;
            }
        }
    </style>
@stop

@section('content_header')
    <h1>Loan Requests</h1>
@stop

@section('content')
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

   <div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table id="loans-table" class="table table-bordered table-hover table-striped dt-responsive nowrap" style="width:100%">
                <thead class="thead-light">
                    <tr>
                        <th>Member</th>
                        <th>Amount</th>
                        <th>Purpose</th>
                        <th>Status</th>
                        <th>Requested</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($loans as $loan)
                        <tr>
                            <td data-label="Member">{{ $loan->member->full_name }}</td>
                            <td data-label="Amount">₱{{ number_format($loan->amount, 2) }}</td>
                            <td data-label="Purpose">{{ $loan->purpose }}</td>
                            <td data-label="Status">
                                <span class="badge bg-{{ $loan->status == 'Approved' ? 'success' : ($loan->status == 'Rejected' ? 'danger' : 'warning') }}">
                                    {{ $loan->status }}
                                </span>
                            </td>
                            <td data-label="Requested">{{ $loan->created_at->format('M d, Y') }}</td>
                            <td data-label="Actions">
                                @if($loan->status == 'Pending')
                                    <form action="{{ route('admin.loans.approve', $loan->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button class="btn btn-success btn-sm" title="Approve">
                                            <i class="bi bi-check"></i>
                                        </button>
                                    </form>
                                    <form action="{{ route('admin.loans.reject', $loan->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button class="btn btn-danger btn-sm" title="Reject">
                                            <i class="bi bi-x"></i>
                                        </button>
                                    </form>
                                @endif
                                <a href="{{ route('admin.loans.show', $loan->id) }}" class="btn btn-primary btn-sm mt-1" title="View">
                                    <i class="bi bi-eye"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="text-center">No loan requests.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@stop
@include('partials.mobile-footer')
@section('css')
    <link rel="stylesheet" href="{{ asset('vendor/adminlte/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('vendor/adminlte/plugins/datatables-responsive/css/responsive.bootstrap4.min.css') }}">
@stop

@section('js')
    <script src="{{ asset('vendor/adminlte/plugins/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('vendor/adminlte/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('vendor/adminlte/plugins/datatables-responsive/js/dataTables.responsive.min.js') }}"></script>
    <script src="{{ asset('vendor/adminlte/plugins/datatables-responsive/js/responsive.bootstrap4.min.js') }}"></script>

    <script>
        $(document).ready(function () {
            $('#loans-table').DataTable({
                responsive: true,
                autoWidth: false,
                ordering: true,
                pageLength: 10,
                language: {
                    searchPlaceholder: "Search loans...",
                    search: "_INPUT_"
                }
            });
        });
    </script>
@stop
