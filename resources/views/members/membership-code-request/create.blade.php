@extends('adminlte::page')

@section('title', 'Request Membership Codes')

@section('content_header')
    <h5> Request Membership Codes</h5>
@stop

@section('content')
<div class="container-fluid">
    {{-- @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif --}}

    {{-- ✅ Membership Dashboard Cards - Mobile Optimized --}}
    <div class="row mb-4 g-3">
        {{-- Reserved Membership Codes Card --}}
        <div class="col-12 col-md-4">
            <div class="card h-100 shadow-sm">
   <div class="card-header bg-info text-white py-2 px-3">
  <div class="d-flex align-items-center text-white">
    <i class="bi bi-upc-scan me-2"></i> <h6 class="card-title mb-0 fs-6" style="color: white !important;">&nbsp; Reserved Codes</h6>
  </div>
</div>
                <div class="card-body p-3">
                    @if(isset($reservedCodes) && $reservedCodes->count() > 0)
                    <div class="text-center mb-2">
                        <span class="badge bg-warning text-dark fs-6">{{ $reservedCodes->count() }}</span>
                    </div>
                    <div class="small text-muted text-center">
                        {{ $reservedCodes->count() }} code(s) available
                    </div>
                    @else
                    <div class="text-center">
                        <i class="bi bi-upc-scan fs-3 text-muted mb-1"></i>
                        <div class="small text-muted"> No codes</div>
                    </div>
                    @endif
                </div>
                <div class="card-footer bg-light p-2 text-center">
                    <a href="#reserved-codes-section" class="small text-decoration-none"> View Details</a>
                </div>
            </div>
        </div>

        {{-- Membership Code Requests Card --}}
        <div class="col-12 col-md-4">
            <div class="card h-100 shadow-sm">
                <div class="card-header bg-primary text-white py-2 px-3">
                    <div class="d-flex align-items-center text-white">
                        <i class="bi bi-file-earmark-text me-2"></i> <h6 class="card-title  mb-0 fs-6"  style="color: white !important;">&nbsp;Code Requests</h6>
                    </div>
                </div>
                <div class="card-body p-3">
                    @if(isset($membershipCodeRequests) && $membershipCodeRequests->count() > 0)
                    @php
                        $pendingCount = $membershipCodeRequests->where('status', 'pending')->count();
                        $approvedCount = $membershipCodeRequests->where('status', 'approved')->count();
                    @endphp
                    <div class="d-flex justify-content-around text-center">
                        <div>
                            <div class="fw-bold">{{ $pendingCount }}</div>
                            <div class="small text-muted"> Pending</div>
                        </div>
                        <div>
                            <div class="fw-bold">{{ $approvedCount }}</div>
                            <div class="small text-muted"> Approved</div>
                        </div>
                    </div>
                    @else
                    <div class="text-center">
                        <i class="bi bi-file-earmark-text fs-3 text-muted mb-1"></i>
                        <div class="small text-muted"> No requests</div>
                    </div>
                    @endif
                </div>
                <div class="card-footer bg-light p-2 text-center">
                    <a href="#code-requests-section" class="small text-decoration-none"> View Details</a>
                </div>
            </div>
        </div>

        {{-- Register New Members Card --}}
        <div class="col-12 col-md-4">
            <div class="card h-100 shadow-sm">
                <div class="card-header bg-success text-white py-2 px-3">
                    <div class="d-flex align-items-center">
                        <i class="bi bi-person-plus me-2"></i>
                        <h6 class="card-title mb-0 fs-6" style="color: white !important;" >&nbsp; Register Members</h6>
                    </div>
                </div>
                <div class="card-body p-3 text-center">
                    <div class="mb-2">
                        <i class="bi bi-person-plus fs-2 text-success"></i>
                    </div>
                    <div class="small text-muted">
                        Expand your network
                    </div>
                </div>
                <div class="card-footer bg-light p-2 text-center">
                    <a href="{{ route('member.register.form') }}" class="small text-decoration-none">Register Now</a>
                </div>
            </div>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-12 col-lg-8">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h6 class="card-title mb-0" style="color: white !important;">
                        <i class="bi bi-code" ></i> Request Membership Codes
                    </h6>
                </div>
                <div class="card-body">
                    <form action="{{ route('member.membership-code-request.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <!-- Quantity and Price Section -->
                        <div class="row">
                            <div class="col-12 col-md-6 mb-3">
                                <div class="form-group">
                                    <label for="quantity" class="font-weight-bold">
                                        <i class="bi bi-hash"></i> Quantity
                                    </label>
                                    <input type="number" name="quantity" id="quantity"
                                           class="form-control form-control-lg"
                                           min="1" max="100" value="{{ old('quantity', 1) }}" required>
                                    <small class="form-text text-muted">
                                        Enter the number of membership codes you want to request (1-100)
                                    </small>
                                </div>
                            </div>

                            <div class="col-12 col-md-6 mb-3">
                                <div class="form-group">
                                    <label class="font-weight-bold">
                                        <i class="bi bi-tag"></i> Price per Code
                                    </label>
                                    <div class="input-group input-group-lg">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text bg-light">₱</span>
                                        </div>
                                        <input type="text" class="form-control" value="{{ number_format($amountPerCode, 2) }}" readonly>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Total Amount and Wallet Balance -->
                        <div class="row">
                            <div class="col-12 col-md-6 mb-3">
                                <div class="form-group">
                                    <label class="font-weight-bold">
                                        <i class="bi bi-calculator"></i> Total Amount
                                    </label>
                                    <div class="input-group input-group-lg">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text bg-success text-white">₱</span>
                                        </div>
                                        <input type="text" id="total_amount" class="form-control font-weight-bold text-success"
                                               value="{{ number_format($amountPerCode, 2) }}" readonly>
                                    </div>
                                </div>
                            </div>

                            @isset($wallet)
                            <div class="col-12 col-md-6 mb-3">
                                <div class="form-group">
                                    <label class="font-weight-bold">
                                        <i class="bi bi-wallet"></i> Wallet Balance
                                    </label>
                                    <div class="input-group input-group-lg">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text bg-info text-white">₱</span>
                                        </div>
                                        <input type="text" id="wallet_balance" class="form-control"
                                               value="{{ number_format($wallet->balance, 2) }}" readonly>
                                    </div>
                                </div>
                            @endisset
                        </div>

                        <!-- Payment Method Selection -->
                        <div class="form-group mb-4">
                            <label for="payment_method" class="font-weight-bold">
                                <i class="bi bi-credit-card"></i> Payment Method
                            </label>
                            <select name="payment_method" id="payment_method" class="form-control form-control-lg" required>
                                <option value="">Select Payment Method</option>
                                <option value="GCash" {{ old('payment_method') == 'GCash' ? 'selected' : '' }}>
                                    <i class="bi bi-phone"></i> GCash
                                </option>
                                <option value="Bank" {{ old('payment_method') == 'Bank' ? 'selected' : '' }}>
                                    <i class="bi bi-bank"></i> Bank Transfer
                                </option>
                                <option value="Wallet" {{ old('payment_method') == 'Wallet' ? 'selected' : '' }}>
                                    <i class="bi bi-wallet"></i> Wallet
                                </option>
                            </select>
                        </div>

                        <!-- Wallet Payment Warning -->
                        <div id="wallet_warning" class="alert alert-warning d-none">
                            <i class="bi bi-exclamation-triangle"></i>
                            <strong>Insufficient Funds!</strong> Please choose another payment method or add funds to your wallet.
                        </div>

                        <!-- Wallet Payment Success -->
                        <div id="wallet_success" class="alert alert-info d-none">
                            <i class="bi bi-info-circle"></i>
                            <strong>Wallet Payment:</strong> If you proceed with wallet payment, the codes will be transferred to you immediately after payment.
                        </div>

                        <!-- GCash Payment Details -->
                        <div id="gcash_details" class="d-none">
                            <div class="card border-primary shadow-sm mb-3">
                                <div class="card-header bg-primary text-white ">
                                    <h6 class="mb-0" style="color: white !important;">
                                        <i class="bi bi-phone"></i> GCash Payment Details
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <div class="text-center mb-3">
                                        <img src="{{ asset('images/gcashQR.jpeg') }}" alt="GCash QR Code"
                                            class="img-fluid rounded shadow-sm" style="max-width: 200px;">
                                    </div>

                                    <div class="row">
                                        <div class="col-12 col-md-6">
                                            <strong>GCash Account:</strong><br>LU*** C.
                                        </div>
                                        <div class="col-12 col-md-6 text-md-right">
                                            <a href="{{ asset('images/gcashQR.jpeg') }}" download="gcashQR.jpeg"
                                               class="btn btn-sm btn-primary">
                                                <i class="bi bi-download"></i> Download QR Code
                                            </a>
                                        </div>
                                    </div>

                                    <div class="mt-2">
                                        <small class="text-muted">
                                            <i class="bi bi-info-circle"></i> After payment, please upload the proof below.
                                        </small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Bank Payment Details -->
                        <div id="bank_details" class="d-none">
                            <div class="card border-primary shadow-sm mb-3">
                                <div class="card-header bg-primary text-white">
                                    <h6 class="mb-0" style="color: white !important;">
                                        <i class="bi bi-bank"></i> Bank Transfer Details
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <div class="d-flex align-items-center mb-3">
                                        <img src="{{ asset('images/bdo-logo.png') }}" alt="BDO Logo"
                                             class="mr-3" style="max-width: 60px;">
                                        <div>
                                            <div><strong>Bank:</strong> BDO</div>
                                            <div><strong>Account Name:</strong> Amigos 98 Online</div>
                                            <div><strong>Account No:</strong> 0071 5801 3083</div>
                                        </div>
                                    </div>
                                    <small class="text-muted">
                                        <i class="bi bi-info-circle"></i> Please send the exact amount and upload the proof below.
                                    </small>
                                </div>
                            </div>
                        </div>

                        <!-- Proof Upload -->
                        <div class="form-group mb-3">
                            <label for="proof" class="font-weight-bold">
                                <i class="bi bi-upload"></i> Proof of Payment (Optional)
                            </label>
                            <div class="custom-file">
                                <input type="file" name="proof" id="proof" class="custom-file-input" accept="image/*">
                                <label class="custom-file-label" for="proof">Choose file...</label>
                            </div>
                            <small class="form-text text-muted">
                                Upload proof of payment (JPEG, PNG, GIF - max 2MB)
                            </small>
                        </div>

                        <!-- Note -->
                        <div class="form-group mb-4">
                            <label for="note" class="font-weight-bold">
                                <i class="bi bi-sticky"></i> Note (Optional)
                            </label>
                            <textarea name="note" id="note" class="form-control" rows="3"
                                      placeholder="Any additional notes...">{{ old('note') }}</textarea>
                        </div>
                        <br>
                        <!-- Submit Buttons -->
                        <div class="row">
                            <div class="col-12 col-md-6 mb-2">
                                <button type="submit" class="btn btn-primary btn-sm btn-block">
                                    <i class="bi bi-send"></i> Submit
                                </button>
                            </div>
                            <div class="col-12 col-md-6 mb-2">
                                <a href="{{ route('member.dashboard') }}" class="btn btn-warning text-white btn-sm btn-block">
                                    <i class="bi bi-arrow-left"></i> Cancel
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- ✅ Reserved Membership Codes --}}
<div id="reserved-codes-section" class="row mb-4">
    <div class="col-12">
        <div class="card shadow-sm">
            <div class="card-header bg-info text-white">
                <h6 class="card-title mb-0" style="color:  white !important;">
                    <i class="bi bi-upc-scan me-2"></i>&nbsp;  Reserved Membership Codes
                </h6>
            </div>
            <div class="card-body">
                @if(isset($reservedCodes) && $reservedCodes->count() > 0)
                <div class="table-responsive">
                    <table class="table table-striped table-hover mb-0">
                        <thead>
                            <tr>
                                <th>Code</th>
                                <th>Status</th>
                                <th>Reserved At</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($reservedCodes as $code)
                            <tr>
                                <td>
                                    <strong>{{ $code->code }}</strong>
                                </td>
                                <td>
                                    <span class="badge bg-warning text-dark">Reserved</span>
                                </td>
                                <td>
                                    {{ $code->reserved_at->format('M d, Y H:i') }}
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="mt-2 small text-muted">
                    These codes are reserved for you and can be used when registering new members.
                </div>
                @else
                <div class="text-center py-4">
                    <i class="bi bi-upc-scan fs-1 text-muted mb-3"></i>
                    <h6 class="text-muted"> No Reserved Codes</h6>
                    <p class="text-muted"> You don't have any reserved membership codes yet.</p>
                    <a href="{{ route('member.membership-code-request.create') }}" class="btn btn-success text-white">
                        <i class="bi bi-plus-circle me-1"></i> Request Membership Codes
                    </a>
                    <div class="mt-3 small text-muted">
                        Reserved codes can be used when registering new members to your network.
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

