
@extends('layouts.adminlte-base')
@section('title', 'Member Dashboard')

{{-- E-Bili Theme Styling --}}
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet" integrity="sha512-iecdLmaskl7CVkqkXNQ/ZH/XLlvWZOJyj7Yy7tcenmpD1ypASozpmT/E0iPtmFIB46ZmdtAc9eNBvH0H/ZpiBw==" crossorigin="anonymous" referrerpolicy="no-referrer">
<link rel="stylesheet" href="{{ asset('css/ebili-theme.css') }}">
<script src="https://cdn.jsdelivr.net/npm/browser-image-compression@2.0.2/dist/browser-image-compression.js"></script>
<script src="https://cdn.jsdelivr.net/npm/jsqr@1.4.0/dist/jsQR.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/qrcode-generator/1.4.4/qrcode.min.js"></script>


@section('content_header')
<div class="text-center mb-4 fade-in">
    {{-- QR Code Container for Member Transfer --}}
    <div class="qr-container mx-auto mb-3" style="position: relative; max-width: 300px;">
        <div class="qr-code-wrapper" style="background: rgb(249, 247, 247); padding: 15px; border-radius: 20px; box-shadow: 0 8px 25px rgba(167, 173, 174, 0.3); display: inline-block; width: 100%;">
            {{-- QR Code Display Area --}}
            <div id="qrCodeDisplay" style="width: 142.5px; height: 142.5px; border-radius: 10px; display: flex; align-items: center; justify-content: center; background: #ffffff; border: 3px solid #212022; margin: 0 auto;">
                <div class="text-center">
                    <i class="bi bi-qr-code fa-3x text-primary mb-2"></i>
                    <div class="small text-muted" style="font-size: 0.75rem;">Generating QR...</div>
                </div>
            </div>

            {{-- Member Info Display --}}
            <div class="member-info mt-2 text-center">
                <div class="fw-bold" style="color: var(--primary-black); font-size: 0.85rem;">{{ auth()->user()->name }}</div>
                <div class="text-muted" style="font-size: 0.75rem;">{{ auth()->user()->mobile_number }}</div>
            </div>

            <div class="qr-overlay text-center mt-2" style="background: var(--accent-gold); color: var(--dark-purple); padding: 4px 12px; border-radius: 15px; font-size: 0.7rem; font-weight: 500; margin: 0 auto; display: inline-block;">
              &raquo;  Scan to Send Money
            </div>
        </div>

        {{-- QR Code Action Buttons --}}
        <div class="qr-actions mt-2 d-flex justify-content-center gap-2">
            <button type="button" class="btn btn-sm btn-outline-primary" id="shareReferralLink" title="Share Referral Link" style="font-size: 0.75rem; padding: 0.25rem 0.5rem;">
                <i class="bi bi-person-plus"></i> Share Referral
            </button>
            <button type="button" class="btn btn-sm btn-outline-success" id="downloadQRCode" title="Download QR Code" style="font-size: 0.75rem; padding: 0.25rem 0.5rem;">
                <i class="bi bi-download"></i> Download
            </button>
        </div>
    </div>

    {{-- <small class="text-muted d-block">Welcome back!</small>
    <h2 class="fw-bold mb-2" style="color: var(--primary-purple);">{{ strtoupper(auth()->user()->name) }}</h2>
    <p class="slogan mb-0" style="font-size: 0.9rem;">{{ strtoupper(auth()->user()->mobile_number) }}</p> --}}

    @if(isset($latestWin) && $latestWin && $latestWin->program && $latestWin->status === 'unclaimed')
    <div class="alert alert-success alertz-dismissible fade show slide-up mt-3" role="alert">
        🎉 <strong>Congratulations!</strong> You won in the
        <strong>{{ $latestWin->program->title }}</strong> reward program!
        <br>
        🗓 Drawn on: {{ \Carbon\Carbon::parse($latestWin->drawn_at)->format('F d, Y') }}
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
    @endif
</div>
@stop

