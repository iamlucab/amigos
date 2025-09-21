@extends('adminlte::page')

@section('title', 'Members List')

{{-- E-Bili Theme Styling --}}
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
<link rel="stylesheet" href="{{ asset('css/ebili-theme.css') }}">

@section('content_header')
<div class="text-center mb-4 fade-in">
    <h2 class="fw-bold mb-2" style="color: var(--primary-purple);">
        <i class="bi bi-people me-2"></i>Members Management
    </h2>
    <p class="slogan mb-0" style="font-size: 0.9rem;">Manage Your E-Bili Community Members</p>
</div>
@stop

@section('content')
<div class="container-fluid">
    @if(session('success'))
        <div class="alert alert-success slide-up">
            <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
        </div>
    @endif

    <div class="text-center mb-4">
        <a href="{{ route('members.create') }}" class="btn btn-primary">
            <i class="bi bi-person-plus me-2"></i>Add New Member
        </a>
    </div>

    <div class="card fade-in">
        <div class="card-header">
            <h4 class="card-title text-white fw-bold mb-0">
                <i class="bi bi-list me-2"></i>Members Directory
            </h4>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table id="members-table" class="table dt-responsive nowrap" style="width:100%">
                    <thead>
                        <tr>
                            <th><i class="bi bi-hash me-1"></i>ID</th>
                            <th><i class="bi bi-person me-1"></i>Name</th>
                            <th><i class="bi bi-cake me-1"></i>Birthday</th>
                            <th><i class="bi bi-phone me-1"></i>Mobile</th>
                            <th><i class="bi bi-key me-1"></i>Codes Used</th>
                            <th><i class="bi bi-person-tag me-1"></i>Role</th>
                            <th><i class="bi bi-handshake me-1"></i>Sponsor</th>
                            <th><i class="bi bi-toggle-on me-1"></i>Status</th>
                            <th><i class="bi bi-gear-fill me-1"></i>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($members as $member)
                            <tr>
                                <td>
                                    <span class="badge bg-primary rounded-pill">{{ $member->id }}</span>
                                </td>
                                <td class="fw-bold" style="color: var(--primary-purple);">
                                    {{ $member->first_name }} {{ $member->middle_name }} {{ $member->last_name }}
                                </td>
                                <td>
                                    <small class="text-muted">{{ \Carbon\Carbon::parse($member->birthday)->format('M d, Y') }}</small>
                                </td>
                                <td>
                                    <span class="badge bg-info rounded-pill">{{ $member->mobile_number }}</span>
                                </td>
                                <td>
                                    @if($member->membershipCode)
                                        <span class="badge bg-primary rounded-pill">{{ $member->membershipCode->code }}</span>
                                    @else
                                        <small class="text-muted">No Code</small>
                                    @endif
                                </td>
                                <td>
                                    @if($member->role === 'Admin')
                                        <span class="badge bg-danger rounded-pill">{{ $member->role }}</span>
                                    @elseif($member->role === 'Staff')
                                        <span class="badge bg-warning rounded-pill">{{ $member->role }}</span>
                                    @else
                                        <span class="badge bg-success rounded-pill">{{ $member->role }}</span>
                                    @endif
                                </td>
                                <td>
                                    @if($member->sponsor)
                                        <small class="text-muted">{{ $member->sponsor->first_name }} {{ $member->sponsor->last_name }}</small>
                                    @else
                                        <small class="text-muted">No Sponsor</small>
                                    @endif
                                </td>
                                <td>
    @if($member->status === 'Active')
        <span class="badge bg-success">{{ $member->status }}</span>
    @elseif($member->status === 'Pending')
        <span class="badge bg-warning text-dark">{{ $member->status }}</span>
    @else
        <span class="badge bg-secondary">{{ $member->status }}</span>
    @endif