{{-- ✅ Membership Code Requests --}}
<div id="code-requests-section" class="row mb-4">
    <div class="col-12">
        <div class="card shadow-sm">
            <div class="card-header bg-primary text-white">
                <h6 class="card-title mb-0" style="color:  white !important;">
                    <i class="bi bi-upc-scan me-2"></i>&nbsp;Membership Code Requests
                </h6>
            </div>
            <div class="card-body">
                @if(isset($membershipCodeRequests) && $membershipCodeRequests->count() > 0)
                <div class="row g-3">
                    @foreach($membershipCodeRequests as $request)
                    <div class="col-12">
                        <div class="card border rounded shadow-sm h-100">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <div>
                                        <h6 class="card-title mb-1">Request # &nbsp; {{ $request->id }}</h6>
                                        <small class="text-muted">{{ $request->created_at->format('M d, Y H:i') }}</small>
                                    </div>
                                    <div>
                                        @if($request->status === 'pending')
                                            <span class="badge bg-warning text-dark" style="font-size: 0.7rem;">Pending</span>
                                        @elseif($request->status === 'approved')
                                            <span class="badge bg-success" style="font-size: 0.7rem;">Approved</span>
                                        @elseif($request->status === 'rejected')
                                            <span class="badge bg-danger" style="font-size: 0.7rem;">Rejected</span>
                                        @elseif($request->status === 'cancelled')
                                            <span class="badge bg-secondary" style="font-size: 0.7rem;">Cancelled</span>
                                        @endif
                                    </div>
                                </div>

                                <div class="row g-2">
                                    <div class="col-6">
                                        <small class="text-muted">Quantity</small>
                                        <div class="fw-bold">{{ $request->quantity }}</div>
                                    </div>
                                    <div class="col-6">
                                        <small class="text-muted">Payment Method</small>
                                        <div class="fw-bold">{{ $request->payment_method }}</div>
                                    </div>
                                    <div class="col-6">
                                        <small class="text-muted">Amount per Code</small>
                                        <div class="fw-bold">₱{{ number_format($request->amount_per_code, 2) }}</div>
                                    </div>
                                    <div class="col-6">
                                        <small class="text-muted">Total Amount</small>
                                        <div class="fw-bold">₱{{ number_format($request->total_amount, 2) }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>

                {{-- Fallback message for pending requests --}}
                @if($membershipCodeRequests->where('status', 'pending')->count() > 0)
                <div class="mt-3 alert alert-info mb-0">
                    <i class="bi bi-info-circle me-2"></i>
                    Your membership code request is currently pending approval. Once approved, the codes will be displayed here.
                </div>
                @endif

                {{-- Message for approved requests without codes yet --}}
                @if($membershipCodeRequests->where('status', 'approved')->count() > 0 && (!isset($reservedCodes) || $reservedCodes->count() == 0))
                <div class="mt-3 alert alert-info mb-0">
                    <i class="bi bi-info-circle me-2"></i>
                    Your membership code request has been approved. Your codes are being prepared and will appear here shortly.
                </div>
                @endif
                @else
                <div class="text-center py-4">
                    <i class="bi bi-file-earmark-text fs-1 text-muted mb-3"></i>
                    <h6 class="text-muted"> No Membership Code Requests</h6>
                    <p class="text-muted"> You haven't made any membership code requests yet.</p>
                    <a href="{{ route('member.membership-code-request.create') }}" class="btn btn-primary">
                        <i class="bi bi-plus-circle me-1"></i> Request Membership Codes
                    </a>
                    <div class="mt-3 small text-muted">
                        Request membership codes to register new members in your network.
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

{{-- ✅ Membership Registration with Assigned Codes --}}
<div class="row mb-4">
    <div class="col-12">
        <div class="card shadow-sm">
            <div class="card-header bg-success text-white">
                <h6 class="card-title mb-0" style="color: white !important;">
                    <i class="bi bi-person-plus me-2"></i> Register New Members
                </h6>
            </div>
            <div class="card-body">
                <p class="small text-muted">
                    Use your reserved membership codes to register new members in your network.
                    This helps expand your referral network and earn more bonuses.
                </p>

                @if(isset($reservedCodes) && $reservedCodes->count() > 0)
                <div class="alert alert-info mb-3">
                    <i class="bi bi-info-circle me-2"></i>
                    You have <strong>{{ $reservedCodes->count() }}</strong> reserved code(s) ready to use for registration.
                </div>
                @endif

           <div class="d-flex flex-wrap gap-2">
    <!-- Register Member Icon Button -->
    <a href="{{ route('member.register.form') }}"
       class="btn btn-sm btn-success text-white p-2"
       data-bs-toggle="tooltip"
       data-bs-placement="top"
       title="Register Member">
        <i class="bi bi-person-plus"></i>
    </a>
 &nbsp;
    <!-- Request Codes Icon Button -->
    <a href="{{ route('member.membership-code-request.create') }}"
       class="btn btn-sm btn-outline-primary p-2"
       data-bs-toggle="tooltip"
       data-bs-placement="top"
       title="Request More Codes">
        <i class="bi bi-upc-scan"></i>
    </a>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
        })
    });