@section('css')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    <style>
        /* Floating Cart Styles */
                .floating-cart {
                    position: fixed;
                    bottom: 15px;
                    right: 15px;
                    z-index: 1000;
                    background: linear-gradient(135deg, #6f42c1 0%, #8e44ad 100%);
                    border-radius: 50%;
                    width: 60px;
                    height: 60px;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    box-shadow: 0 4px 15px rgba(111, 66, 193, 0.4);
                    cursor: pointer;
                    transition: all 0.3s ease;
                    animation: bounceIn 0.5s ease;
                }

                .floating-cart:hover {
                    transform: scale(1.1);
                    box-shadow: 0 6px 20px rgba(111, 66, 193, 0.6);
                }

                .cart-icon {
                    position: relative;
                    color: white;
                    font-size: 24px;
                }

                .cart-count {
                    position: absolute;
                    top: -8px;
                    right: -8px;
                    background: #ff4757;
                    color: white;
                    border-radius: 50%;
                    width: 22px;
                    height: 22px;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    font-size: 12px;
                    font-weight: bold;
                    min-width: 22px;
                }

        /* Product Card Styles */
                .product-card {
                    transition: transform 0.2s ease, box-shadow 0.2s ease;
                }

                .product-card:hover {
                    transform: translateY(-2px);
                    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
                }

        @keyframes bounceIn {
            0% {
                transform: scale(0);
                opacity: 0;
            }
            50% {
                transform: scale(1.2);
            }
            100% {
                transform: scale(1);
                opacity: 1;
            }
        }

        @keyframes pulse {
            0% {
                transform: scale(1);
            }
            50% {
                transform: scale(1.1);
            }
            100% {
                transform: scale(1);
            }
        }

        .cart-pulse {
            animation: pulse 0.6s ease;
        }

        /* Back to Top Button Styles */
                .back-to-top {
                    position: fixed;
                    bottom: 80px;
                    right: 15px;
                    z-index: 999;
                    background: linear-gradient(135deg, #2957d8 0%, #0e269e 100%);
                    border-radius: 50%;
                    width: 45px;
                    height: 45px;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    box-shadow: 0 4px 10px rgba(251, 252, 255, 0.4);
                    cursor: pointer;
                    transition: all 0.3s ease;
                    animation: fadeInUp 0.5s ease;
                }

                .back-to-top:hover {
                    transform: scale(1.1) translateY(-2px);
                    box-shadow: 0 6px 20px rgba(28, 80, 211, 0.6);
                }

                .back-to-top i {
                    color: white;
                    font-size: 18px;
                    font-weight: bold;
                }

        @keyframes fadeInUp {
            0% {
                opacity: 0;
                transform: translateY(20px);
            }
            100% {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes fadeOutDown {
            0% {
                opacity: 1;
                transform: translateY(0);
            }
            100% {
                opacity: 0;
                transform: translateY(20px);
            }
        }

        .back-to-top.fade-out {
            animation: fadeOutDown 0.3s ease;
        }

        /* Mobile optimizations */
                @media (min-width: 768px) {
                    .back-to-top {
                        bottom: 90px;
                        right: 20px;
                        width: 50px;
                        height: 50px;
                    }

                    .back-to-top i {
                        font-size: 20px;
                    }
                }
    </style>
@endsection

@section('js')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <script src="{{ asset('js/mobile-ui.js') }}"></script>

    {{-- Enhanced Success/Error Messages with Toast Only --}}
    @if(session('success'))
        <script>
            console.log('Success session found:', "{{ session('success') }}");

            // Approach 1: Toastr
            document.addEventListener('DOMContentLoaded', function () {
                if (typeof toastr !== 'undefined') {
                    console.log('Toastr is available, showing success message');
                    toastr.success("{{ session('success') }}", "Success", {
                        timeOut: 5000,
                        progressBar: true,
                        positionClass: 'toast-top-right',
                        closeButton: true
                    });
                } else {
                    console.log('Toastr not available, using fallback');
                    // Approach 2: Native browser alert as fallback
                    alert("Success: {{ session('success') }}");
                }
            });
        </script>
    @endif

    @if(session('error'))
        <script>
            console.log('Error session found:', "{{ session('error') }}");

            // Approach 1: Toastr
            document.addEventListener('DOMContentLoaded', function () {
                if (typeof toastr !== 'undefined') {
                    console.log('Toastr is available, showing error message');
                    toastr.error("{{ session('error') }}", "Error", {
                        timeOut: 7000,
                        progressBar: true,
                        positionClass: 'toast-top-right',
                        closeButton: true
                    });
                } else {
                    console.log('Toastr not available, using fallback');
                    // Approach 2: Native browser alert as fallback
                    alert("Error: {{ session('error') }}");
                }
            });
        </script>
    @endif
@endsection



@section('content')
<div class="container-fluid px-2 px-sm-3">

{{-- Success/Error Messages - HTML Approach (Removed to prevent duplicate messages) --}}
@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show mb-3" role="alert">
        <i class="bi bi-check-circle me-2"></i>
        <strong>Success!</strong> {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show mb-3" role="alert">
        <i class="bi bi-exclamation-triangle me-2"></i>
        <strong>Error!</strong> {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif



{{-- ✅ Wallet and Cashback Balances in a Row --}}
<div class="row mb-4 g-2">
    {{-- Main Wallet --}}
    <div class="col-6">
        <a href="{{ route('wallet.history', ['type' => 'main']) }}" class="text-decoration-none">
            <div class="wallet-card p-2 p-sm-3 text-center h-100" style="color: white !important; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
                <small class="text-uppercase d-block" style="font-size: 0.6rem; opacity: 0.9; color: rgba(255, 255, 255, 0.9) !important;">Available Balance</small>
                @isset($wallet)
                    <h5 class="fw-bold mt-1 mb-1" style="color: white !important; font-size: 1rem;">₱{{ number_format($wallet->balance, 2) }}</h5>
                @endisset
                <div class="mt-1 small" style="color: rgba(255, 255, 255, 0.8) !important; font-size: 0.65rem;"><i class="bi bi-clock-history me-1"></i> View History</div>
            </div>
        </a>
    </div>

    {{-- Cashback Wallet --}}
    <div class="col-6">
    <a href="{{ route('wallet.history', ['type' => 'cashback']) }}" class="text-decoration-none" style="text-decoration: none;">
            <div class="cashback-card p-2 p-sm-3 text-center h-100" style="color: rgb(255, 255, 255) !important; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
                <small class="text-uppercase d-block" style="font-size: 0.6rem; opacity: 0.9; color: rgba(255, 255, 255, 0.9) !important;">Cashback Wallet</small>
                @php
                    $cashback = auth()->user()->member->cashbackWallet;
                @endphp
                <h5 class="fw-bold mt-1 mb-1" style="color: rgb(255, 255, 255) !important; font-size: 1rem;">₱{{ number_format($cashback?->balance ?? 0, 2) }}</h5>
                <div class="mt-1 small" style="color: rgba(255, 255, 255, 0.8) !important; font-size: 0.65rem;"><i class="bi bi-clock-history me-1"></i> View Cashback</div>
            </div>
        </a>
    </div>
</div>

{{-- ✅ Referral Network Card --}}
<div class="row mb-4">
  <div class="col-12">
    <div class="card shadow-sm" style="background: linear-gradient(135deg, #28a745 0%, #20c997 100%); border: none;">
      <div class="card-body text-white p-3">
        <div class="row align-items-center">
          <div class="col-8">
            <h6 class="card-title mb-1" style="color: white !important; font-size: 0.9rem;">
              <i class="bi bi-people-fill me-1"></i>My Referral Network
            </h6>
            <p class="mb-0 small" style="color: rgba(255,255,255,0.9); font-size: 0.75rem;">
              <span style="white-space: nowrap;">Direct Referrals:</span>
              <strong>{{ auth()->user()->member->getDirectReferrals()->count() }}</strong>
            </p>
            <p class="mb-0 small" style="color: rgba(255,255,255,0.9); font-size: 0.75rem;">
              Total Network: <strong>{{ auth()->user()->member->getTotalReferralCount() }}</strong>
            </p>
          </div>
          <div class="col-4 text-end">
            <a href="{{ route('member.referral.summary') }}" class="btn btn-light btn-sm" style="font-size: 0.75rem; padding: 0.25rem 0.5rem;">
              <i class="bi bi-eye me-1"></i> View
            </a>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>



{{-- ✅ Dashboard Action Icons (Mobile-Optimized Inline Layout) --}}
<div class="d-flex flex-wrap justify-content-around text-center mb-4" style="gap: 0.5rem;">

        {{-- Send --}}
        <a href="#" data-toggle="modal" data-target="#sendModal" class="text-decoration-none text-dark" style="flex: 0 0 30%; max-width: 30%;">
            <div class="card shadow-sm p-2 p-sm-3">
                <i class="bi bi-send fa-lg text-primary mb-1"></i>
                <div class="small" style="font-size: 0.75rem;">Send</div>
            </div>
        </a>


{{-- Borrow --}}
@if(auth()->user()->member->loan_eligible)
    <a href="#" data-toggle="modal" data-target="#borrowModal" class="text-decoration-none text-dark" style="flex: 0 0 30%; max-width: 30%;">
        <div class="card shadow-sm p-2 p-sm-3">
            <i class="bi bi-cash-coin fa-lg text-danger mb-1"></i>
            <div class="small" style="font-size: 0.75rem;">Borrow</div>
        </div>
    </a>
@else
    <a href="javascript:void(0)" class="text-decoration-none text-muted" style="flex: 0 0 30%; max-width: 30%;" tabindex="0" data-toggle="popover" data-trigger="focus" title="Notice!" data-content="You are currently not eligible to borrow.">
        <div class="card shadow-sm p-2 p-sm-3" style="opacity: 0.5;">
            <i class="bi bi-cash-coin fa-lg mb-1"></i>
            <div class="small" style="font-size: 0.75rem;">Borrow</div>
        </div>
    </a>
@endif

{{-- Cash In --}}
<a href="#" data-toggle="modal" data-target="#cashinModal" class="text-decoration-none text-dark" style="flex: 0 0 30%; max-width: 30%;">
    <div class="card shadow-sm p-2 p-sm-3">
        <i class="bi bi-wallet2 fa-lg text-secondary mb-1"></i>
        <div class="small" style="font-size: 0.75rem;">Cash In</div>
    </div>
</a>

    {{-- Network --}}
    <a href="{{ route('genealogy.index') }}" class="text-decoration-none text-dark" style="flex: 0 0 30%; max-width: 30%;">
        <div class="card shadow-sm p-2 p-sm-3">
            <i class="bi bi-diagram-3 fa-lg text-info mb-1"></i>
            <div class="small" style="font-size: 0.75rem;">Network</div>
        </div>
    </a>

    {{-- Register --}}
    <a href="{{ route('member.register.form') }}" class="text-decoration-none text-dark" style="flex: 0 0 30%; max-width: 30%;">
        <div class="card shadow-sm p-2 p-sm-3">
             <i class="bi bi-person-plus fa-lg text-success mb-1"></i>
            <div class="small" style="font-size: 0.75rem;">Register</div>
        </div>
    </a>

    {{-- Request Membership Codes --}}
    <a href="{{ route('member.membership-code-request.create') }}" class="text-decoration-none text-dark" style="flex: 0 0 30%; max-width: 30%;">
        <div class="card shadow-sm p-2 p-sm-3">
             <i class="bi bi-upc-scan fa-lg text-info mb-1"></i>
            <div class="small" style="font-size: 0.75rem;">Codes</div>
        </div>
    </a>

    {{-- Become a Merchant --}}
    <a href="#" data-toggle="modal" data-target="#merchantRegisterModal" class="text-decoration-none text-dark" style="flex: 0 0 30%; max-width: 30%;">
        <div class="card shadow-sm p-2 p-sm-3">
             <i class="bi bi-shop fa-lg text-success mb-1"></i>
            <div class="small" style="font-size: 0.75rem;">Merchant</div>
        </div>
    </a>

</div>

</div>

{{-- Category Carousel --}}
<div class="mb-4">
    <h4 class="section-title text-center">Categories</h4>
    <div class="category-carousel">
        <div class="category-item active" data-category-id="all">
            <i class="bi bi-grid-3x3-gap-fill mb-1" style="font-size: 1.25rem;"></i>
      <div class="fw-bold text-white" style="font-size: 0.75rem;">All</div>
        </div>
        @foreach($categories as $category)
            <div class="category-item" data-category-id="{{ $category->id }}">
                @if($category->image)
                    <img src="{{ asset('storage/' . $category->image) }}" alt="{{ $category->name }}"
                         style="width: 30px; height: 30px; object-fit: cover; border-radius: 50%;" class="mb-1">
                @else
                    <i class="bi bi-tag-fill mb-1" style="font-size: 1.25rem;"></i>
                @endif
                <div class="fw-bold" style="font-size: 0.75rem;">{{ $category->name }}</div>
            </div>
        @endforeach
    </div>
</div>

{{-- 🛍️ Featured Products --}}
@if($products->count())
    <h4 class="section-title text-center">🛍️ Market Place</h4>

    <div class="row row-cols-2 row-cols-sm-3 row-cols-md-4 row-cols-lg-5 g-2 mb-4">
        @foreach($products as $product)
            <div class="col">
                <a href="{{ route('shop.show', $product) }}" class="text-decoration-none">
                    <div class="product-card fade-in h-100"
                         data-category-id="{{ $product->category_id }}"
                         style="cursor: pointer;">
                        {{-- Thumbnail --}}
                        @if($product->thumbnail)
                            <div data-fancybox="thumb-{{ $product->id }}" href="{{ asset('storage/' . $product->thumbnail) }}">
                                <img src="{{ asset('storage/' . $product->thumbnail) }}"
                                     class="product-image"
                                     alt="{{ $product->name }}"
                                     style="height: 100px;">
                            </div>
                        @else
                            <div class="product-placeholder">
                                <div>
                                    <i class="bi bi-box-seam fs-3 mb-1"></i>
                                    <div class="text-dark" style="font-size: 0.8rem;">{{ $product->name }}</div>
                                </div>
                            </div>
                        @endif

                        {{-- Details --}}
                        <div class="p-2">
                            <h6 class="fw-bold mb-1 text-truncate text-dark" title="{{ $product->name }}" style="font-size: 0.85rem;">
                                {{ $product->name }}
                            </h6>

                            <p class="text-muted small mb-1 text-truncate" title="{{ $product->description }}" style="font-size: 0.7rem;">
                                {{ Str::limit($product->description, 25) }}
                            </p>

                            <div class="mt-auto">
                                {{-- Price Display with Discount --}}
                                @if($product->hasDiscount())
                                    <div class="d-flex align-items-center gap-1 mb-1">
                                        <span class="price-tag" style="font-size: 0.85rem;">₱{{ number_format($product->getDiscountedPrice(), 2) }}</span>
                                        <small class="text-muted text-decoration-line-through" style="font-size: 0.65rem;">₱{{ number_format($product->price, 2) }}</small>
                                        <span class="badge bg-danger" style="font-size: 0.55rem;">-{{ $product->getDiscountPercentage() }}%</span>
                                    </div>
                                @else
                                    <div class="price-tag mb-1" style="font-size: 0.85rem;">₱{{ number_format($product->price, 2) }}</div>
                                @endif

                                <span class="badge bg-info text-dark rounded-pill" style="font-size: 0.55rem;">
                                    Cashback: ₱{{ number_format($product->cashback_amount, 2) }}
                                </span>
                                <small class="text-muted d-block mt-1" style="font-size: 0.6rem;">Level 1 to Level {{ $product->cashback_max_level }}</small>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
        @endforeach
    </div>

    <div class="text-center mb-4">
        <a href="{{ url('/shop') }}" class="btn btn-outline-primary">
            <i class="bi bi-bag me-1"></i> View All Products
        </a>
    </div>
    <br>
@else
    <div class="alert alert-info text-center fade-in">
        <i class="bi bi-info-circle me-2"></i>No products available at the moment.
    </div>
@endif
{{-- ✅ Send Modal --}}
<div class="modal fade" id="sendModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <form method="POST" action="{{ route('wallet.transfer') }}">
            @csrf
            <input type="hidden" name="_modal" value="send">
            <div class="modal-content">
                <div class="modal-header">
                    <h6 class="modal-title text-white">Send to any member account</h6>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>

                <div class="modal-body">
                    @if ($errors->any() && old('_modal') === 'send')
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <label>Mobile Number</label>
                    <div class="input-group">
                        <input type="text" name="mobile_number" id="sendMobileNumber" class="form-control"
                               value="{{ old('mobile_number') }}"
                               maxlength="11" minlength="11"
                               pattern="^09\d{9}$"
                               oninput="this.value = this.value.replace(/[^0-9]/g, '')"
                               required
                               placeholder="e.g. 09123456789">
                        <div class="input-group-append">
                            <button type="button" class="btn btn-outline-secondary" id="scanQRSend" title="Scan QR Code">
                                <i class="bi bi-qr-code"></i>
                            </button>
                        </div>
                    </div>

                    <small id="recipientName" class="text-muted d-block mt-1"></small>

                    {{-- QR Scanner for Send --}}
                                        <div id="qrScannerSend" class="mt-3" style="display: none;">
                                            <div class="text-center">
                                                <video id="qrVideoSend" width="100%" height="150" style="border: 1px solid #ccc; border-radius: 8px;"></video>
                                                <div class="mt-2">
                                                    <button type="button" class="btn btn-sm btn-secondary" id="stopScanSend" style="font-size: 0.75rem; padding: 0.25rem 0.5rem;">Stop Scanning</button>
                                                </div>
                                            </div>
                                        </div>

                  <label class="mt-3">Amount</label>
<small class="d-block text-muted">Available: ₱{{ number_format($wallet->balance, 2) }}</small>
<input type="number" step="0.01" name="amount" class="form-control" value="{{ old('amount') }}" required>


                    <div class="alert mt-3 small" style="background-color: #fff9db; color: #856404;" role="alert">
                        Confirmed transactions will not be refunded. Please make sure the mobile number and amount are correct.
                    </div>

                    <div class="form-check mt-2">
                        <input type="checkbox" class="form-check-input" id="confirmSendCheckbox">
                        <label class="form-check-label" for="confirmSendCheckbox">I confirm that the details are correct.</label>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary" id="sendButton" disabled>Send</button>
                </div>
            </div>
        </form>
    </div>
</div>

{{-- ✅ Borrow Modal --}}
<div class="modal fade" id="borrowModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <form method="POST" action="{{ route('loan.request') }}">
            @csrf
            <div class="modal-content text-wnite ">
                <div class="modal-header text-white">
                    <h6 class="modal-title text-white "> Borrow Money</h6>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>

                <div class="modal-body">
                    {{-- Monthly Due Preview --}}
<div id="monthlyPreview" class="mt-3 d-none">
    <div class="alert alert-info small mb-0">
        Estimated Monthly Payment: <strong id="monthlyAmount">₱0.00</strong>
    </div>
</div>

                    @if ($errors->any() && old('_modal') === 'borrow')
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <input type="hidden" name="_modal" value="borrow">

                    {{-- Amount --}}
                    <label>Amount</label>
                    <input type="number" step="0.01" name="amount" class="form-control" value="{{ old('amount') }}" required>

                    {{-- Terms --}}
                    <label class="mt-3">Terms</label>
                    <select name="term_months" class="form-control" required>
                        <option value="" disabled selected>Select Term</option>
                        <option value="6" {{ old('term_months') == '6' ? 'selected' : '' }}>6 Months</option>
                        <option value="12" {{ old('term_months') == '12' ? 'selected' : '' }}>12 Months</option>
                    </select>

                    {{-- Purpose --}}
                    <label class="mt-3">Purpose <small class="text-muted">(optional)</small></label>
                    <input type="text" name="purpose" class="form-control" placeholder="e.g., School Fees, Vacation, etc." value="{{ old('purpose') }}">

                    <div class="alert alert-warning mt-3 small" role="alert">
                        Your loan request will be subject to approval. Make sure the amount and term are correct before submitting.
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="submit" class="btn btn-danger">Borrow</button>
                </div>
            </div>
        </form>
    </div>
</div>



{{-- ✅ Cash In Modal --}}
<div class="modal fade" id="cashinModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        {{-- <form action="{{ route('wallet.cashin') }}" method="POST"> --}}
            <form action="{{ route('wallet.cashin') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h6 class="modal-title text-white">Request Cash In</h6>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>

                <div class="modal-body">
               {{-- Payment Method --}}
<label class="mt-3">Payment Method</label>
<select name="payment_method" class="form-control" id="paymentMethodSelect" required>
    <option value="" disabled selected>Select Payment Method</option>
    <option value="GCash">GCash</option>
    <option value="Bank">Bank Transfer</option>
    <option value="Others">Others</option>
</select>

{{-- QR Scanner for Cash In --}}
<div class="mt-3">
    <label>Scan QR Code or Enter Amount</label>
    <div class="input-group">
        <input type="number" class="form-control" name="amount" id="cashinAmount" placeholder="Enter amount" value="{{ old('amount') }}">
        <div class="input-group-append">
            <button type="button" class="btn btn-outline-secondary" id="scanQRCashin" title="Scan QR Code">
                <i class="bi bi-qr-code"></i>
            </button>
        </div>
    </div>

    {{-- QR Scanner for Cash In --}}
        <div id="qrScannerCashin" class="mt-3" style="display: none;">
            <div class="text-center">
                <video id="qrVideoCashin" width="100%" height="150" style="border: 1px solid #ccc; border-radius: 8px;"></video>
                <div class="mt-2">
                    <button type="button" class="btn btn-sm btn-secondary" id="stopScanCashin" style="font-size: 0.75rem; padding: 0.25rem 0.5rem;">Stop Scanning</button>
                </div>
            </div>
        </div>
</div>

{{-- GCash Instruction Area --}}
<div id="gcashCollapse" class="mt-3" style="display: none;">
    <div class="card border rounded shadow-sm p-2 p-sm-3">
            <div class="text-center">
        <label class="fw-bold d-block mb-1" style="font-size: 0.9rem;">Scan GCash QR Code</label>
    <small class="text-muted d-block mb-1" style="font-size: 0.75rem;">
        Use this QR in GCash to complete your cash in request.
    </small>

    <img src="{{ asset('images/gcashQR.jpeg') }}" alt="GCash QR Code"
         class="img-fluid rounded shadow-sm mb-1" style="max-width: 150px;">
    <small class="text-muted d-block mb-1" style="font-size: 0.75rem;">GCash Account: <strong> LU*** CAB*</strong> </small>
    <a href="{{ asset('images/gcashQR.jpeg') }}" download="GCash-QR-Code.jpeg" class="btn btn-sm btn-primary" style="font-size: 0.75rem; padding: 0.25rem 0.5rem;">
        <i class="bi bi-download"></i> Download QR
    </a>
            </div>
            <label class="mt-2" style="font-size: 0.85rem;">Amount Sent<small class="d-block" style="font-size: 0.7rem;">(must match reference or uploaded file)</small></label>
            <input type="number" class="form-control" name="amount" value="{{ old('amount') }}">

            <label class="mt-2" style="font-size: 0.85rem;">Reference / Notes</label>
            <input type="text" class="form-control" name="gcash_note" placeholder="Fill above amount then upload proof" value="{{ old('note') }}" style="font-size: 0.85rem;">
            <small class="text-muted" style="font-size: 0.75rem;">Make sure the amount matches what you sent via GCash.</small>
        </div>
</div>

{{-- Bank Instruction Area --}}
<div id="bankCollapse" class="mt-3" style="display: none;">
    <div class="card border rounded shadow-sm p-3">
        <label class="fw-bold mb-2">Bank Information</label>
        <div class="d-flex align-items-center mb-2">
            <img src="{{ asset('images/bdo-logo.png') }}" alt="BDO Logo" class="me-2" style="max-width: 60px;">
            <div>
                <div><strong>Bank:</strong> BDO</div>
                <div><strong>Account Name:</strong> Amigos'98</div>
                <div>
                    <strong>Account No:</strong>
                    <span id="bankAccount">0071 5801 3083</span>
                    <button type="button"
                        class="btn btn-sm btn-outline-secondary py-0 px-1 ms-1"
                        onclick="copyBankAccount()">
                        <i class="bi bi-clipboard"></i>
                    </button>
                </div>
            </div>
        </div>
        <small class="text-muted">Please send the exact amount and upload the proof below.</small>
    </div>
</div>

<script>
    function copyBankAccount() {
        const accountNumber = document.getElementById('bankAccount').innerText.trim();
        navigator.clipboard.writeText(accountNumber).then(() => {
            // Simple toast-like feedback
            const toast = document.createElement('div');
            toast.innerText = 'Account number copied!';
            toast.className = 'position-fixed bottom-0 start-50 translate-middle-x bg-dark text-white px-3 py-2 rounded shadow-sm';
            toast.style.zIndex = 9999;
            document.body.appendChild(toast);
            setTimeout(() => toast.remove(), 2000);
        }).catch(err => {
            alert('Failed to copy account number');
            console.error(err);
        });
    }
</script>


{{-- Bank Collapse --}}
<div id="bankCollapse" class="collapse mt-3">
    <label class="fw-bold">Bank Information:</label>
    <div class="d-flex align-items-center mb-2">
        <img src="{{ asset('images/bdo-logo.png') }}" alt="BDO Logo" style="max-width: 60px; margin-right: 10px;">
        <div>
            <div><strong>BDO</strong></div>
            <div>Account Name: Amigos '98' Online</div>
            <div>Account No: 0071 5801 3083</div>
        </div>
    </div>
</div>


                    {{-- Proof of Payment --}}
<label class="mt-3">Upload Proof of Payment <small class="text-muted">(optional)</small></label>
<input type="file" name="proof" id="proofInput" class="form-control" accept="image/*">
<small class="text-muted">Accepted: JPG/PNG. Max size: 2MB.</small>
<div id="proofPreview" class="mt-2"></div>

                    {{-- Optional Notes --}}
                    <label class="mt-3">Notes <small class="text-muted">(optional)</small></label>
                    <input type="text" name="note" class="form-control" placeholder="e.g., Reference number, time sent">
                </div>

                <div class="modal-footer">
                    <button type="submit" class="btn btn-success">Request</button>
                </div>
            </div>
        </form>
    </div>
</div>

</div>

{{-- Floating Cart Basket --}}
<div id="floatingCart" class="floating-cart" style="display: none;">
    <div class="cart-icon">
        <i class="bi bi-basket"></i>
        <span class="cart-count" id="cartCount">0</span>
    </div>
</div>

{{-- Floating Back to Top Button --}}
<div id="backToTop" class="back-to-top" style="display: none;">
    <i class="bi bi-arrow-up"></i>
</div>

@stop



{{-- 📱 Reusable Mobile Footer --}}
{{-- @include('partials.mobile-footer') --}}

@push('js')
<script>
$(document).ready(function () {
    // Show modal if validation fails
    @if ($errors->any() && old('_modal') === 'send')
        $('#sendModal').modal('show');
    @endif

    // Enable/disable send button
    $('#confirmSendCheckbox').on('change', function () {
        $('#sendButton').prop('disabled', !this.checked);
    });

    // AJAX recipient preview
    $('input[name="mobile_number"]').on('blur', function () {
        let number = $(this).val();
        if (number.length > 5) {
            $.get("{{ url('/api/member-name') }}/" + number, function (data) {
                const fullName = data.full_name || '';
                const parts = fullName.trim().split(' ');
                if (parts.length >= 2) {
                    const first = parts[0].slice(0, 2) + '***';
                    const last = parts[1].charAt(0) + '***';
                    $('#recipientName').text('Send to ' + (first + ' ' + last).toUpperCase());
                } else {
                    $('#recipientName').text('Send to ***');
                }
            }).fail(() => {
                $('#recipientName').text('No Record found. Please check the number.');
            });
        }
    });
});
</script>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const availableBalance = parseFloat({{ $wallet->balance ?? 0 }});
        const amountInput = document.querySelector('input[name="amount"]');
        const checkbox = document.getElementById('confirmSendCheckbox');
        const sendButton = document.getElementById('sendButton');

        function validateSendForm() {
            const amount = parseFloat(amountInput.value);
            const isChecked = checkbox.checked;

            const hasEnoughBalance = !isNaN(amount) && amount <= availableBalance && amount > 0;

            sendButton.disabled = !(hasEnoughBalance && isChecked);

            // Show/hide warning
            let warning = document.getElementById('amountWarning');
            if (!hasEnoughBalance && amountInput.value) {
                if (!warning) {
                    warning = document.createElement('small');
                    warning.id = 'amountWarning';
                    warning.classList.add('text-danger', 'mt-1', 'd-block');
                    warning.innerText = 'Insufficient balance.';
                    amountInput.parentNode.insertBefore(warning, amountInput.nextSibling);
                }
            } else if (warning) {
                warning.remove();
            }
        }

        amountInput.addEventListener('input', validateSendForm);
        checkbox.addEventListener('change', validateSendForm);
    });
</script>

<script>
    $(function () {
        $('[data-toggle="popover"]').popover();
    });
</script>
@push('js')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const amountInput = document.querySelector('#borrowModal input[name="amount"]');
        const termSelect = document.querySelector('#borrowModal select[name="term_months"]');
        const previewContainer = document.getElementById('monthlyPreview');
        const previewAmount = document.getElementById('monthlyAmount');

        function updateMonthlyDue() {
            const amount = parseFloat(amountInput.value);
            const months = parseInt(termSelect.value);
            const interestRate = 5;

            if (!isNaN(amount) && amount > 0 && (months === 6 || months === 12)) {
                const total = amount + (amount * (interestRate / 100));
                const monthly = total / months;
                previewAmount.textContent = '₱' + monthly.toFixed(2);
        amountInput.addEventListener('input', validateSendForm);
        checkbox.addEventListener('change', validateSendForm);
    });
</script>

<script>
    $(function () {
        $('[data-toggle="popover"]').popover();
    });
</script>
@push('js')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const amountInput = document.querySelector('#borrowModal input[name="amount"]');
        const termSelect = document.querySelector('#borrowModal select[name="term_months"]');
        const previewContainer = document.getElementById('monthlyPreview');
        const previewAmount = document.getElementById('monthlyAmount');

        function updateMonthlyDue() {
            const amount = parseFloat(amountInput.value);
            const months = parseInt(termSelect.value);
            const interestRate = 5;

            if (!isNaN(amount) && amount > 0 && (months === 6 || months === 12)) {
                const total = amount + (amount * (interestRate / 100));
                const monthly = total / months;
                previewAmount.textContent = '₱' + monthly.toFixed(2);
                previewContainer.classList.remove('d-none');
            } else {
                previewContainer.classList.add('d-none');
            }
        }

        amountInput.addEventListener('input', updateMonthlyDue);
        termSelect.addEventListener('change', updateMonthlyDue);
    });
</script>
@endpush
{{-- script for self-send validate --}}
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const mobileInput = document.querySelector('input[name="mobile_number"]');
        const sendButton = document.getElementById('sendButton');
        const recipientName = document.getElementById('recipientName');

        if (mobileInput) {
            mobileInput.addEventListener('input', function () {
                const enteredNumber = this.value.replace(/\D/g, '');
                const senderNumber = "{{ auth()->user()->member->mobile_number }}";

                if (enteredNumber === senderNumber) {
                    recipientName.textContent = "⚠️ You cannot send to own account.";
                    recipientName.classList.add('text-danger');
                    sendButton.disabled = true;
                } else {
                    recipientName.textContent = "";
                    recipientName.classList.remove('text-danger');
                    sendButton.disabled = !document.getElementById('confirmSendCheckbox').checked;
                }
            });
        }

        const confirmSendCheckbox = document.getElementById('confirmSendCheckbox');
        confirmSendCheckbox.addEventListener('change', function () {
            const enteredNumber = mobileInput.value.replace(/\D/g, '');
            const senderNumber = "{{ auth()->user()->member->mobile_number }}";

            sendButton.disabled = !this.checked || enteredNumber === senderNumber;
        });
    });
