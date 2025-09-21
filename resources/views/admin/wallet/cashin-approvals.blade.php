@extends('adminlte::page')

@section('title', 'Cash In Approvals')

{{-- ✅ AdminLTE + DataTables CSS --}}
@section('css')
    <link rel="stylesheet" href="{{ asset('vendor/adminlte/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('vendor/adminlte/plugins/datatables-responsive/css/responsive.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('vendor/adminlte/plugins/datatables-buttons/css/buttons.bootstrap4.min.css') }}">
@endsection

@section('content')
<div class="container-fluid mt-4">
    <h4 class="mb-4">Cash In Requests</h4>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert">&times;</button>
        </div>
    @endif

    <div class="card">
        <div class="card-body">
            {{-- 🖥️ Desktop DataTable --}}
            <div class="table-responsive d-none d-md-block">
                <table id="cashin-table" class="table table-bordered table-hover table-striped dt-responsive nowrap" style="width:100%">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Member</th>
                            <th>Amount</th>
                            <th>Payment Method</th>
                            <th>Note</th>
                            <th>Status</th>
                            <th>Requested At</th>
                            <th>Proof</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($requests as $request)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>
                                    @if($request->member)
                                        <strong>{{ $request->member->first_name }} {{ $request->member->last_name }}</strong><br>
                                        <small>{{ $request->member->mobile_number }}</small>
                                    @else
                                        <span class="text-muted">Member not found</span>
                                    @endif
                                </td>
                                <td>₱{{ number_format($request->amount, 2) }}</td>
                                <td>{{ $request->payment_method }}</td>
                                <td>{{ $request->note }}</td>
                                <td>
                                    <span class="badge badge-{{ 
                                        $request->status == 'Approved' ? 'success' : 
                                        ($request->status == 'Rejected' ? 'danger' : 
                                        ($request->status == 'Reviewed' ? 'info' : 'warning')) }}">
                                        {{ $request->status }}
                                    </span>
                                </td>
                                <td>{{ $request->created_at->format('M d, Y h:i A') }}</td>
                                <td>
                                    @if($request->proof_path)
                                       <button class="btn btn-sm btn-info" data-toggle="modal" data-target="#proofModal{{ $request->id }}">
                                            View
                                       </button>
                                    @else
                                        <small>No proof</small>
                                    @endif
                                </td>
                                <td>
                                    @if($request->status == 'Pending')
                                        <form action="{{ route('admin.cashin.reviewed', $request->id) }}" method="POST" class="d-inline-block mb-1">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-warning" onclick="return confirm('Mark as reviewed?')">
                                                <i class="bi bi-eye"></i> Review
                                            </button>
                                        </form>
                                    @elseif($request->status == 'Reviewed')
                                        <form action="{{ route('admin.cashin.approve', $request->id) }}" method="POST" class="d-inline-block mb-1">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-success" onclick="return confirm('Approve this request?')">
                                                <i class="bi bi-check"></i> Approve
                                            </button>
                                        </form>
                                        <form action="{{ route('admin.cashin.reject', $request->id) }}" method="POST" class="d-inline-block ml-1">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Reject this request?')">
                                                <i class="bi bi-x"></i> Reject
                                            </button>
                                        </form>
                                    @else
                                        <em>Completed</em>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- 📱 Mobile Card View --}}
            <div class="d-md-none">
                @foreach($requests as $request)
                    <div class="card mb-2">
                        <div class="card-body">
                            <h5 class="mb-1">
                                @if($request->member)
                                    {{ $request->member->first_name }} {{ $request->member->last_name }}
                                    <small class="d-block text-muted">{{ $request->member->mobile_number }}</small>
                                @else
                                    <span class="text-muted">Member not found</span>
                                @endif
                            </h5>
                            <p class="mb-1"><strong>Amount:</strong> ₱{{ number_format($request->amount, 2) }}</p>
                            <p class="mb-1"><strong>Payment Method:</strong> {{ $request->payment_method }}</p>
                            <p class="mb-1"><strong>Note:</strong> {{ $request->note }}</p>
                            <p class="mb-1"><strong>Status:</strong>
                                <span class="badge badge-{{ 
                                    $request->status == 'Approved' ? 'success' : 
                                    ($request->status == 'Rejected' ? 'danger' : 
                                    ($request->status == 'Reviewed' ? 'info' : 'warning')) }}">
                                    {{ $request->status }}
                                </span>
                            </p>
                            <p class="mb-1"><strong>Requested:</strong> {{ $request->created_at->format('M d, Y h:i A') }}</p>
                            <p class="mb-2"><strong>Proof:</strong><br>
                                @if($request->proof_path)
                                    <a href="{{ asset('storage/' . $request->proof_path) }}" target="_blank" class="btn btn-sm btn-info">View</a>
                                @else
                                    <small>No uploaded proof</small>
                                @endif
                            </p>
                            <div>
                                @if($request->status == 'Pending')
                                    <form action="{{ route('admin.cashin.reviewed', $request->id) }}" method="POST" class="d-inline-block">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-warning mb-1" onclick="return confirm('Mark as reviewed?')">
                                            <i class="bi bi-eye"></i> Review
                                        </button>
                                    </form>
                                @elseif($request->status == 'Reviewed')
                                    <form action="{{ route('admin.cashin.approve', $request->id) }}" method="POST" class="d-inline-block">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-success mb-1" onclick="return confirm('Approve this request?')">
                                            <i class="bi bi-check"></i> Approve
                                        </button>
                                    </form>
                                    <form action="{{ route('admin.cashin.reject', $request->id) }}" method="POST" class="d-inline-block">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-danger mb-1" onclick="return confirm('Reject this request?')">
                                            <i class="bi bi-x"></i> Reject
                                        </button>
                                    </form>
                                @else
                                    <em>Completed</em>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>

{{-- Proof Modals --}}
@foreach($requests as $request)
    @if($request->proof_path)
        <div class="modal" id="proofModal{{ $request->id }}" tabindex="-1" role="dialog">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Payment Proof</h5>
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                    </div>
                    <div class="modal-body text-center">
                        <img src="{{ asset('storage/' . $request->proof_path) }}" class="img-fluid" alt="Payment Proof">
                        <br><br>
                        <a href="{{ asset('storage/' . $request->proof_path) }}" download class="btn btn-sm btn-secondary">
                            Download
                        </a>
                    </div>
                </div>
            </div>
        </div>
    @endif
@endforeach

@endsection
@include('partials.mobile-footer')

{{-- ✅ AdminLTE + DataTables JS --}}
@section('js')
    <script src="{{ asset('vendor/adminlte/plugins/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('vendor/adminlte/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('vendor/adminlte/plugins/datatables-responsive/js/dataTables.responsive.min.js') }}"></script>
    <script src="{{ asset('vendor/adminlte/plugins/datatables-responsive/js/responsive.bootstrap4.min.js') }}"></script>

    <script>
        $(function () {
            $('#cashin-table').DataTable({
                responsive: true,
                autoWidth: false,
                ordering: true,
                pageLength: 10,
                lengthChange: false,
                language: {
                    search: "_INPUT_",
                    searchPlaceholder: "Search requests..."
                }
            });
        });
    </script>
@endsection
