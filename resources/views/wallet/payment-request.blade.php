@extends('adminlte::page')
@section('title', 'Payment Request')

{{-- E-Bili Theme Styling --}}
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
<link rel="stylesheet" href="{{ asset('css/ebili-theme.css') }}">

@section('content_header')
<div class="text-center mb-4 fade-in">
    <div class="qr-payment-header">
        <i class="bi bi-qr-code fa-3x text-primary mb-3"></i>
        <h2 class="fw-bold" style="color: var(--primary-purple);">QR Payment Request</h2>
        <p class="text-muted">Complete your payment to {{ $recipient->full_name }}</p>
    </div>
</div>
@stop

@section('content')
<div class="container-fluid px-2">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow-lg border-0" style="border-radius: 20px;">
                <div class="card-header text-center" style="background: linear-gradient(135deg, #6f42c1 0%, #8e44ad 100%); color: white; border-radius: 20px 20px 0 0;">
                    <h4 class="mb-0"><i class="bi bi-send me-2"></i>Send Payment</h4>
                </div>
                
                <div class="card-body p-4">
                    {{-- Recipient Information --}}
                    <div class="recipient-info mb-4 p-3" style="background: #f8f9fa; border-radius: 15px; border-left: 4px solid var(--primary-purple);">
                        <h5 class="fw-bold mb-2" style="color: var(--primary-purple);">
                            <i class="bi bi-person me-2"></i>Send to:
                        </h5>
                        <div class="d-flex align-items-center">
                            <div class="recipient-avatar me-3" style="width: 50px; height: 50px; background: var(--primary-purple); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-weight: bold; font-size: 18px;">
                                {{ strtoupper(substr($recipient->full_name, 0, 2)) }}
                            </div>
                            <div>
                                <div class="fw-bold">{{ $recipient->full_name }}</div>
                                <div class="text-muted small">{{ $recipient->mobile_number }}</div>
                            </div>
                        </div>
                    </div>

                    {{-- Sender Balance Info --}}
                    <div class="balance-info mb-4 p-3" style="background: #e8f5e8; border-radius: 15px; border-left: 4px solid #28a745;">
                        <div class="d-flex justify-content-between align-items-center">
                            <span><i class="bi bi-wallet2 me-2"></i>Your Available Balance:</span>
                            <span class="fw-bold text-success">₱{{ number_format($senderWallet->balance, 2) }}</span>
                        </div>
                    </div>

                    {{-- Payment Form --}}
                    <form method="POST" action="{{ route('payment.request.send', $recipientWallet->wallet_id) }}">
                        @csrf
                        
                        {{-- Error Display --}}
                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <ul class="mb-0">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        {{-- Amount Input --}}
                        <div class="mb-4">
                            <label class="form-label fw-bold">
                                <i class="bi bi-currency-dollar me-2"></i>Enter Amount
                            </label>
                            <div class="input-group input-group-lg">
                                <span class="input-group-text" style="background: var(--primary-purple); color: white; border: none;">₱</span>
                                <input type="number" 
                                       name="amount" 
                                       class="form-control" 
                                       placeholder="0.00" 
                                       step="0.01" 
                                       min="1" 
                                       max="{{ $senderWallet->balance }}"
                                       value="{{ old('amount') }}" 
                                       required
                                       style="border: 2px solid #e9ecef; border-radius: 0 10px 10px 0;">
                            </div>
                            <small class="text-muted">Maximum: ₱{{ number_format($senderWallet->balance, 2) }}</small>
                        </div>

                        {{-- Transaction Fee Notice --}}
                        <div class="alert alert-info" style="background: #fff9db; border-color: #ffeaa7; color: #856404;">
                            <i class="bi bi-info-circle me-2"></i>
                            <strong>Transaction Notice:</strong> This payment will be processed immediately and cannot be reversed. Please verify the recipient details and amount before confirming.
                        </div>

                        {{-- Confirmation Checkbox --}}
                        <div class="form-check mb-4">
                            <input type="checkbox" class="form-check-input" id="confirmPayment" name="confirm" required>
                            <label class="form-check-label" for="confirmPayment">
                                I confirm that I want to send this payment to <strong>{{ $recipient->full_name }}</strong> and understand that this transaction cannot be reversed.
                            </label>
                        </div>

                        {{-- Action Buttons --}}
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-lg" style="background: var(--primary-purple); color: white; border-radius: 15px;" id="sendPaymentBtn" disabled>
                                <i class="bi bi-send me-2"></i>Send Payment
                            </button>
                            <a href="{{ route('member.dashboard') }}" class="btn btn-outline-secondary btn-lg" style="border-radius: 15px;">
                                <i class="bi bi-x me-2"></i>Cancel
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Security Notice --}}
<div class="row justify-content-center mt-4">
    <div class="col-md-6">
        <div class="alert alert-warning text-center">
            <i class="bi bi-shield-check me-2"></i>
            <strong>Security Reminder:</strong> Only send money to people you know and trust. E-Bili will never ask for your password or PIN via QR codes.
        </div>
    </div>
</div>
@stop

@section('css')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    <style>
        .fade-in {
            animation: fadeIn 0.5s ease-in;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .card {
            transition: transform 0.2s ease-in-out;
        }
        
        .card:hover {
            transform: translateY(-2px);
        }
        
        .btn:disabled {
            opacity: 0.6;
            cursor: not-allowed;
        }
        
        .input-group-text {
            font-weight: bold;
        }
        
        .form-control:focus {
            border-color: var(--primary-purple);
            box-shadow: 0 0 0 0.2rem rgba(111, 66, 193, 0.25);
        }
    </style>
@endsection

@section('js')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    
    {{-- Toastr Success/Error Messages --}}
    @if(session('success'))
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                toastr.success("{{ session('success') }}", "Success", {
                    timeOut: 3000,
                    progressBar: true,
                    positionClass: 'toast-top-right'
                });
            });
        </script>
    @endif

    @if(session('error'))
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                toastr.error("{{ session('error') }}", "Error", {
                    timeOut: 5000,
                    progressBar: true,
                    positionClass: 'toast-top-right'
                });
            });
        </script>
    @endif
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const confirmCheckbox = document.getElementById('confirmPayment');
            const sendButton = document.getElementById('sendPaymentBtn');
            const amountInput = document.querySelector('input[name="amount"]');
            const maxAmount = {{ $senderWallet->balance }};
            
            function validateForm() {
                const amount = parseFloat(amountInput.value);
                const isChecked = confirmCheckbox.checked;
                const hasValidAmount = !isNaN(amount) && amount > 0 && amount <= maxAmount;
                
                sendButton.disabled = !(isChecked && hasValidAmount);
                
                // Show amount validation
                let warning = document.getElementById('amountWarning');
                if (amount > maxAmount && amountInput.value) {
                    if (!warning) {
                        warning = document.createElement('small');
                        warning.id = 'amountWarning';
                        warning.classList.add('text-danger', 'mt-1', 'd-block');
                        warning.innerText = 'Amount exceeds available balance.';
                        amountInput.parentNode.insertBefore(warning, amountInput.parentNode.nextSibling);
                    }
                } else if (warning) {
                    warning.remove();
                }
            }
            
            confirmCheckbox.addEventListener('change', validateForm);
            amountInput.addEventListener('input', validateForm);
            
            // Auto-focus amount input
            amountInput.focus();
        });
    </script>
@endsection