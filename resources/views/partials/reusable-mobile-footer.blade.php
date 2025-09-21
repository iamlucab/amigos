    {{-- Reusable Mobile Footer Component --}}
@props(['activeRoute' => null])

{{-- Mobile Footer Navigation --}}
<div class="reusable-mobile-footer position-fixed bottom-0 start-0 w-100 d-flex justify-content-around align-items-center py-2"
     style="z-index: 1050; height: 70px;">
     
    @auth
        @if(auth()->user()->role === 'Admin' || auth()->user()->role === 'Staff')
            {{-- Admin/Staff Footer --}}
            <a href="{{ route('admin.dashboard') }}"
               class="mobile-footer-item text-decoration-none d-flex flex-column align-items-center justify-content-center {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}"
               style="flex: 1; text-align: center;">
                <i class="bi bi-speedometer2 fa-lg mb-1"></i>
                <span class="small fw-bold">Dashboard</span>
            </a>
            
            <a href="{{ url('/admin/members') }}"
               class="mobile-footer-item text-decoration-none d-flex flex-column align-items-center justify-content-center {{ request()->is('admin/members*') ? 'active' : '' }}"
               style="flex: 1; text-align: center;">
                <i class="bi bi-people fa-lg mb-1"></i>
                <span class="small fw-bold">Members</span>
            </a>
            
            <a href="{{ url('/admin/cashin-approvals') }}"
               class="mobile-footer-item text-decoration-none d-flex flex-column align-items-center justify-content-center {{ request()->is('admin/cashin*') ? 'active' : '' }}"
               style="flex: 1; text-align: center;">
                <i class="bi bi-wallet2 fa-lg mb-1"></i>
                <span class="small fw-bold">Cash In</span>
            </a>
            
            <a href="{{ route('profile.edit') }}"
               class="mobile-footer-item text-decoration-none d-flex flex-column align-items-center justify-content-center {{ request()->routeIs('profile.*') ? 'active' : '' }}"
               style="flex: 1; text-align: center;">
                <i class="bi bi-person fa-lg mb-1"></i>
                <span class="small fw-bold">Profile</span>
            </a>
        @else
            {{-- Member Footer --}}
            <a href="{{ route('member.dashboard') }}"
               class="mobile-footer-item text-decoration-none d-flex flex-column align-items-center justify-content-center {{ request()->routeIs('member.dashboard') ? 'active' : '' }}"
               style="flex: 1; text-align: center;">
                <i class="bi bi-house fa-lg mb-1"></i>
                <span class="small fw-bold">Home</span>
            </a>
            
            <a href="{{ route('shop.index') }}"
               class="mobile-footer-item text-decoration-none d-flex flex-column align-items-center justify-content-center {{ request()->routeIs('shop.*') ? 'active' : '' }}"
               style="flex: 1; text-align: center;">
                <i class="bi bi-bag fa-lg mb-1"></i>
                <span class="small fw-bold">Shop</span>
            </a>
            
            <a href="{{ route('wallet.history', ['type' => 'main']) }}"
               class="mobile-footer-item text-decoration-none d-flex flex-column align-items-center justify-content-center {{ request()->routeIs('wallet.*') ? 'active' : '' }}"
               style="flex: 1; text-align: center;">
                <i class="bi bi-wallet2 fa-lg mb-1"></i>
                <span class="small fw-bold">Wallet</span>
            </a>
            
            <a href="{{ route('genealogy.index') }}"
               class="mobile-footer-item text-decoration-none d-flex flex-column align-items-center justify-content-center {{ request()->routeIs('genealogy.*') ? 'active' : '' }}"
               style="flex: 1; text-align: center;">
                <i class="bi bi-diagram-3 fa-lg mb-1"></i>
                <span class="small fw-bold">Network</span>
            </a>
            
            <a href="{{ route('profile.edit') }}"
               class="mobile-footer-item text-decoration-none d-flex flex-column align-items-center justify-content-center {{ request()->routeIs('profile.*') ? 'active' : '' }}"
               style="flex: 1; text-align: center;">
                <i class="bi bi-person fa-lg mb-1"></i>
                <span class="small fw-bold">Profile</span>
            </a>
        @endif
    @else
        {{-- Guest Footer --}}
        <a href="{{ url('/') }}" class="mobile-footer-item text-decoration-none d-flex flex-column align-items-center justify-content-center {{ request()->is('/') ? 'active' : '' }}"
           style="flex: 1; text-align: center;">
            <i class="bi bi-house-fill mb-1" style="font-size: 1.25rem;"></i>
            <span class="small fw-bold">Home</span>
        </a>
        
        <a href="{{ route('login') }}" class="mobile-footer-item text-decoration-none d-flex flex-column align-items-center justify-content-center"
           style="flex: 1; text-align: center;">
            <i class="bi bi-bag-fill mb-1" style="font-size: 1.25rem;"></i>
            <span class="small fw-bold">Shop</span>
        </a>
        
        <a href="{{ route('login') }}" class="mobile-footer-item text-decoration-none d-flex flex-column align-items-center justify-content-center"
           style="flex: 1; text-align: center;">
            <i class="bi bi-box-arrow-in-right mb-1" style="font-size: 1.25rem;"></i>
            <span class="small fw-bold">Login</span>
        </a>
        
        <a href="{{ route('guest.register') }}" class="mobile-footer-item text-decoration-none d-flex flex-column align-items-center justify-content-center"
           style="flex: 1; text-align: center;">
            <i class="bi bi-person-plus-fill mb-1" style="font-size: 1.25rem;"></i>
            <span class="small fw-bold">Join</span>
        </a>
    @endauth
