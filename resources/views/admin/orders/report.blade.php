@extends('adminlte::page')
@section('title', 'Order Reports')

@section('content')
<div class="container-fluid p-2">
    <h3 class="mb-3"><i class="bi bi-file-text"></i> Order Reportsxx</h3>

    {{-- Filter Form --}}
    <form method="GET" action="{{ route('admin.orders.index') }}" class="mb-4 bg-light p-3 rounded shadow-sm">
        <div class="row g-2">
            <div class="col-md-3">
                <label for="from">From</label>
                <input type="date" name="from" class="form-control" value="{{ request('from') }}">
            </div>
            <div class="col-md-3">
                <label for="to">To</label>
                <input type="date" name="to" class="form-control" value="{{ request('to') }}">
            </div>
            <div class="col-md-3">
                <label for="status">Status</label>
                <select name="status" class="form-select">
                    <option value="">-- All --</option>
                    @foreach(['Pending', 'Processing', 'On the Way', 'Delivered'] as $stat)
                        <option value="{{ $stat }}" {{ request('status') === $stat ? 'selected' : '' }}>{{ $stat }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label for="member">Member</label>
                <input type="text" name="member" class="form-control" value="{{ request('member') }}" placeholder="Name or Mobile">
            </div>
            <div class="col-md-12 text-end mt-2">
                <button type="submit" class="btn btn-primary btn-sm"><i class="bi bi-funnel"></i> Filter</button>
                <a href="{{ route('admin.orders.index') }}" class="btn btn-secondary btn-sm"><i class="bi bi-arrow-clockwise"></i> Reset</a>
                <a href="{{ route('admin.orders.exportPdf', request()->all()) }}" class="btn btn-danger btn-sm"><i class="bi bi-filetype-pdf"></i> Export PDF</a>
            </div>
        </div>
    </form>

    {{-- Filters Summary --}}
    @if(request()->filled(['from', 'to', 'status', 'member']))
        <div class="mb-3 d-flex flex-wrap gap-2">
            @if(request('from'))<span class="badge bg-primary">From: {{ request('from') }}</span>@endif
            @if(request('to'))<span class="badge bg-primary">To: {{ request('to') }}</span>@endif
            @if(request('status'))<span class="badge bg-warning text-dark">Status: {{ request('status') }}</span>@endif
            @if(request('member'))<span class="badge bg-success">Member: {{ request('member') }}</span>@endif
        </div>
    @endif

    {{-- Orders Table --}}
    <div class="table-responsive shadow-sm rounded">
        <table class="table table-bordered table-hover align-middle">
            <thead class="bg-secondary text-white">
                <tr>
                    <th>#</th>
                    <th>Member</th>
                    <th>Items</th>
                    <th>Total</th>
                    <th>Cashback</th>
                    <th>Status</th>
                    <th>Proof</th>
                    <th>Invoice</th>
                    <th>Update</th>
                </tr>
            </thead>
            <tbody>
                @forelse($orders as $order)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>
                            {{ $order->member->user->name }}<br>
                            <small>{{ $order->member->user->mobile }}</small>
                        </td>
                        <td>
                            <ul class="list-unstyled small mb-0">
                                @foreach($order->items as $item)
                                    <li>
                                        {{ $item->product->name }} -
                                        ₱{{ number_format($item->price, 2) }} × {{ $item->quantity }}
                                    </li>
                                @endforeach
                            </ul>
                        </td>
                        <td>₱{{ number_format($order->total_amount, 2) }}</td>
                        <td>₱{{ number_format($order->total_cashback, 2) }}</td>
                        <td>
                            <span class="badge bg-{{ $order->status === 'Delivered' ? 'success' : ($order->status === 'Pending' ? 'warning' : 'info') }}">
                                {{ $order->status }}
                            </span>
                        </td>
                        <td>
                            @if($order->proof_photo)
                                <a href="{{ asset('storage/' . $order->proof_photo) }}" target="_blank">View</a>
                            @else
                                <span class="text-muted">None</span>
                            @endif
                        </td>
                        <td>
                            <a href="{{ route('admin.orders.invoice', $order->id) }}" target="_blank" class="btn btn-sm btn-light">
                                <i class="bi bi-receipt"></i>
                            </a>
                        </td>
                        <td>
                            <form method="POST" action="{{ route('admin.orders.updateStatus', $order->id) }}" class="d-flex align-items-center update-status-form">
                                @csrf
                                <select name="status" class="form-select form-select-sm status-select" {{ $order->status === 'Delivered' ? 'disabled' : '' }}>
                                    @foreach(['Pending', 'Processing', 'On the Way', 'Delivered'] as $stat)
                                        <option value="{{ $stat }}" {{ $order->status === $stat ? 'selected' : '' }}>{{ $stat }}</option>
                                    @endforeach
                                </select>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" class="text-center text-muted">No orders found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    <div class="mt-3">
        {{ $orders->withQueryString()->links() }}
    </div>
</div>
@endsection

@section('js')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    document.querySelectorAll('.status-select').forEach(select => {
        select.addEventListener('change', function() {
            const form = this.closest('form');
            const status = this.value;
            Swal.fire({
                title: 'Are you sure?',
                text: "Change status to " + status + "?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, update it!',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                } else {
                    // revert select back to original
                    this.selectedIndex = [...this.options].findIndex(opt => opt.defaultSelected);
                }
            });
        });
    });
</script>
@endsection
