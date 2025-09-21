{{-- Guest Mobile Footer Navigation --}}
<div class="guest-mobile-footer">
    {{-- Home --}}
    <a href="{{ url('/') }}" class="guest-footer-item active">
        <i class="bi bi-house-fill"></i>
        <span>Home</span>
    </a>
    
    {{-- Shop --}}
    <a href="{{ route('login') }}" class="guest-footer-item">
        <i class="bi bi-bag-fill"></i>
        <span>Shop</span>
    </a>
    
    {{-- Login --}}
    <a href="{{ route('login') }}" class="guest-footer-item">
        <i class="bi bi-box-arrow-in-right"></i>
        <span>Login</span>
    </a>
    
    {{-- Register --}}
    <a href="{{ route('guest.register') }}" class="guest-footer-item">
        <i class="bi bi-person-plus-fill"></i>
        <span>Join</span>
    </a>
</div>

{{-- Guest Mobile Footer Styles --}}
<style>
    .guest-mobile-footer {
        position: fixed;
        bottom: 0;
        left: 0;
        right: 0;
        background: linear-gradient(135deg, #6f42c1 0%, #8e44ad 100%);
        backdrop-filter: blur(10px);
        border-top: 1px solid rgba(255, 215, 0, 0.2);
        display: flex;
        justify-content: space-around;
        align-items: center;
        padding: 8px 0;
        z-index: 1000;
        box-shadow: 0 -2px 20px rgba(111, 66, 193, 0.3);
    }
    
    .guest-footer-item {
        display: flex;
        flex-direction: column;
        align-items: center;
        text-decoration: none;
        color: rgba(255, 255, 255, 0.7);
        transition: all 0.3s ease;
        padding: 8px 12px;
        border-radius: 12px;
        min-width: 60px;
    }
    
    .guest-footer-item:hover,
    .guest-footer-item.active {
        color: #ffd700;
        background: rgba(255, 215, 0, 0.1);
        transform: translateY(-2px);
    }
    
    .guest-footer-item i {
        font-size: 20px;
        margin-bottom: 4px;
    }
    
    .guest-footer-item span {
        font-size: 11px;
        font-weight: 500;
        letter-spacing: 0.5px;
    }
    
    /* Add padding to body to account for fixed footer */
    body {
        padding-bottom: 80px;
    }
    
    /* Hide on desktop */
    @media (min-width: 992px) {
        .guest-mobile-footer {
            display: none;
        }
        
        body {
            padding-bottom: 0;
        }
    }
</style>