</td>
                                <td>
                                    <a href="{{ route('members.edit', $member->id) }}"
                                       class="btn btn-sm btn-outline-primary me-1"
                                       title="Edit Member">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    {{-- Uncomment if delete functionality is needed
                                    <form action="{{ route('members.destroy', $member->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this member?');">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-sm btn-outline-danger" title="Delete Member">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                    --}}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

{{-- 📱 Reusable Mobile Footer --}}
@include('partials.reusable-mobile-footer')

@stop

@section('css')
    {{-- DataTables & Responsive Plugin CSS --}}
    <link rel="stylesheet" href="{{ asset('vendor/adminlte/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('vendor/adminlte/plugins/datatables-responsive/css/responsive.bootstrap4.min.css') }}">
    <style>
        /* Enhanced DataTables Styling */
        table.dataTable td {
            vertical-align: middle;
            font-family: 'Poppins', sans-serif;
        }

        .dataTables_wrapper .dataTables_length select,
        .dataTables_wrapper .dataTables_filter input {
            border-radius: 10px;
            border: 2px solid rgba(111, 66, 193, 0.2);
            font-family: 'Poppins', sans-serif;
        }

        .dataTables_wrapper .dataTables_filter input:focus {
            border-color: var(--primary-purple);
            box-shadow: 0 0 0 0.2rem rgba(111, 66, 193, 0.25);
        }

        .dataTables_wrapper .dataTables_paginate .paginate_button {
            border-radius: 10px;
            margin: 0 2px;
        }

        .dataTables_wrapper .dataTables_paginate .paginate_button.current {
            background: linear-gradient(135deg, var(--primary-purple) 0%, var(--secondary-purple) 100%) !important;
            border-color: var(--primary-purple) !important;
            color: white !important;
        }

        .dataTables_wrapper .dataTables_info {
            font-family: 'Poppins', sans-serif;
            color: var(--primary-purple);
            font-weight: 500;
        }

        /* Mobile responsiveness */
        @media (max-width: 768px) {
            .dataTables_wrapper .dataTables_length,
            .dataTables_wrapper .dataTables_filter {
                text-align: left;
                margin-bottom: 1rem;
            }
            
            .table-responsive {
                border-radius: 15px;
            }
        }
    </style>
@stop

@section('js')
    {{-- DataTables & Responsive Plugin JS --}}
    <script src="{{ asset('vendor/adminlte/plugins/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('vendor/adminlte/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('vendor/adminlte/plugins/datatables-responsive/js/dataTables.responsive.min.js') }}"></script>
    <script src="{{ asset('vendor/adminlte/plugins/datatables-responsive/js/responsive.bootstrap4.min.js') }}"></script>

    <script>
        $(document).ready(function () {
            $('#members-table').DataTable({
                responsive: true,
                autoWidth: false,
                pageLength: 10,
                lengthChange: true,
                order: [[0, 'desc']], // Sort by ID descending (newest first)
                language: {
                    search: "_INPUT_",
                    searchPlaceholder: "🔍 Search members...",
                    lengthMenu: "Show _MENU_ members per page",
                    info: "Showing _START_ to _END_ of _TOTAL_ members",
                    infoEmpty: "No members found",
                    infoFiltered: "(filtered from _MAX_ total members)",
                    paginate: {
                        first: "First",
                        last: "Last",
                        next: "Next",
                        previous: "Previous"
                    }
                },
                dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>' +
                     '<"row"<"col-sm-12"tr>>' +
                     '<"row"<"col-sm-12 col-md-5"i><"col-sm-12 col-md-7"p>>',
                drawCallback: function() {
                    // Add fade-in animation to table rows
                    $('#members-table tbody tr').addClass('fade-in');
                }
            });

            // Add hover effect to table rows
            $('#members-table tbody').on('mouseenter', 'tr', function() {
                $(this).addClass('table-hover-effect');
            }).on('mouseleave', 'tr', function() {
                $(this).removeClass('table-hover-effect');
            });
        });
    </script>

    <style>
        .table-hover-effect {
            background-color: var(--light-purple) !important;
            transform: scale(1.01);
            transition: all 0.2s ease;
        }
    </style>
@stop