</script>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const proofInput = document.getElementById('proofInput');
        const proofPreview = document.getElementById('proofPreview');

        proofInput.addEventListener('change', function () {
            const file = this.files[0];
            proofPreview.innerHTML = '';

            if (file && file.type.startsWith('image/')) {
                const reader = new FileReader();
                reader.onload = function (e) {
                    const img = document.createElement('img');
                    img.src = e.target.result;
                    img.style.maxWidth = '100%';
                    img.className = 'rounded shadow-sm';
                    proofPreview.appendChild(img);
                };
                reader.readAsDataURL(file);
            }
        });
    });
</script>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const methodSelect = document.getElementById('paymentMethodSelect');
    const gcashBox = document.getElementById('gcashCollapse');
    const bankBox = document.getElementById('bankCollapse');

    function togglePaymentBoxes() {
        gcashBox.style.display = 'none';
        bankBox.style.display = 'none';

        switch (methodSelect.value) {
            case 'GCash':
                gcashBox.style.display = 'block';
                break;
            case 'Bank':
                bankBox.style.display = 'block';
                break;
        }
    }

    togglePaymentBoxes();
    methodSelect.addEventListener('change', togglePaymentBoxes);
});
</script>

{{-- QR Code Scanner JavaScript --}}
<script>
class QRScanner {
    constructor(videoId, containerId, inputId, stopButtonId) {
        this.video = document.getElementById(videoId);
        this.container = document.getElementById(containerId);
        this.input = document.getElementById(inputId);
        this.stopButton = document.getElementById(stopButtonId);
        this.stream = null;
        this.scanning = false;
        this.canvas = document.createElement('canvas');
        this.context = this.canvas.getContext('2d');
    }

