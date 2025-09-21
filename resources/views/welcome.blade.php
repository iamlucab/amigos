<!DOCTYPE html>
<html lang="en" data-bs-theme="light">
<head>
    <meta charset="UTF-8">
    <title>Amigos Community Online Marketplace</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="mobile-web-app-capable" content="yes">
    {{-- PWA & Icons --}}
<link rel="icon" type="image/png" href="{{ asset('storage/icons/favicon-96x96.png') }}" sizes="96x96" />
<link rel="icon" type="image/svg+xml" href="{{ asset('storage/icons/favicon.ico') }}" />
<link rel="shortcut icon" href="{{ asset('storage/icons/favicon.ico') }}" />
<link rel="apple-touch-icon" sizes="180x180" href="{{ asset('storage/icons/apple-touch-icon.png') }}" />
<link rel="manifest" href="{{ asset('site.webmanifest') }}" />

<!-- iOS Splash Screens -->
<link rel="apple-touch-startup-image" href="{{ asset('storage/splash/splash-640x1136.png') }}" media="(device-width: 320px) and (device-height: 568px) and (-webkit-device-pixel-ratio: 2)">
<link rel="apple-touch-startup-image" href="{{ asset('storage/splash/splash-750x1334.png') }}" media="(device-width: 375px) and (device-height: 667px) and (-webkit-device-pixel-ratio: 2)">
<link rel="apple-touch-startup-image" href="{{ asset('storage/splash/splash-1242x2208.png') }}" media="(device-width: 414px) and (device-height: 736px) and (-webkit-device-pixel-ratio: 3)">
<link rel="apple-touch-startup-image" href="{{ asset('storage/splash/splash-1125x2436.png') }}" media="(device-width: 375px) and (device-height: 812px) and (-webkit-device-pixel-ratio: 3)">
<link rel="apple-touch-startup-image" href="{{ asset('storage/splash/splash-828x1792.png') }}" media="(device-width: 414px) and (device-height: 896px) and (-webkit-device-pixel-ratio: 2)">
<link rel="apple-touch-startup-image" href="{{ asset('storage/splash/splash-1242x2688.png') }}" media="(device-width: 414px) and (device-height: 896px) and (-webkit-device-pixel-ratio: 3)">
<link rel="apple-touch-startup-image" href="{{ asset('storage/splash/splash-1536x2048.png') }}" media="(min-device-width: 768px) and (max-device-width: 1024px) and (orientation: portrait) and (-webkit-device-pixel-ratio: 2)">
<link rel="apple-touch-startup-image" href="{{ asset('storage/splash/splash-1668x2224.png') }}" media="(device-width: 834px) and (device-height: 1112px) and (-webkit-device-pixel-ratio: 2)">
<link rel="apple-touch-startup-image" href="{{ asset('storage/splash/splash-2048x2732.png') }}" media="(device-width: 1024px) and (device-height: 1366px) and (-webkit-device-pixel-ratio: 2)">

    {{-- Bootstrap + Icons --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">

    {{-- Google Fonts --}}
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <style>
    :root,
    [data-bs-theme="light"] {
        --bs-body-bg: #f2f4f8;
        --bs-body-color: #000000;

        /* New blues */
        --primary-blue: #0084fe;
        --secondary-blue: #0b2f6c;
        --accent-color: #0b2f6c;
        --light-bg: #f2f4f8;
        --dark-text: #000000;

        /* Optionally tie into Bootstrap's primary */
        --bs-primary: var(--primary-blue);
        --bs-primary-hover: var(--secondary-blue);
    }

    /* Uniform button sizing for mobile */
    @media (max-width: 767px) {
        .product-action-btn {
            padding: 12px 8px;
            font-size: 0.85rem;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .product-action-btn .btn-text {
            display: block;
            font-size: 0.75rem;
        }

        .product-action-btn .btn-icon {
            font-size: 1rem;
        }
    }

    /* Ensure buttons have uniform height */
    .btn-uniform {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        height: 50px;
        padding: 5px;
    }

    .btn-uniform .btn-icon {
        font-size: 1.2rem;
        line-height: 1;
    }

    .btn-uniform .btn-text {
        font-size: 0.7rem;
        line-height: 1.2;
        margin-top: 2px;
    }

[data-bs-theme="dark"] {
    --bs-body-bg: #1a0d2e;
    --bs-body-color: #e8e3ff;

    /* Blues in dark mode (slightly lighter for contrast) */
    --primary-blue: #0b78df;
    --secondary-blue: #0b2f6c;
    --accent-color: #0b2f6c;
    --light-bg: #1a3d6b;
    --dark-text: #e8e3ff;

    --bs-primary: var(--primary-blue);
    --bs-primary-hover: var(--secondary-blue);
}

/* Example usage for buttons */
.btn-primary {
    background-color: var(--bs-primary);
    border-color: var(--bs-primary);
}

.btn-primary:hover {
    background-color: var(--bs-primary-hover);
    border-color: var(--bs-primary-hover);
}


body {
    font-family: 'Poppins', sans-serif;
    background: var(--bs-body-bg);
    color: var(--bs-body-color);
    transition: all 0.3s ease;
    min-height: 100vh;
    font-size: 16px; /* Base font size for better mobile readability */
    line-height: 1.5; /* Improved line height for readability */
}

/* Mobile Font Adjustments */
@media (max-width: 767px) {
    h1 { font-size: 1.75rem !important; }
    h2 { font-size: 1.5rem !important; }
    h3 { font-size: 1.35rem !important; }
    h4 { font-size: 1.25rem !important; }
    h5 { font-size: 1.15rem !important; }
    h6 { font-size: 1.1rem !important; }

    p, div, span {
        font-size: 0.95rem !important;
    }

    .small, small {
        font-size: 0.85rem !important;
    }

    .btn {
        font-size: 1rem !important;
    }
}

.btn-primary {
    background: var(--primary-blue);
    border: none;
    box-shadow: 0 4px 15px rgba(0, 132, 254, 0.3);
}

.btn-primary:hover {
    background: var(--secondary-blue);
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(11, 47, 108, 0.4);
}

.btn-outline-primary {
    border: 2px solid var(--primary-blue);
    color: var(--primary-blue);
}

.btn-outline-primary:hover {
    background: var(--primary-blue);
    border-color: var(--primary-blue);
    color: white;
}

.logo-container {
    background: var(--primary-blue);
    border-radius: 50%;
    width: 120px;
    height: 120px;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 1rem;
    box-shadow: 0 8px 25px rgba(0, 132, 254, 0.3);
}

.logo-text {
    color: white;
    font-size: 2.5rem;
    font-weight: 700;
    text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
}

.slogan {
    color: var(--primary-blue);
    font-weight: 600;
    font-size: 1.1rem;
    text-shadow: 1px 1px 2px rgba(0,0,0,0.2);
    margin-bottom: 2rem;
}

.carousel-inner {
    border-radius: 1rem;
    overflow: hidden;
    box-shadow: 0 8px 25px rgba(0, 132, 254, 0.2);
}

.carousel-item img {
    height: 200px;
    object-fit: cover;
}

.carousel-placeholder {
    height: 200px;
    background: var(--primary-blue);
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 1.2rem;
    font-weight: 600;
}

.product-card {
    background: white;
    border-radius: 15px;
    box-shadow: 0 4px 15px rgba(0, 132, 254, 0.1);
    transition: all 0.3s ease;
    overflow: hidden;
    border: 1px solid rgba(0, 132, 254, 0.1);
    height: 100%;
    display: flex;
    flex-direction: column;
}

.product-card .p-3 {
    flex-grow: 1;
    display: flex;
    flex-direction: column;
}

.product-card .p-3 .mt-2 {
    margin-top: auto !important;
}

.product-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 25px rgba(0, 132, 254, 0.2);
}

.product-image {
    height: 150px;
    object-fit: cover;
    width: 100%;
}

.product-placeholder {
    height: 150px;
    background: linear-gradient(135deg, var(--light-bg) 0%, var(--primary-blue) 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 0.9rem;
}

.price-tag {
    color: var(--primary-blue);
    font-weight: 700;
    font-size: 1.1rem;
}

.btn-toggle {
    position: absolute;
    top: 1rem;
    right: 1rem;
    background: rgba(255, 255, 255, 0.9);
    border: 1px solid var(--primary-blue);
    color: var(--primary-blue);
}

.btn-toggle:hover {
    background: var(--primary-blue);
    color: white;
}

.feature-icons i {
    font-size: 2rem;
    color: var(--primary-blue);
    transition: all 0.3s ease;
}

.feature-icons div:hover i {
    color: var(--accent-color);
    transform: scale(1.1);
}

.feature-icons span {
    display: block;
    margin-top: 0.5rem;
    font-size: 0.9rem;
    font-weight: 500;
}

.section-title {
    color: var(--primary-blue);
    font-weight: 700;
    margin-bottom: 1.5rem;
    position: relative;
}

.section-title::after {
    content: '';
    position: absolute;
    bottom: -5px;
    left: 50%;
    transform: translateX(-50%);
    width: 50px;
    height: 3px;
    background: var(--primary-blue);
    border-radius: 2px;
}

.swiper-container {
    overflow: hidden;
    padding: 10px 0;
}

.swiper-wrapper {
    display: flex;
    transition: transform 0.3s ease;
}

.swiper-slide {
    flex: 0 0 auto;
    margin-right: 15px;
    min-width: 280px; /* Ensure minimum width for uniform sizing */
}

@media (max-width: 768px) {
    .swiper-slide {
        width: 280px;
    }
}

@media (min-width: 769px) {
    .swiper-slide {
        width: calc(25% - 15px);
    }
}

.modal-content {
    border: none;
    border-radius: 15px;
    box-shadow: 0 10px 30px rgba(0, 132, 254, 0.2);
}

.modal-header {
    background: var(--primary-blue);
    color: white;
    border-radius: 15px 15px 0 0;
}

#mobileFooter {
    transition: transform 0.3s ease-in-out;
}

.btn {
    transition: all 0.3s ease;
    border-radius: 10px;
    font-weight: 500;
}

.btn:hover {
    transform: translateY(-1px);
}

.get-started-btn {
    background: var(--primary-blue);
    color: white;
    border: none;
    font-weight: 700;
    padding: 12px 30px;
    font-size: 1.1rem;
    box-shadow: 0 4px 15px rgba(0, 132, 254, 0.3);
}

.get-started-btn:hover {
    background: var(--secondary-blue);
    color: white;
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(11, 47, 108, 0.4);
}

/* Dark mode adjustments */
[data-bs-theme="dark"] .product-card {
    background: rgba(255, 255, 255, 0.1);
    border-color: rgba(255, 255, 255, 0.2);
}

[data-bs-theme="dark"] .btn-toggle {
    background: rgba(0, 0, 0, 0.3);
    border-color: var(--accent-color);
    color: var(--accent-color);
}
</style>


</head>
<body class="d-flex flex-column" style="min-height: 100vh;">

<div id="installSuccessToast" class="toast align-items-center text-bg-success border-0 position-fixed bottom-0 end-0 m-3 shadow" role="alert" aria-live="assertive" aria-atomic="true" style="z-index: 9999;">
  <div class="d-flex">
    <div class="toast-body">
      🎉 App successfully installed!
    </div>
    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
  </div>
</div>

{{-- Dark Mode Toggle --}}
<button class="btn btn-sm btn-toggle" onclick="toggleTheme()">
    <i class="bi bi-moon-stars-fill me-1"></i> Dark Mode
</button>

<div class="container text-center px-4 flex-grow-1 d-flex flex-column justify-content-center">
    {{-- Logo --}}
    <div class="logo-container">
        <img src="{{ asset('storage/icons/ebili-logo.png') }}" alt="Amigos Tayo Logo" style="width: 100px; height: 100px; object-fit: contain;">
    </div>

    <h2 class="mb-2 fw-bold" style="color: var(--primary-blue);">Amigos Community Online</h2>
    <p class="slogan text-success">Kapit-Bisig, Kahit Online.</p>

    {{-- Get Started Button --}}
    <div class="mb-4">
        <a href="{{ route('login') }}" class="btn get-started-btn btn-lg px-5 py-3">
            <i class="bi bi-rocket-takeoff me-2"></i>Get Started
        </a>
    </div>

    {{-- Banner Carousel --}}
    <div class="mb-4">
        <h4 class="section-title">Featured Promotions</h4>
        <div id="promoCarousel" class="carousel slide" data-bs-ride="carousel" data-bs-interval="3000">
            <div class="carousel-inner">
                <div class="carousel-item active">
                    <div class="carousel-placeholder">
                        <div>
                            <i class="bi bi-gift-fill fs-1 mb-2"></i>
                            <div>Special Offers Await!</div>
                            </div>
                    </div>
                </div>
                <div class="carousel-item">
                    <div class="carousel-placeholder">
                        <div>
                            <i class="bi bi-percent fs-1 mb-2"></i>
                            <div>Exclusive Discounts</div>
                        </div>
                    </div>
                </div>
                <div class="carousel-item">
                    <div class="carousel-placeholder">
                        <div>
                            <i class="bi bi-people-fill fs-1 mb-2"></i>
                            <div>Sali ka na sa Community</div>
                        </div>
                    </div>
                </div>
            </div>
            <button class="carousel-control-prev" type="button" data-bs-target="#promoCarousel" data-bs-slide="prev">
                <span class="carousel-control-prev-icon"></span>
            </button>
            <button class="carousel-control-next" type="button" data-bs-target="#promoCarousel" data-bs-slide="next">
                <span class="carousel-control-next-icon"></span>
            </button>
            <div class="carousel-indicators">
                <button type="button" data-bs-target="#promoCarousel" data-bs-slide-to="0" class="active"></button>
                <button type="button" data-bs-target="#promoCarousel" data-bs-slide-to="1"></button>
                <button type="button" data-bs-target="#promoCarousel" data-bs-slide-to="2"></button>
            </div>
        </div>
    </div>

    {{-- Featured Products --}}
    <div class="mb-4">
        <h4 class="section-title">Featured Products</h4>
        <div class="swiper-container">
            <div class="swiper-wrapper" id="productsWrapper">
                {{-- Real Products --}}
                @forelse($featuredProducts as $product)
                <div class="swiper-slide">
                    <div class="product-card" onclick="showMembershipModal()">
                        @if($product->thumbnail)
                            <img src="{{ asset('storage/' . $product->thumbnail) }}" alt="{{ $product->name }}" class="product-image">
                        @else
                            <div class="product-placeholder">
                                <div>
                                    <i class="bi bi-box-seam fs-1 mb-2"></i>
                                    <div>{{ $product->name }}</div>
                                </div>
                            </div>
                        @endif
                        <div class="p-3">
                            <h6 class="mb-2 fw-bold">{{ $product->name }}</h6>
                            <p class="text-muted small mb-2">{{ Str::limit($product->description, 50) }}</p>
                            @if($product->hasDiscount())
                                <div class="d-flex align-items-center gap-2 mb-2">
                                    <span class="price-tag">₱{{ number_format($product->getDiscountedPrice(), 2) }}</span>
                                    <small class="text-muted text-decoration-line-through">₱{{ number_format($product->price, 2) }}</small>
                                    <span class="badge bg-danger">-{{ $product->getDiscountPercentage() }}%</span>
                                </div>
                            @else
                                <div class="price-tag">₱{{ number_format($product->price, 2) }}</div>
                            @endif
                            <button class="btn btn-primary btn-sm w-100 mt-2 btn-uniform" onclick="event.stopPropagation(); showMembershipModal();">
                                <i class="bi bi-cart-plus btn-icon"></i>
                                <span class="btn-text">Add to Cart</span>
                            </button>
                        </div>
                    </div>
                </div>
                @empty
                {{-- Fallback if no products --}}
                @for($i = 1; $i <= 8; $i++)
                <div class="swiper-slide">
                    <div class="product-card" onclick="showMembershipModal()">
                        <div class="product-placeholder">
                            <div>
                                <i class="bi bi-box-seam fs-1 mb-2"></i>
                                <div>Product {{ $i }}</div>
                            </div>
                        </div>
                        <div class="p-3">
                            <h6 class="mb-2 fw-bold">Sample Product {{ $i }}</h6>
                            <p class="text-muted small mb-2">High quality product with amazing features</p>
                            <div class="price-tag">₱{{ number_format(rand(100, 999), 2) }}</div>
                            <button class="btn btn-primary btn-sm w-100 mt-2 btn-uniform" onclick="event.stopPropagation(); showMembershipModal();">
                                <i class="bi bi-cart-plus btn-icon"></i>
                                <span class="btn-text">Add to Cart</span>
                            </button>
                        </div>
                    </div>
                </div>
                @endfor
                @endforelse
            </div>
        </div>
    </div>

    {{-- Feature Icons --}}
    <div class="d-flex justify-content-around text-center feature-icons mb-4">
        <div role="button" data-bs-toggle="modal" data-bs-target="#whyModal">
            <i class="bi bi-info-circle"></i>
            <span>Why Amigos  Community</span>
        </div>
        <div role="button" data-bs-toggle="modal" data-bs-target="#programsModal">
            <i class="bi bi-stars"></i>
            <span>Programs</span>
        </div>
        <div role="button" data-bs-toggle="modal" data-bs-target="#rewardsModal">
            <i class="bi bi-gift"></i>
            <span>Rewards</span>
        </div>
        <div role="button" data-bs-toggle="modal" data-bs-target="#offersModal">
            <i class="bi bi-tags"></i>
            <span>Offers</span>
        </div>
    </div>

    <div class="d-flex flex-column flex-md-row justify-content-center align-items-center gap-3 my-4">
        <a href="{{ route('login') }}" class="btn btn-lg btn-primary px-5 py-2">
            🔒 Login
        </a>
        <a href="{{ route('guest.register') }}" class="btn btn-lg btn-outline-primary px-5 py-2">
            <i class="bi bi-people-fill me-2"></i> Join Us
        </a>
    </div>

    <hr style="border-color: var(--primary-blue); opacity: 0.3;">
    <p class="small" style="color: var(--primary-blue);"> More than just a member. Be an Amigos  Community. &copy; 2025</p>
</div>

{{-- Reusable Modals --}}
@foreach (['why' => 'Why Amigos  Community', 'rewards' => 'Rewards System', 'offers' => 'Exclusive Offers'] as $id => $title)
<div class="modal fade" id="{{ $id }}Modal" tabindex="-1" aria-labelledby="{{ $id }}ModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content text-center">
            <div class="modal-header border-0 justify-content-center">
                @if ($id === 'why')
                    <i class="bi bi-people-fill fs-1 text-white"></i>
                @elseif ($id === 'rewards')
                    <i class="bi bi-gift-fill fs-1 text-white"></i>
                @elseif ($id === 'offers')
                    <i class="bi bi-tags-fill fs-1 text-white"></i>
                @endif
            </div>
            <div class="modal-body px-4 pb-4">
                <h5 class="mb-3 fw-bold">{{ $title }}</h5>
                @if ($id === 'why')
                    <p class="text-muted">
                        <strong>Amigos  Communitys</strong> is derived from the Spanish word for "friends."<br><br>
                        At Amigos  Community online community, we believe in friendship with purpose — built on trust, cooperation, and shared goals.<br>
                        <strong>One community. One purpose. Helping each other thrive.</strong><br><br>
                        It's more than an app — it's a digital bayanihan, empowering members to support and uplift one another.
                    </p>
                @elseif ($id === 'rewards')
                    <p class="text-muted">
                        <strong>Join, engage, and be rewarded!</strong><br><br>
                        Every month, active members have the chance to win exciting rewards, enjoy exclusive perks, and discover new surprises.<br>
                        Your loyalty and participation matter — and we celebrate that.
                    </p>
                @elseif ($id === 'offers')
                    <p class="text-muted">
                        <strong>Everyone has something to give.</strong><br><br>
                        Whether it's your skills, stories, time, or talents — Amigos  Communitys is a platform where you can share what you have to offer, connect with others, and even earn while doing so.<br>
                        We believe in <strong>value exchange within the community</strong>.
                    </p>
                @endif

              {{-- CTA Buttons --}}
<div class="d-flex gap-2 mt-3">
    <a href="{{ route('login') }}" class="btn btn-primary flex-fill">Get Started</a>
    <button type="button" class="btn btn-outline-secondary flex-fill" data-bs-dismiss="modal">Close</button>
</div>

            </div>
        </div>
    </div>
</div>
@endforeach

<button id="backToTop" class="btn btn-primary rounded-circle shadow position-fixed" style="bottom: 100px; right: 16px; display: none; z-index: 9999;">
    <i class="bi bi-arrow-up"></i>
</button>

<!-- Programs Tabs Modal with Swipe and Auto-Scroll Support -->
<div class="modal fade" id="programsModal" tabindex="-1" aria-labelledby="programsModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content">
      <div class="modal-header border-0 justify-content-center">
        <h5 class="modal-title fw-bold text-white" id="programsModalLabel">
          Amigos Community: Bayanihan-Inspired Programs
        </h5>
      </div>
      <div class="modal-body">
        <div class="overflow-auto" style="white-space: nowrap;" id="programTabsWrapper">
          <ul class="nav nav-tabs nav-fill flex-nowrap sticky-top bg-white pt-2 mb-3" id="programTabs" role="tablist">
            <li class="nav-item" role="presentation">
              <button class="nav-link active" id="vision-tab" data-bs-toggle="tab" data-bs-target="#vision" type="button" role="tab">🌟 Vision</button>
            </li>
            <li class="nav-item" role="presentation">
              <button class="nav-link" id="membership-tab" data-bs-toggle="tab" data-bs-target="#membership" type="button" role="tab">🔑 Membership</button>
            </li>
            <li class="nav-item" role="presentation">
              <button class="nav-link" id="rewards-tab" data-bs-toggle="tab" data-bs-target="#rewards" type="button" role="tab">💰 Rewards</button>
            </li>
            <li class="nav-item" role="presentation">
              <button class="nav-link" id="borrow-tab" data-bs-toggle="tab" data-bs-target="#borrow" type="button" role="tab">🤝 Borrow</button>
            </li>
            <li class="nav-item" role="presentation">
              <button class="nav-link" id="showcase-tab" data-bs-toggle="tab" data-bs-target="#showcase" type="button" role="tab">📣 Showcase</button>
            </li>
            <li class="nav-item" role="presentation">
              <button class="nav-link" id="referral-tab" data-bs-toggle="tab" data-bs-target="#referral" type="button" role="tab">📈 Referral</button>
            </li>
            <li class="nav-item" role="presentation">
              <button class="nav-link" id="causes-tab" data-bs-toggle="tab" data-bs-target="#causes" type="button" role="tab">🎯 Causes</button>
            </li>
            <li class="nav-item" role="presentation">
              <button class="nav-link" id="core-tab" data-bs-toggle="tab" data-bs-target="#core" type="button" role="tab">💬 Message</button>
            </li>
          </ul>
        </div>

        <div class="tab-content swipe-tabs" id="programTabsContent">
          <div class="tab-pane fade show active p-3" id="vision" role="tabpanel">
            <h6 class="fw-bold">🌟 Our Vision</h6>
            <p>To build a bayanihan-style, friends-of-friends cooperative group united by shared interests, values, and advocacies. This is Amigos Community — not just a group, but with purpose-driven individuals becoming one under the name "Amigos Community".</p>
          </div>
          <div class="tab-pane fade p-3" id="membership" role="tabpanel">
            <h6 class="fw-bold">🔑 Amigos Community Membership</h6>
            <p>Enjoy member discounts, sell or offer services, and support each other in a growing community marketplace.</p>
          </div>
          <div class="tab-pane fade p-3" id="rewards" role="tabpanel">
            <h6 class="fw-bold">💰 Cashback & Rewards</h6>
            <p>Get cashback when you buy, sell, or offer services. Join monthly raffles with exciting prizes like groceries, sack of rice, appliances, etc.</p>
          </div>
          <div class="tab-pane fade p-3" id="borrow" role="tabpanel">
            <h6 class="fw-bold">🤝 Bayanihan "Borrow" Program</h6>
            <p>Quick, low-interest emergency lending available for verified members who need support.</p>
          </div>
          <div class="tab-pane fade p-3" id="showcase" role="tabpanel">
            <h6 class="fw-bold">📣 Skills & Business Showcase</h6>
            <p>Promote your skills, products, or services within the app and earn from your fellow Amigos  Communitys.</p>
          </div>
          <div class="tab-pane fade p-3" id="referral" role="tabpanel">
            <h6 class="fw-bold">📈 Referral Earnings</h6>
         <ul>
              <li>Level A -  From your invite you get:₱25</li>
              <li>Level B - Invite's invite you get: ₱15</li>
              <li>Level C - A friend's Invite's(3rd level) you get: ₱10</li>
            </ul>
            <p>Earn as you grow the Amigos Community group.</p>
          </div>
          <div class="tab-pane fade p-3" id="causes" role="tabpanel">
            <h6 class="fw-bold">🎯 Support for Local Causes</h6>
            <p>Part of the earnings goes to drivers, women's groups, and other community-led initiatives.</p>
          </div>
          <div class="tab-pane fade p-3" id="core" role="tabpanel">
            <h6 class="fw-bold">💬 Core Message</h6>
            <p><strong>SHOP TO SAVE. SHARE TO EARN.</strong><br>
            Be more than a member — be an Amigos Community.</p>
          </div>
        </div>
      </div>
      <div class="modal-footer border-0">
        <a href="{{ route('guest.register') }}" class="btn btn-success">Join Now</a>
        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>


{{-- Membership Required Modal --}}
<div class="modal fade" id="membershipModal" tabindex="-1" aria-labelledby="membershipModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content text-center">
            <div class="modal-header border-0 justify-content-center">
                <i class="bi bi-person-check-fill fs-1 text-white"></i>
            </div>
            <div class="modal-body px-4 pb-4">
                <h5 class="mb-3 fw-bold">Membership Required</h5>
                <p class="text-muted">
                    <strong>You need to be a Member to add items to cart!</strong><br><br>
                    Join Amigos Community today and enjoy exclusive benefits:<br>
                    • Member discounts on all products<br>
                    • Cashback rewards on purchases<br>
                    • Access to exclusive deals<br>
                    • Community marketplace features
                </p>

                {{-- CTA Buttons --}}
                <div class="d-flex gap-2 mt-3">
                    <a href="{{ route('guest.register') }}" class="btn btn-primary flex-fill">Join Now</a>
                    <a href="{{ route('login') }}" class="btn btn-outline-primary flex-fill">Login</a>
                </div>
                <button type="button" class="btn btn-outline-secondary w-100 mt-2" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

{{-- JS Scripts --}}
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
// Service Worker
if ('serviceWorker' in navigator) {
    navigator.serviceWorker.register('/sw.js')
        .then(reg => console.log('Service Worker registered ✅', reg))
        .catch(err => console.error('Service Worker registration failed ❌', err));
}

// Theme Toggle
function toggleTheme() {
    const html = document.documentElement;
    const isDark = html.getAttribute('data-bs-theme') === 'dark';
    html.setAttribute('data-bs-theme', isDark ? 'light' : 'dark');

    // Toggle icon and label
    const toggleBtn = document.querySelector('.btn-toggle i');
    const toggleText = document.querySelector('.btn-toggle');
    if (isDark) {
        toggleBtn.className = 'bi bi-moon-stars-fill me-1';
        toggleText.innerHTML = '<i class="bi bi-moon-stars-fill me-1"></i> Dark Mode';
    } else {
        toggleBtn.className = 'bi bi-brightness-high-fill me-1';
        toggleText.innerHTML = '<i class="bi bi-brightness-high-fill me-1"></i> Light Mode';
    }
}

// Redirect to login function
function redirectToLogin() {
    window.location.href = "{{ route('login') }}";
}

// Show membership modal function
function showMembershipModal() {
    const modal = new bootstrap.Modal(document.getElementById('membershipModal'));
    modal.show();
}

// Swiper functionality for products
let currentSlide = 0;
const slides = document.querySelectorAll('.swiper-slide');
const totalSlides = slides.length;

function updateSwiper() {
    const wrapper = document.getElementById('productsWrapper');
    const slideWidth = slides[0].offsetWidth + 15; // width + margin
    const visibleSlides = Math.floor(wrapper.parentElement.offsetWidth / slideWidth);
    const maxSlide = Math.max(0, totalSlides - visibleSlides);

    currentSlide = Math.min(currentSlide, maxSlide);
    wrapper.style.transform = `translateX(-${currentSlide * slideWidth}px)`;
}

// Touch events for swiper
let startX = 0;
let isDragging = false;

document.getElementById('productsWrapper').addEventListener('touchstart', (e) => {
    startX = e.touches[0].clientX;
    isDragging = true;
});

document.getElementById('productsWrapper').addEventListener('touchmove', (e) => {
    if (!isDragging) return;
    e.preventDefault();
});

document.getElementById('productsWrapper').addEventListener('touchend', (e) => {
    if (!isDragging) return;
    isDragging = false;

    const endX = e.changedTouches[0].clientX;
    const diff = startX - endX;

    if (Math.abs(diff) > 50) {
        if (diff > 0 && currentSlide < totalSlides - 1) {
            currentSlide++;
        } else if (diff < 0 && currentSlide > 0) {
            currentSlide--;
        }
        updateSwiper();
    }
});

// Auto-scroll products
setInterval(() => {
    const wrapper = document.getElementById('productsWrapper');
    const slideWidth = slides[0].offsetWidth + 15;
    const visibleSlides = Math.floor(wrapper.parentElement.offsetWidth / slideWidth);
    const maxSlide = Math.max(0, totalSlides - visibleSlides);

    currentSlide = (currentSlide + 1) % (maxSlide + 1);
    updateSwiper();
}, 4000);

// Resize handler
window.addEventListener('resize', updateSwiper);

// Initialize on load
window.addEventListener('load', updateSwiper);

// Back to top functionality
let lastScrollTop = 0;
const backToTop = document.getElementById('backToTop');

window.addEventListener('scroll', function () {
    let st = window.pageYOffset || document.documentElement.scrollTop;

    // Show back to top if scrolled down enough
    if (st > 200) {
        backToTop.style.display = 'block';
    } else {
        backToTop.style.display = 'none';
    }

    lastScrollTop = st <= 0 ? 0 : st;
});

// Scroll to top button
backToTop.addEventListener('click', () => {
    window.scrollTo({ top: 0, behavior: 'smooth' });
});

// Enable swipe gestures on tab content
document.querySelectorAll('.swipe-tabs').forEach(function(tabContent) {
    let startX;
    tabContent.addEventListener('touchstart', function(e) {
        startX = e.changedTouches[0].screenX;
    }, { passive: true });

    tabContent.addEventListener('touchend', function(e) {
        const endX = e.changedTouches[0].screenX;
        const diff = startX - endX;
        const tabs = Array.from(document.querySelectorAll('#programTabs .nav-link'));
        const activeIndex = tabs.findIndex(t => t.classList.contains('active'));

        if (Math.abs(diff) > 50) {
            let nextIndex = diff > 0 ? activeIndex + 1 : activeIndex - 1;
            if (nextIndex >= 0 && nextIndex < tabs.length) {
                tabs[nextIndex].click();
            }
        }
    }, { passive: true });
});

// Auto-scroll tab bar when switching tabs
document.querySelectorAll('#programTabs .nav-link').forEach(function(tab) {
    tab.addEventListener('shown.bs.tab', function(e) {
        const tabElement = e.target;
        const container = document.getElementById('programTabsWrapper');
        const tabOffsetLeft = tabElement.offsetLeft;
        const tabWidth = tabElement.offsetWidth;
        const containerWidth = container.offsetWidth;
        const scrollLeft = tabOffsetLeft - (containerWidth / 2) + (tabWidth / 2);
        container.scrollTo({ left: scrollLeft, behavior: 'smooth' });
    });
});
</script>


{{-- PWA Install Button --}}
@include('partials.pwa-install-button')

</body>
</html>