</script>
@endpush


            </div>
        </div>
    </div>
</div>

@stop
<br>
@include('partials.mobile-footer')


@section('css')
<style>
    .card {
        border-radius: 15px;
        border: none;
    }

    .card-header {
        border-radius: 15px 15px 0 0 !important;
    }

    .form-control-lg {
        border-radius: 10px;
    }

    .input-group-text {
        border-radius: 10px 0 0 10px;
    }

    .btn-lg {
        border-radius: 10px;
        padding: 12px 24px;
    }

    .custom-file-label {
        border-radius: 10px;
    }

    .alert {
        border-radius: 10px;
        border: none;
    }

    /* Mobile optimizations */
    @media (max-width: 768px) {
        .container-fluid {
            padding: 10px;
        }

        .card-body {
            padding: 1.5rem;
        }

        .form-control-lg {
            font-size: 16px; /* Prevents zoom on iOS */
        }

        .btn-lg {
            padding: 15px 20px;
            font-size: 16px;
        }
    }

    /* Hover effects */
    .card:hover {
        transform: translateY(-2px);
        transition: transform 0.2s ease-in-out;
    }

    .btn:hover {
        transform: translateY(-1px);
        transition: transform 0.2s ease-in-out;
    }
</style>
@stop

@include('partials.mobile-footer')