    async startScanning() {
        try {
            this.stream = await navigator.mediaDevices.getUserMedia({
                video: { facingMode: 'environment' }
            });
            this.video.srcObject = this.stream;
            this.video.play();
            this.container.style.display = 'block';
            this.scanning = true;
            this.scanFrame();
        } catch (error) {
            console.error('Error accessing camera:', error);
            alert('Unable to access camera. Please check permissions.');
        }
    }

    stopScanning() {
        this.scanning = false;
        if (this.stream) {
            this.stream.getTracks().forEach(track => track.stop());
        }
        this.container.style.display = 'none';
    }

    scanFrame() {
        if (!this.scanning) return;

        if (this.video.readyState === this.video.HAVE_ENOUGH_DATA) {
            this.canvas.height = this.video.videoHeight;
            this.canvas.width = this.video.videoWidth;
            this.context.drawImage(this.video, 0, 0, this.canvas.width, this.canvas.height);

            const imageData = this.context.getImageData(0, 0, this.canvas.width, this.canvas.height);
            const code = jsQR(imageData.data, imageData.width, imageData.height);

            if (code) {
                this.processQRCode(code.data);
                this.stopScanning();
                return;
            }
        }

        requestAnimationFrame(() => this.scanFrame());
    }

    processQRCode(data) {
        // Try to extract mobile number or amount from QR code
        console.log('QR Code data:', data);

        // Check if it's a payment request URL (highest priority)
        if (data.includes('/payment-request/')) {
            toastr.success('Payment request QR code detected! Redirecting...');
            setTimeout(() => {
                window.location.href = data;
            }, 1000);
            return;
        }

        // Try to parse as JSON first (for eBili member QR codes)
        try {
            const memberData = JSON.parse(data);
            if (memberData.type === 'ebili_transfer' && memberData.mobile && this.input.name === 'mobile_number') {
                this.input.value = memberData.mobile;
                this.input.dispatchEvent(new Event('blur'));
                toastr.success(`Scanned ${memberData.name}'s mobile number!`);
                return;
            }
            if (memberData.type === 'ebili_payment_request' && memberData.payment_url) {
                toastr.success('Payment request QR code detected! Redirecting...');
                setTimeout(() => {
                    window.location.href = memberData.payment_url;
                }, 1000);
                return;
            }
        } catch (e) {
            // Not JSON, continue with other processing
        }

        // Check if it's a mobile number (11 digits starting with 09)
        const mobileMatch = data.match(/09\d{9}/);
        if (mobileMatch && this.input.name === 'mobile_number') {
            this.input.value = mobileMatch[0];
            this.input.dispatchEvent(new Event('blur'));
            toastr.success('Mobile number scanned successfully!');
            return;
        }

        // Check if it's an amount (number with optional decimal)
        const amountMatch = data.match(/(\d+(?:\.\d{2})?)/);
        if (amountMatch && this.input.name === 'amount') {
            this.input.value = amountMatch[1];
            toastr.success('Amount scanned successfully!');
            return;
        }

        // If it's a URL or complex data, try to extract relevant info
        try {
            const url = new URL(data);
            const params = new URLSearchParams(url.search);

            // Check if it's a payment request URL
            if (url.pathname.includes('/payment-request/')) {
                toastr.success('Payment request QR code detected! Redirecting...');
                setTimeout(() => {
                    window.location.href = data;
                }, 1000);
                return;
            }

            if (params.has('mobile') && this.input.name === 'mobile_number') {
                this.input.value = params.get('mobile');
                this.input.dispatchEvent(new Event('blur'));
                toastr.success('Mobile number extracted from QR code!');
                return;
            }

            if (params.has('amount') && this.input.name === 'amount') {
                this.input.value = params.get('amount');
                toastr.success('Amount extracted from QR code!');
                return;
            }
        } catch (e) {
            // Not a URL, continue with other processing
        }

        // Fallback: just put the raw data in the input
        this.input.value = data;
        toastr.info('QR code scanned. Please verify the data.');
    }
}

