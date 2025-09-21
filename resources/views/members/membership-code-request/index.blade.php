@extends('adminlte::page')

@section('title', 'My Membership Code Requests')

@section('content_header')
    <h4>My Membership Code Requests</h4>
@stop

@section('content')
<div class="container-fluid">
    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <div class="card">
        <div class="card-header">
            <h5 class="card-title text-white">Membership Code Requests</h5>
            <div class="card-tools">
                <a href="{{ route('member.membership-code-request.create') }}" class="btn btn-primary btn-sm">
                    <i class="fas fa-plus"></i> Request New Codes
                </a>
            </div>
        </div>
        <div class="card-body">
            @if($requests->count() > 0)
                <div class="row">
                    @foreach($requests as $request)
                        <div class="col-12 col-md-6 col-lg-4 mb-3">
                            <div class="card h-100 shadow-sm border-0">
                                <div class="card-header bg-light">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <h6 class="mb-0 text-muted">Request # {{ $request->id }}</h6>
                                        <span class="badge badge-{{ $request->status === 'pending' ? 'warning' : ($request->status === 'approved' ? 'success' : ($request->status === 'rejected' ? 'danger' : 'secondary')) }}">
                                            {{ ucfirst($request->status) }}
                                        </span>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-6">
                                            <small class="text-muted">Quantity</small>
                                            <div class="font-weight-bold">{{ $request->quantity }}</div>
                                        </div>
                                        <div class="col-6">
                                            <small class="text-muted">Reserved Codes</small>
                                            <div class="font-weight-bold">{{ $request->reserved_codes_count ?? 0 }}</div>
                                        </div>
                                    </div>

                                    <hr class="my-2">

                                    <div class="row">
                                        <div class="col-6">
                                            <small class="text-muted">Amount per Code</small>
                                            <div class="font-weight-bold text-primary">₱{{ number_format($request->amount_per_code, 2) }}</div>
                                        </div>
                                        <div class="col-6">
                                            <small class="text-muted">Total Amount</small>
                                            <div class="font-weight-bold text-success">₱{{ number_format($request->total_amount, 2) }}</div>
                                        </div>
                                    </div>

                                    <hr class="my-2">

                                    <div class="row">
                                        <div class="col-6">
                                            <small class="text-muted">Payment Method</small>
                                            <div class="font-weight-bold">
                                                @if($request->payment_method === 'GCash')
                                                    <i class="fas fa-mobile-alt text-success"></i> GCash
                                                @elseif($request->payment_method === 'Bank')
                                                    <i class="fas fa-university text-primary"></i> Bank
                                                @else
                                                    <i class="fas fa-wallet text-warning"></i> Wallet
                                                @endif
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <small class="text-muted">Requested</small>
                                            <div class="font-weight-bold">{{ $request->created_at->format('M d, Y') }}</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-footer bg-white border-top-0">
                                    @if($request->proof_path)
                                        <a href="{{ asset('storage/' . $request->proof_path) }}" target="_blank" class="btn btn-sm btn-outline-info btn-block">
                                            <i class="fas fa-file-image"></i> View Payment Proof
                                        </a>
                                    @endif

                                    @if($request->status === 'approved' && $request->reserved_codes_count > 0)
                                        <div class="mt-2">
                                            <small class="text-success">
                                                <i class="fas fa-check-circle"></i> {{ $request->reserved_codes_count }} codes reserved for you
                                            </small>
                                        </div>
                                    @endif

                                    @if($request->status === 'pending')
                                        <div class="mt-2">
                                            <small class="text-warning">
                                                <i class="fas fa-clock"></i> Awaiting admin approval
                                            </small>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="d-flex justify-content-center mt-4">
                    {{ $requests->links() }}
                </div>
            @else
                <div class="text-center py-5">
                    <div class="mb-3">
                        <i class="fas fa-file-alt fa-3x text-muted"></i>
                    </div>
                    <h5 class="text-muted">No Membership Code Requests</h5>
                    <p class="text-muted">You haven't made any membership code requests yet.</p>
                    <a href="{{ route('member.membership-code-request.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Request Your First Codes
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>
@stop

@section('css')
<style>
    .card {
        transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
    }

    .card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0,0,0,0.1) !important;
    }

    .badge {
        font-size: 0.75rem;
    }

    .text-muted {
        font-size: 0.8rem;
    }

    .font-weight-bold {
        font-size: 0.9rem;
    }

    /* Mobile optimizations */
    @media (max-width: 768px) {
        .col-12.col-md-6.col-lg-4 {
            padding-left: 8px;
            padding-right: 8px;
        }

        .card-body {
            padding: 1rem;
        }

        .card-footer {
            padding: 0.75rem 1rem;
        }
    }

    /* Card grid adjustments */
    @media (min-width: 768px) and (max-width: 991px) {
        .col-md-6 {
            flex: 0 0 50%;
            max-width: 50%;
        }
    }

    @media (min-width: 992px) {
        .col-lg-4 {
            flex: 0 0 33.333333%;
            max-width: 33.333333%;
        }
    }
</style>
@stop

@include('partials.mobile-footer')

@section('js')
<script>
    // Add any JavaScript functionality here if needed
    $(document).ready(function() {
        // Optional: Add card click effects or other interactions
        $('.card').on('click', function() {
            // You can add card click functionality here
        });
    });
</script>
@stop