</div>

{{-- Enhanced Mobile Footer Styling --}}
<style>
    /* Mobile Footer Base Styling */
    .reusable-mobile-footer {
        background: #17a2b8 !important; /* bg-info color */
        border-radius: 20px 20px 0 0 !important;
        box-shadow: 0 -4px 15px rgba(0, 0, 0, 0.1) !important;
        border-top: 2px solid rgba(23, 162, 184, 0.1);
        color: white !important;
    }

    /* Mobile Footer Items */
    .reusable-mobile-footer .mobile-footer-item {
        color: white !important;
        transition: all 0.3s ease !important;
        font-family: 'Poppins', sans-serif !important;
        padding: 12px 4px !important;
        border-radius: 12px !important;
        position: relative !important;
        overflow: hidden !important;
        min-height: 44px !important; /* Minimum touch target size */
        display: flex !important;
        flex-direction: column !important;
        justify-content: center !important;
        align-items: center !important;
    }

    .reusable-mobile-footer .mobile-footer-item::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: #17a2b8; /* bg-info color */
        opacity: 0;
        transition: opacity 0.3s ease;
        border-radius: 12px;
        z-index: -1;
    }

    .reusable-mobile-footer .mobile-footer-item.active,
    .reusable-mobile-footer .mobile-footer-item:hover {
        color: white !important;
        transform: translateY(-3px) !important;
        text-shadow: none !important;
    }

    .reusable-mobile-footer .mobile-footer-item.active::before,
    .reusable-mobile-footer .mobile-footer-item:hover::before {
        opacity: 1;
    }

    .reusable-mobile-footer .mobile-footer-item.active i,
    .reusable-mobile-footer .mobile-footer-item:hover i {
        color: white !important;
        transform: scale(1.1);
    }

    .reusable-mobile-footer .mobile-footer-item.active span,
    .reusable-mobile-footer .mobile-footer-item:hover span {
        color: white !important;
        font-weight: 700 !important;
    }

    /* Icon styling */
    .reusable-mobile-footer .mobile-footer-item i {
        transition: all 0.3s ease !important;
        filter: drop-shadow(0 2px 4px rgba(0,0,0,0.1));
        font-size: 1.2rem !important; /* Larger icons for better touch targets */
    }

    /* Text styling */
    .reusable-mobile-footer .mobile-footer-item span {
        transition: all 0.3s ease !important;
        font-size: 0.75rem !important;
        letter-spacing: 0.5px !important;
    }

    /* Active indicator */
    .reusable-mobile-footer .mobile-footer-item.active::after {
        content: '';
        position: absolute;
        top: -2px;
        left: 50%;
        transform: translateX(-50%);
        width: 30px;
        height: 3px;
        background: #17a2b8; /* bg-info color */
        border-radius: 0 0 3px 3px;
        box-shadow: 0 2px 8px rgba(23, 162, 184, 0.5);
    }

    /* Body padding for fixed footer - Improved spacing */
    body {
        padding-bottom: 90px !important;
        font-family: 'Poppins', sans-serif !important;
    }
    
    /* Content wrapper padding to prevent overlap */
    .content-wrapper {
        padding-bottom: 90px !important;
    }
    
    /* Main content padding */
    .content {
        padding-bottom: 20px !important;
    }
    
    /* Container fluid padding for mobile */
    @media (max-width: 991px) {
        .container-fluid {
            padding-bottom: 20px !important;
        }
    }
    
    /* Hide on desktop */
    @media (min-width: 992px) {
        .reusable-mobile-footer {
            display: none !important;
        }
        
        body {
            padding-bottom: 0 !important;
        }
        
        .content-wrapper {
            padding-bottom: 0 !important;
        }
        
        .content {
            padding-bottom: 0 !important;
        }
        
        .container-fluid {
            padding-bottom: 0 !important;
        }
    }

    /* Responsive adjustments */
    @media (max-width: 576px) {
        .reusable-mobile-footer .mobile-footer-item span {
            font-size: 0.7rem !important;
        }
        
        .reusable-mobile-footer .mobile-footer-item i {
            font-size: 1rem !important;
        }
    }

    /* Smooth entrance animation */
    .reusable-mobile-footer {
        animation: slideUpFooter 0.5s ease-out;
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

    /* Pulse effect for active items */
    .reusable-mobile-footer .mobile-footer-item.active {
        animation: pulseActive 2s infinite;
    }

    @keyframes pulseActive {
        0%, 100% {
            box-shadow: 0 0 0 0 rgba(23, 162, 184, 0.4);
        }
        50% {
            box-shadow: 0 0 0 8px rgba(23, 162, 184, 0);
        }
    }
</style>