// Initialize QR scanners when document is ready
document.addEventListener('DOMContentLoaded', function() {
    // Send Modal QR Scanner
    const sendScanner = new QRScanner('qrVideoSend', 'qrScannerSend', 'sendMobileNumber', 'stopScanSend');

    document.getElementById('scanQRSend').addEventListener('click', function() {
        sendScanner.startScanning();
    });

    document.getElementById('stopScanSend').addEventListener('click', function() {
        sendScanner.stopScanning();
    });

    // Cash In Modal QR Scanner
    const cashinScanner = new QRScanner('qrVideoCashin', 'qrScannerCashin', 'cashinAmount', 'stopScanCashin');

    document.getElementById('scanQRCashin').addEventListener('click', function() {
        cashinScanner.startScanning();
    });

    document.getElementById('stopScanCashin').addEventListener('click', function() {
        cashinScanner.stopScanning();
    });

    // Stop scanning when modals are closed
    $('#sendModal').on('hidden.bs.modal', function() {
        sendScanner.stopScanning();
    });

    $('#cashinModal').on('hidden.bs.modal', function() {
        cashinScanner.stopScanning();
    });
});

// Helper function for notifications
function showNotification(message, type = 'info') {
    if (typeof toastr !== 'undefined') {
        toastr[type](message);
    } else {
        alert(message);
    }
}

