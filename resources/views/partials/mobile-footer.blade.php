{{--  Theme Styling --}}
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="{{ asset('css/ebili-theme.css') }}">

{{-- Mobile Footer Navigation --}}
<div id="mobile-footer" class="mobile-footer d-flex justify-content-around align-items-center py-2">
    {{-- Home --}}
    <a href="{{ route('member.dashboard') }}"
       class="mobile-footer-item text-decoration-none d-flex flex-column align-items-center justify-content-center {{ request()->routeIs('member.dashboard') ? 'active' : '' }}">
        <i class="bi bi-house fa-lg mb-1"></i>
         <span class="small fw-bold text-dark">Home</span>
    </a>

    {{-- Shop --}}
    <a href="{{ route('shop.index') }}"
       class="mobile-footer-item text-decoration-none d-flex flex-column align-items-center justify-content-center {{ request()->routeIs('shop.*') ? 'active' : '' }}">
        <i class="bi bi-bag fa-lg mb-1"></i>
          <span class="small fw-bold text-dark">Shop</span>
    </a>

    {{-- Wallet --}}
    <a href="{{ route('wallet.history', ['type' => 'main']) }}"
       class="mobile-footer-item text-decoration-none d-flex flex-column align-items-center justify-content-center {{ request()->routeIs('wallet.*') ? 'active' : '' }}">
        <i class="bi bi-wallet2 fa-lg mb-1"></i>
         <span class="small fw-bold text-dark">Wallet</span>
    </a>

    {{-- Network --}}
    <a href="{{ route('genealogy.index') }}"
       class="mobile-footer-item text-decoration-none d-flex flex-column align-items-center justify-content-center {{ request()->routeIs('genealogy.*') ? 'active' : '' }}">
        <i class="bi bi-diagram-3 fa-lg mb-1"></i>
         <span class="small fw-bold text-dark">Network</span>
    </a>

    {{-- Profile --}}
    <a href="{{ route('profile.edit') }}"
       class="mobile-footer-item text-decoration-none d-flex flex-column align-items-center justify-content-center {{ request()->routeIs('profile.*') ? 'active' : '' }}">
        <i class="bi bi-person fa-lg mb-1"></i>
         <span class="small fw-bold text-dark">Profile</span>
    </a>
</div>

{{-- Enhanced Mobile Footer Styling --}}
<style>
   .mobile-footer {
    position: fixed; /* changed from sticky */
    bottom: 0;
    left: 0;
    right: 0;
    height: 70px;
    background: linear-gradient(135deg, var(--primary-purple) 0%, var(--secondary-purple) 100%) !important;
    border-radius: 20px 20px 0 0 !important;
    box-shadow: 0 -4px 15px rgba(11, 85, 205, 0.3) !important;
    backdrop-filter: blur(10px);
    border-top: 2px solid rgba(255, 255, 255, 0.1);
    z-index: 1050;
    transition: transform 0.3s ease-in-out;
    animation: slideUpFooter 0.5s ease-out;
    /* Add touch-action to prevent scrolling interference */
    touch-action: manipulation;
    -webkit-touch-callout: none;
    -webkit-user-select: none;
    -moz-user-select: none;
    -ms-user-select: none;
    user-select: none;
}

@keyframes slideUpFooter {
    from {
        transform: translateY(100%);
        opacity: 0;
    }
    to {
        transform: translateY(0);
        opacity: 1;
    }
}

@media (min-width: 992px) {
    .mobile-footer {
        display: none !important;
    }
}


    .mobile-footer.hide-footer {
        transform: translateY(100%);
    }

    .mobile-footer-item {
        color: rgba(255, 255, 255, 0.7) !important;
        transition: all 0.3s ease !important;
        font-family: 'Poppins', sans-serif !important;
        padding: 8px 4px !important;
        border-radius: 12px !important;
        position: relative !important;
        overflow: hidden !important;
        flex: 1;
        text-align: center;
        /* Add pointer events to ensure proper touch handling */
        pointer-events: auto;
        touch-action: manipulation;
    }

    .mobile-footer-item::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: linear-gradient(135deg, var(--accent-gold) 0%, #092b6a 100%);
        opacity: 0;
        transition: opacity 0.3s ease;
        border-radius: 12px;
        z-index: -1;
    }

    .mobile-footer-item.active,
    .mobile-footer-item:hover {
        color: var(--dark-purple) !important;
        transform: translateY(-3px) !important;
        text-shadow: none !important;
    }

    .mobile-footer-item.active::before,
    .mobile-footer-item:hover::before {
        opacity: 1;
    }

    .mobile-footer-item.active i,
    .mobile-footer-item:hover i {
        color: var(--dark-purple) !important;
        transform: scale(1.1);
    }

    .mobile-footer-item.active span,
    .mobile-footer-item:hover span {
        color: var(--dark-purple) !important;
        font-weight: 700 !important;
    }

    .mobile-footer-item i {
        transition: all 0.3s ease !important;
        filter: drop-shadow(0 2px 4px rgba(255, 177, 177, 0.1));
    }

    .mobile-footer-item span {
        transition: all 0.3s ease !important;
        font-size: 0.75rem !important;
        letter-spacing: 0.5px !important;
    }

    .mobile-footer-item.active::after {
        content: '';
        position: absolute;
        top: -2px;
        left: 50%;
        transform: translateX(-50%);
        width: 30px;
        height: 3px;
        background: var(--accent-gold);
        border-radius: 0 0 3px 3px;
        box-shadow: 0 2px 8px rgba(255, 215, 0, 0.5);
    }

    body {
        padding-bottom: 90px !important;
        font-family: 'Poppins', sans-serif !important;
    }

    .content-wrapper {
        padding-bottom: 90px !important;
    }

    .content {
        padding-bottom: 20px !important;
    }

    @media (min-width: 992px) {
        .mobile-footer {
            display: none !important;
        }

        body,
        .content-wrapper,
        .content {
            padding-bottom: 0 !important;
        }
    }

    @media (max-width: 576px) {
        .mobile-footer-item span {
            font-size: 0.7rem !important;
        }

        .mobile-footer-item i {
            font-size: 1rem !important;
        }
    }

    @keyframes slideUpFooter {
        from {
            transform: translateY(100%);
            opacity: 0;
        }
        to {
            transform: translateY(0);
            opacity: 1;
        }
    }

    .mobile-footer-item.active {
        animation: pulseActive 2s infinite;
    }

    @keyframes pulseActive {
        0%, 100% {
            box-shadow: 0 0 0 0 rgba(255, 215, 0, 0.4);
        }
        50% {
            box-shadow: 0 0 0 8px rgba(255, 215, 0, 0);
        }
    }
</style>

{{-- Scroll-based show/hide script --}}
<script>
    let lastScrollTop = 0;
    const mobileFooter = document.getElementById('mobile-footer');

    window.addEventListener('scroll', function () {
        const currentScroll = window.pageYOffset || document.documentElement.scrollTop;

        if (currentScroll > lastScrollTop) {
            // scrolling down
            mobileFooter.classList.add('hide-footer');
        } else {
            // scrolling up
            mobileFooter.classList.remove('hide-footer');
        }

        lastScrollTop = currentScroll <= 0 ? 0 : currentScroll;
    });
</script>
