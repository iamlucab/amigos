@extends('adminlte::page')

@section('title', 'Membership Codes')

@section('content_header')
    <h4>Membership Code Manager</h4>
@stop

@section('content')
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    {{-- Generate Code --}}
    <div class="card mb-4">
        <div class="card-header">Generate New Codes</div>
        <div class="card-body">
            <form action="{{ route('admin.membership-codes.generate') }}" method="POST">
                @csrf
                <div class="row">
                    <div class="col-md-4">
                        <label for="count">Number of Codes</label>
                        <input type="number" name="count" class="form-control" required min="1">
                    </div>
                    <div class="col-md-4">
                        <label>&nbsp;</label>
                        <button class="btn btn-primary btn-block mt-2">Generate</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- Code Table --}}
    <div class="card">
        <div class="card-header">Generated Codes</div>
        <div class="card-body">
            <div class="table-responsive">
                <table id="codesTable" class="table table-striped table-bordered table-sm codes-table">
                    <thead class="d-none d-md-table-header-group">
                        <tr>
                            <th>Code</th>
                            <th>Status</th>
                            <th>Used By</th>
                            <th>Used At</th>
                            <th>Created At</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($codes as $code)
                            <tr>
                                <td data-label="Code"><strong>{{ $code->code }}</strong></td>
                                <td data-label="Status">
                                    @if($code->used)
                                        <span class="badge badge-success">Used</span>
                                    @else
                                        <span class="badge badge-secondary">Unused</span>
                                    @endif
                                </td>
                                <td data-label="Used By">
                                    @if($code->user && $code->user->member)
                                        {{ $code->user->member->full_name }}
                                        <small class="text-muted d-block">{{ $code->user->mobile_number }}</small>
                                    @else
                                        -
                                    @endif
                                </td>
                                <td data-label="Used At">{{ $code->used_at ?? '-' }}</td>
                                <td data-label="Created At">{{ $code->created_at }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@stop

@section('css')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap4.min.css">
    <style>
        @media (max-width: 768px) {
            .codes-table thead {
                display: none;
            }

            .codes-table tr {
                display: block;
                margin-bottom: 1rem;
                border: 1px solid #dee2e6;
                border-radius: 0.5rem;
                padding: 0.75rem;
                background: #fff;
            }

            .codes-table td {
                display: block;
                text-align: right;
                padding-left: 50%;
                position: relative;
                border: none;
                border-bottom: 1px solid #e9ecef;
            }

            .codes-table td::before {
                content: attr(data-label);
                position: absolute;
                left: 0;
                top: 0;
                width: 50%;
                padding-left: 15px;
                font-weight: bold;
                text-align: left;
                white-space: nowrap;
                color: #6c757d;
            }

            .codes-table td:last-child {
                border-bottom: none;
            }
        }
    </style>
@endsection
@include('partials.mobile-footer')
@section('js')
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap4.min.js"></script>
    <script>
        $(function () {
            $('#codesTable').DataTable({
                responsive: false, // Use our custom CSS instead
                autoWidth: false,
                order: [[4, 'desc']],
            });
        });
    </script>
@endsection