@section('js')
<script>
    // Calculate total amount when quantity changes
    document.getElementById('quantity').addEventListener('input', function() {
        const quantity = parseInt(this.value) || 1;
        const amountPerCode = {{ $amountPerCode }};
        const totalAmount = quantity * amountPerCode;
        document.getElementById('total_amount').value = totalAmount.toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,');
        validateWalletPayment();
        showPaymentDetails();
    });

    // Handle payment method change
    document.getElementById('payment_method').addEventListener('change', function() {
        validateWalletPayment();
        showPaymentDetails();
    });

    // Validate wallet payment
    function validateWalletPayment() {
        const paymentMethod = document.getElementById('payment_method').value;
        const walletWarning = document.getElementById('wallet_warning');
        const walletSuccess = document.getElementById('wallet_success');

        if (paymentMethod === 'Wallet') {
            const totalAmount = parseFloat(document.getElementById('total_amount').value.replace(/,/g, ''));
            const walletBalance = parseFloat(document.getElementById('wallet_balance').value.replace(/,/g, ''));

            if (totalAmount > walletBalance) {
                walletWarning.classList.remove('d-none');
                walletSuccess.classList.add('d-none');
                // Disable submit button
                document.querySelector('button[type="submit"]').disabled = true;
            } else {
                walletWarning.classList.add('d-none');
                walletSuccess.classList.remove('d-none');
                // Enable submit button
                document.querySelector('button[type="submit"]').disabled = false;
            }
        } else {
            walletWarning.classList.add('d-none');
            walletSuccess.classList.add('d-none');
            // Enable submit button
            document.querySelector('button[type="submit"]').disabled = false;
        }
    }

    // Show payment details based on selected method
    function showPaymentDetails() {
        const paymentMethod = document.getElementById('payment_method').value;
        const gcashDetails = document.getElementById('gcash_details');
        const bankDetails = document.getElementById('bank_details');

        // Hide all payment details
        gcashDetails.classList.add('d-none');
        bankDetails.classList.add('d-none');

        // Show relevant details
        if (paymentMethod === 'GCash') {
            gcashDetails.classList.remove('d-none');
        } else if (paymentMethod === 'Bank') {
            bankDetails.classList.remove('d-none');
        }
    }

    // Custom file input
    document.querySelector('.custom-file-input').addEventListener('change', function(e) {
        var fileName = e.target.files[0].name;
        var nextSibling = e.target.nextElementSibling;
        nextSibling.innerText = fileName;
    });

    // Initial validation and setup
    document.addEventListener('DOMContentLoaded', function() {
        validateWalletPayment();
        showPaymentDetails();
    });
</script>
@stop