// Generate Member QR Code with Share/Download functionality
document.addEventListener('DOMContentLoaded', function() {
    const qrDisplay = document.getElementById('qrCodeDisplay');
    const shareBtn = document.getElementById('shareQRCode');
    const downloadBtn = document.getElementById('downloadQRCode');

    const memberData = {
        mobile: "{{ auth()->user()->mobile_number }}",
        name: "{{ auth()->user()->name }}",
        @if(isset($wallet))
        wallet_id: "{{ $wallet->wallet_id }}",
        @endif
        type: "ebili_payment_request",
        payment_url: "{{ route('payment.request', $wallet->wallet_id ?? 'unknown') }}"
    };

    // Create QR code data with member information for payment requests
    const qrData = memberData.payment_url;

    console.log('Generating QR code with data:', qrData);

    // Wait a bit for all scripts to load
    setTimeout(function() {
        generateQRCode();
    }, 1000);

    function generateQRCode() {

    // Function to create fallback display
    function createFallbackDisplay() {
        qrDisplay.innerHTML = `
            <div class="text-center" style="background: linear-gradient(135deg, #6f42c1 0%, #8e44ad 100%); width: 171px; height: 171px; border-radius: 15px; display: flex; flex-direction: column; align-items: center; justify-content: center; color: white;">
                <div style="font-size: 28px; font-weight: bold; margin-bottom: 8px;">{{ strtoupper(substr(auth()->user()->name, 0, 2)) }}</div>
                <div style="font-size: 12px; opacity: 0.8;">{{ auth()->user()->mobile_number }}</div>
                <div style="font-size: 10px; opacity: 0.6; margin-top: 8px;">Scan to Pay</div>
            </div>
        `;
    }

    // Generate QR code using qrcode-generator library
    try {
        console.log('Generating QR code with qrcode-generator library...');

        // Create QR code using qrcode-generator library
        const qr = qrcode(0, 'M');
        qr.addData(qrData);
        qr.make();

        // Create canvas
        const canvas = document.createElement('canvas');
        canvas.id = 'qrCanvas';
        const ctx = canvas.getContext('2d');

        const moduleCount = qr.getModuleCount();
        const cellSize = 6;
        const margin = 12;
        const size = moduleCount * cellSize + margin * 2;

        canvas.width = size;
        canvas.height = size;

        // Fill background
        ctx.fillStyle = '#FFFFFF';
        ctx.fillRect(0, 0, size, size);

        // Draw QR modules
        ctx.fillStyle = '#4a1570';  // Dark purple to match design
        for (let row = 0; row < moduleCount; row++) {
            for (let col = 0; col < moduleCount; col++) {
                if (qr.isDark(row, col)) {
                    ctx.fillRect(
                        col * cellSize + margin,
                        row * cellSize + margin,
                        cellSize,
                        cellSize
                    );
                }
            }
        }

        // Style the canvas to fit the design
        canvas.style.width = '171px';  // 5% smaller than 180px
        canvas.style.height = '171px';  // 5% smaller than 180px
        canvas.style.borderRadius = '15px';

        qrDisplay.innerHTML = '';
        qrDisplay.appendChild(canvas);

        console.log('QR code generated successfully with qrcode-generator');

    } catch (error) {
        console.error('QR generation failed:', error);
        generateQRCodeAlternative();
    }

    // Alternative QR generation method
    function generateQRCodeAlternative() {
        console.log('Using alternative QR generation method');
        try {
            // Use QR Server API as fallback
            const qrImg = document.createElement('img');
            qrImg.src = `https://api.qrserver.com/v1/create-qr-code/?size=171x171&data=${encodeURIComponent(qrData)}`;  // 5% smaller than 180x180
            qrImg.style.width = '150px';
            qrImg.style.height = '150px';
            qrImg.style.borderRadius = '15px';
            qrImg.onload = function() {
                qrDisplay.innerHTML = '';
                qrDisplay.appendChild(qrImg);
                console.log('QR Code generated using API fallback');
            };
            qrImg.onerror = function() {
                console.error('QR API fallback failed');
                createFallbackDisplay();
            };
        } catch (error) {
            console.error('Alternative QR generation failed:', error);
            createFallbackDisplay();
        }
    }

    } // Close generateQRCode function

    // Download QR Code functionality
    downloadBtn.addEventListener('click', function() {
        console.log('Download button clicked');
        try {
            // Get the current displayed QR code
            const currentQRCanvas = qrDisplay.querySelector('canvas');
            const currentQRImg = qrDisplay.querySelector('img');

            console.log('QR Canvas found:', !!currentQRCanvas);
            console.log('QR Image found:', !!currentQRImg);

            if (!currentQRCanvas && !currentQRImg) {
                console.log('No QR code elements found');
                showNotification('No QR code available to download', 'error');
                return;
            }

            // Create a high-quality canvas for download
            console.log('Creating download canvas...');
            const downloadCanvas = document.createElement('canvas');
            const downloadCtx = downloadCanvas.getContext('2d');

            if (!downloadCtx) {
                console.error('Failed to get 2D context');
                showNotification('Canvas not supported by browser', 'error');
                return;
            }

            downloadCanvas.width = 400;
            downloadCanvas.height = 500;
            console.log('Canvas created:', downloadCanvas.width, 'x', downloadCanvas.height);

            // White background with subtle gradient
            const gradient = downloadCtx.createLinearGradient(0, 0, 0, 500);
            gradient.addColorStop(0, '#ffffff');
            gradient.addColorStop(1, '#f8f9fa');
            downloadCtx.fillStyle = gradient;
            downloadCtx.fillRect(0, 0, 400, 500);

            // Add border
            downloadCtx.strokeStyle = '#e9ecef';
            downloadCtx.lineWidth = 2;
            downloadCtx.strokeRect(10, 10, 380, 480);

            // Draw E-Bili logo/title
            downloadCtx.fillStyle = '#4a1570';
            downloadCtx.font = 'bold 20px Poppins, Arial, sans-serif';
            downloadCtx.textAlign = 'center';
            downloadCtx.fillText('Amigos \'98 ', 200, 50);

            // Draw member name
            downloadCtx.font = 'bold 18px Poppins, Arial, sans-serif';
            downloadCtx.fillStyle = '#2c3e50';
            downloadCtx.fillText("{{ auth()->user()->name }}", 200, 80);

            // Draw mobile number
            downloadCtx.font = '16px Poppins, Arial, sans-serif';
            downloadCtx.fillStyle = '#6c757d';
            downloadCtx.fillText("{{ auth()->user()->mobile_number }}", 200, 105);

            @if(isset($wallet))
            // Draw wallet ID
            downloadCtx.font = '14px Poppins, Arial, sans-serif';
            downloadCtx.fillStyle = '#6c757d';
            downloadCtx.fillText("{{ $wallet->wallet_id }}", 200, 125);
            @endif

            // Create QR code area background
            downloadCtx.fillStyle = '#ffffff';
            downloadCtx.fillRect(85, 145, 230, 230);
            downloadCtx.strokeStyle = '#dee2e6';
            downloadCtx.lineWidth = 1;
            downloadCtx.strokeRect(85, 145, 230, 230);

            // Use the SAME QR code that's currently displayed
            console.log('Drawing QR code to canvas...');
            if (currentQRCanvas) {
                console.log('Using canvas QR code');
                try {
                    downloadCtx.drawImage(currentQRCanvas, 100, 160, 190, 190);  // 5% smaller than 200px
                    console.log('Canvas QR drawn successfully');
                    finishDownloadImage();
                } catch (error) {
                    console.error('Error drawing canvas QR:', error);
                    generateFallbackQR();
                }
            } else if (currentQRImg && currentQRImg.complete) {
                console.log('Using image QR code');
                try {
                    downloadCtx.drawImage(currentQRImg, 100, 160, 200, 200);
                    console.log('Image QR drawn successfully');
                    finishDownloadImage();
                } catch (error) {
                    console.error('Error drawing image QR:', error);
                    generateFallbackQR();
                }
            } else if (currentQRImg) {
                console.log('Waiting for image to load...');
                // Wait for image to load
                currentQRImg.onload = function() {
                    try {
                        downloadCtx.drawImage(currentQRImg, 100, 160, 200, 200);
                        console.log('Delayed image QR drawn successfully');
                        finishDownloadImage();
                    } catch (error) {
                        console.error('Error drawing delayed image QR:', error);
                        generateFallbackQR();
                    }
                };
            } else {
                console.log('No QR code found, using fallback');
                generateFallbackQR();
            }

            function generateFallbackQR() {
                // Draw fallback QR placeholder
                downloadCtx.fillStyle = '#6f42c1';
                downloadCtx.fillRect(100, 160, 190, 190);  // 5% smaller than 200px
                downloadCtx.fillStyle = '#ffffff';
                downloadCtx.font = 'bold 32px Arial, sans-serif';
                downloadCtx.textAlign = 'center';
                downloadCtx.fillText("{{ strtoupper(substr(auth()->user()->name, 0, 2)) }}", 200, 270);
                finishDownloadImage();
            }

            function finishDownloadImage() {
                console.log('Finishing download image...');
                try {
                    // Draw instructions
                    downloadCtx.font = '14px Poppins, Arial, sans-serif';
                    downloadCtx.fillStyle = '#495057';
                    downloadCtx.textAlign = 'center';
                    downloadCtx.fillText('Scan this QR code to send money', 200, 400);
                    downloadCtx.fillText('to this Amigos \'98 account', 200, 420);

                    // Draw footer
                    downloadCtx.font = 'bold 12px Poppins, Arial, sans-serif';
                    downloadCtx.fillStyle = '#4a1570';
                    downloadCtx.fillText('Amigos \'98 Online - Buy to Save, Share to Earn', 200, 450);

                    // Add timestamp
                    downloadCtx.font = '10px Arial, sans-serif';
                    downloadCtx.fillStyle = '#adb5bd';
                    downloadCtx.fillText('Generated on ' + new Date().toLocaleDateString(), 200, 470);

                    console.log('Text drawn, creating download link...');

                    // Download the image
                    const dataURL = downloadCanvas.toDataURL('image/png', 1.0);
                    console.log('Data URL created, length:', dataURL.length);

                    const link = document.createElement('a');
                    link.download = 'QR_{{ auth()->user()->mobile_number }}.png';
                    link.href = dataURL;

                    // Add to DOM temporarily for Firefox compatibility
                    document.body.appendChild(link);
                    link.click();
                    document.body.removeChild(link);

                    console.log('Download triggered successfully');
                    showNotification('QR Code downloaded successfully!', 'success');
                } catch (error) {
                    console.error('Error in finishDownloadImage:', error);
                    showNotification('Failed to create download: ' + error.message, 'error');
                }
            }

        } catch (error) {
            console.error('Download failed:', error);
            showNotification('Failed to download QR code: ' + error.message, 'error');
        }
    });

    // Share Referral Link functionality
    const shareReferralBtn = document.getElementById('shareReferralLink');
    shareReferralBtn.addEventListener('click', function() {
        try {
            // Create referral link
            const memberId = {{ auth()->user()->member->id ?? 'null' }};
            const referralUrl = `${window.location.origin}/join/${memberId}`;

            const shareText = `🎉 Join Amigos '98 Online through my referral link and we both get bonuses!\n\n` +
                             `👤 Referred by: {{ auth()->user()->name }}\n` +
                             `📱 Mobile: {{ auth()->user()->mobile_number }}\n\n` +
                             `💰 Benefits:\n` +
                             `• Shop and earn cashback\n` +
                             `• Refer friends and earn bonuses\n` +
                             `• Access to exclusive deals\n\n` +
                             `🔗 Register here: ${referralUrl}\n\n` +
                             `#AmigosOnline #ShopToSave #ShareToEarn`;

            if (navigator.share) {
                navigator.share({
                    title: 'Join Our team Online - Referral Invitation',
                    text: shareText,
                    url: referralUrl
                }).then(() => {
                    showNotification('Referral link shared successfully!', 'success');
                }).catch((error) => {
                    console.error('Share failed:', error);
                    fallbackShareReferral(shareText, referralUrl);
                });
            } else {
                fallbackShareReferral(shareText, referralUrl);
            }

        } catch (error) {
            console.error('Referral share failed:', error);
            showNotification('Failed to share referral link', 'error');
        }
    });

    function fallbackShare(text) {
        if (navigator.clipboard) {
            navigator.clipboard.writeText(text).then(() => {
                showNotification('QR code data copied to clipboard!', 'info');
            }).catch(() => {
                showNotification('Failed to copy QR code data', 'error');
            });
        } else {
            // Very old browser fallback
            const textArea = document.createElement('textarea');
            textArea.value = text;
            document.body.appendChild(textArea);
            textArea.select();
            try {
                document.execCommand('copy');
                showNotification('QR code data copied to clipboard!', 'info');
            } catch (err) {
                showNotification('Failed to copy QR code data', 'error');
            }
            document.body.removeChild(textArea);
        }
    }

    function fallbackShareReferral(text, url) {
        if (navigator.clipboard) {
            navigator.clipboard.writeText(text).then(() => {
                showNotification('Referral link copied to clipboard!', 'success');
            }).catch(() => {
                showNotification('Failed to copy referral link', 'error');
            });
        } else {
            // Very old browser fallback
            const textArea = document.createElement('textarea');
            textArea.value = text;
            document.body.appendChild(textArea);
            textArea.select();
            try {
                document.execCommand('copy');
                showNotification('Referral link copied to clipboard!', 'success');
            } catch (err) {
                showNotification('Failed to copy referral link', 'error');
            }
            document.body.removeChild(textArea);
        }
    }
});
</script>

{{-- Product Card Interaction --}}
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize cart count from session
    updateCartCount();
});

// Update cart count
function updateCartCount() {
    const cartCountElement = document.getElementById('cartCount');
    if (cartCountElement) {
        // Get count from session storage or default to 0
        const count = parseInt(sessionStorage.getItem('cartCount') || '0');
        cartCountElement.textContent = count;

        // Show/hide floating cart based on count
        const floatingCart = document.getElementById('floatingCart');
        if (floatingCart) {
            if (count > 0) {
                floatingCart.style.display = 'flex';
            } else {
                floatingCart.style.display = 'none';
            }
        }
    }
}

// Back to Top functionality with mobile fixes
document.addEventListener('DOMContentLoaded', function() {
    const backToTopButton = document.getElementById('backToTop');
    let isVisible = false;

    // Show/hide button based on scroll position
    function toggleBackToTop() {
        const scrollTop = window.pageYOffset || document.documentElement.scrollTop;
        const shouldShow = scrollTop > 300; // Show after scrolling 300px

        if (shouldShow && !isVisible) {
            backToTopButton.style.display = 'flex';
            backToTopButton.classList.remove('fade-out');
            isVisible = true;
        } else if (!shouldShow && isVisible) {
            backToTopButton.classList.add('fade-out');
            setTimeout(() => {
                backToTopButton.style.display = 'none';
                backToTopButton.classList.remove('fade-out');
            }, 300);
            isVisible = false;
        }
    }

    // Smooth scroll to top with mobile compatibility
    function scrollToTop(e) {
        e.preventDefault();
        e.stopPropagation();

        // Use modern smooth scrolling if supported
        if ('scrollBehavior' in document.documentElement.style) {
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        } else {
            // Fallback for older browsers
            const scrollDuration = 500;
            const scrollStep = -window.scrollY / (scrollDuration / 15);

            function scrollAnimation() {
                if (window.scrollY !== 0) {
                    window.scrollBy(0, scrollStep);
                    requestAnimationFrame(scrollAnimation);
                }
            }

            scrollAnimation();
        }
    }

    // Event listeners with passive option for better mobile performance
    window.addEventListener('scroll', toggleBackToTop, { passive: true });
    backToTopButton.addEventListener('click', scrollToTop);
    backToTopButton.addEventListener('touchstart', scrollToTop, { passive: false });

    // Initial check
    toggleBackToTop();
});

// Add CSS animations for custom toast
const style = document.createElement('style');
style.textContent = `
    @keyframes slideInRight {
        from {
            transform: translateX(100%);
            opacity: 0;
@include('members.merchant-register')
        }
        to {
            transform: translateX(0);
            opacity: 1;
        }
    }
@keyframes slideOutRight {
    from {
        transform: translateX(0);
        opacity: 1;
    }
    to {
        transform: translateX(100%);
        opacity: 0;
    }
}
`;
document.head.appendChild(style);
</script>

@include('members.merchant-register')

@endpush
{{-- Footer is handled by AdminLTE layout --}}



