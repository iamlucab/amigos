<!DOCTYPE html>
<html lang="en" data-bs-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="theme-color" content="#64189e">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black">
    <meta name="apple-mobile-web-app-title" content="Amigos 98 Community">
    <meta name="description" content="Shop to Save, Share to Earn! Amigos 98 Community Online Community Marketplace">
    <meta name="mobile-web-app-capable" content="yes">
    <title>{{ config('app.name', 'Amigos 98 Community Online') }}</title>

    {{-- CSRF Token --}}
    <meta name="csrf-token" content="{{ csrf_token() }}">

    {{-- PWA & Icons --}}
    <link rel="icon" type="image/png" href="{{ asset('storage/icons/favicon-96x96.png') }}" sizes="96x96" />
    <link rel="icon" type="image/svg+xml" href="{{ asset('storage/icons/favicon.svg') }}" />
    <link rel="shortcut icon" href="{{ asset('storage/icons/favicon.ico') }}" />
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('storage/icons/apple-touch-icon.png') }}" />
    <link rel="manifest" href="{{ asset('site.webmanifest') }}" />

    {{-- Bootstrap + Icons --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet" integrity="sha512-iecdLmaskl7CVkqkXNQ/ZH/XLlvWZOJyj7Yy7tcenmpD1ypASozpmT/E0iPtmFIB46ZmdtAc9eNBvH0H/ZpiBw==" crossorigin="anonymous" referrerpolicy="no-referrer">
    
    {{-- Google Fonts --}}
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    {{-- Amigos 98 Community Theme --}}
    <link rel="stylesheet" href="{{ asset('css/ebili-theme.css') }}">
    
    @stack('styles')
</head>

<body class="d-flex flex-column" style="min-height: 100vh;">
{{-- Hidden logout form for all pages --}}
    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
        @csrf
    </form>

    {{-- Dark Mode Toggle --}}
        <button class="btn btn-sm btn-toggle position-fixed" onclick="toggleTheme()" style="top: 1rem; right: 1rem; z-index: 1060; min-height: 44px; padding: 10px 15px;">
            <i class="bi bi-moon-stars-fill me-1"></i> Dark Mode
        </button>

    {{-- 🔘 Floating Cart Button (opens modal) --}}
        <button class="btn position-fixed rounded-circle shadow fade-in"
            data-bs-toggle="modal" data-bs-target="#cartModal"
            style="bottom: 100px; right: 20px; z-index: 1050; background: linear-gradient(135deg, var(--accent-gold) 0%, #ffed4e 100%); color: var(--dark-purple); width: 60px; height: 60px; min-height: 44px;">
            <i class="bi bi-cart fa-lg"></i>
            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" id="cart-count" style="font-size: 0.7rem;">
                {{ count(session('cart', [])) }}
            </span>
        </button>

    {{-- 🛒 Cart Modal --}}
    @include('partials.cart-modal')

    {{-- 📦 Page Content --}}
        <div class="container-fluid p-2 p-sm-3 flex-grow-1">
            @yield('content')
        </div>

    {{-- 📱 Reusable Mobile Footer --}}
    @include('partials.reusable-mobile-footer')

    {{-- ✅ Bootstrap Bundle (already includes Popper) --}}
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    {{-- 📦 Enhanced Toast Notification Container --}}
    <div class="toast-container position-fixed bottom-0 end-0 p-3" style="z-index: 1070;">
        {{-- Session-based toasts will be appended here --}}
        @if(session('toast'))
            <div class="toast show slide-up" role="alert" aria-live="assertive" aria-atomic="true">
                <div class="toast-header" style="background: linear-gradient(135deg, var(--primary-purple) 0%, var(--secondary-purple) 100%); color: white; border: none;">
                    <i class="bi bi-bell me-2"></i>
                    <strong class="me-auto">{{ session('toast.title', 'Notification') }}</strong>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="toast" aria-label="Close"></button>
                </div>
                <div class="toast-body" style="font-family: 'Poppins', sans-serif;">
                    {{ session('toast.message') }}
                </div>
            </div>
        @endif
    </div>

    {{-- 📦 Enhanced Real-time toast (for events like MemberApproved) --}}
    @auth
    <script>
        const userId = {{ auth()->id() }};
        if (typeof window.Echo !== 'undefined') {
            window.Echo.private(`user.${userId}`)
                .listen('MemberApproved', (e) => {
                    showToast(e.message, 'success', 'Member Approved');
                });
        }

        function showToast(message, type = 'success', title = 'Notification') {
            const toastContainer = document.querySelector('.toast-container');
            
            const toast = document.createElement('div');
            toast.className = 'toast show slide-up';
            toast.setAttribute('role', 'alert');
            toast.setAttribute('aria-live', 'assertive');
            toast.setAttribute('aria-atomic', 'true');
            
            const typeColors = {
                'success': 'linear-gradient(135deg, #28a745 0%, #20c997 100%)',
                'error': 'linear-gradient(135deg, #dc3545 0%, #e74c3c 100%)',
                'warning': 'linear-gradient(135deg, #ffc107 0%, #f39c12 100%)',
                'info': 'linear-gradient(135deg, #17a2b8 0%, #3498db 100%)',
                'primary': 'linear-gradient(135deg, var(--primary-purple) 0%, var(--secondary-purple) 100%)'
            };
            
            const typeIcons = {
                'success': 'bi bi-check-circle',
                'error': 'bi bi-exclamation-triangle',
                'warning': 'bi bi-exclamation-circle',
                'info': 'bi bi-info-circle',
                'primary': 'bi bi-bell'
            };
            
            toast.innerHTML = `
                <div class="toast-header text-white border-0" style="background: ${typeColors[type] || typeColors['primary']}; font-family: 'Poppins', sans-serif;">
                    <i class="${typeIcons[type] || typeIcons['primary']} me-2"></i>
                    <strong class="me-auto">${title}</strong>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="toast" aria-label="Close"></button>
                </div>
                <div class="toast-body" style="font-family: 'Poppins', sans-serif; color: var(--bs-body-color);">${message}</div>
            `;
            
            toastContainer.appendChild(toast);
            setTimeout(() => {
                toast.classList.remove('show');
                setTimeout(() => toast.remove(), 300);
            }, 5000);
        }
    </script>
    @endauth

    {{-- 💡 Additional Scripts from Pages --}}
    @stack('scripts')
    @yield('js')

    {{-- Enhanced Service Worker Registration for PWA --}}
    <script>
        // Service Worker
        if ('serviceWorker' in navigator) {
            navigator.serviceWorker.register('/sw.js')
                .then(reg => console.log('Service Worker registered ✅', reg))
                .catch(err => console.error('Service Worker registration failed ❌', err));
        }

        // Theme Toggle Function
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
            
            // Save preference
            localStorage.setItem('theme', isDark ? 'light' : 'dark');
        }

        // Load saved theme
        document.addEventListener('DOMContentLoaded', function() {
            const savedTheme = localStorage.getItem('theme') || 'light';
            document.documentElement.setAttribute('data-bs-theme', savedTheme);
            
            const toggleBtn = document.querySelector('.btn-toggle i');
            const toggleText = document.querySelector('.btn-toggle');
            if (savedTheme === 'dark') {
                toggleBtn.className = 'bi bi-brightness-high-fill me-1';
                toggleText.innerHTML = '<i class="bi bi-brightness-high-fill me-1"></i> Light Mode';
            }
        });

        // Enhanced PWA Install Prompt
                let deferredPrompt;
                const addBtn = document.createElement('button');
                addBtn.style.display = 'none';
                addBtn.classList.add('btn', 'btn-success', 'position-fixed', 'shadow', 'fade-in');
                addBtn.style.cssText = `
                    bottom: 180px;
                    right: 20px;
                    z-index: 1050;
                    border-radius: 25px;
                    padding: 12px 20px;
                    font-family: 'Poppins', sans-serif;
                    font-weight: 600;
                    background: linear-gradient(135deg, var(--accent-gold) 0%, #ffed4e 100%);
                    color: var(--dark-purple);
                    border: none;
                    min-height: 44px;
                    font-size: 1rem;
                `;
                addBtn.innerHTML = '<i class="bi bi-download me-2"></i> Install App';
                document.body.appendChild(addBtn);

        window.addEventListener('beforeinstallprompt', (e) => {
            e.preventDefault();
            deferredPrompt = e;
            addBtn.style.display = 'block';

            addBtn.addEventListener('click', async (e) => {
                addBtn.style.display = 'none';
                deferredPrompt.prompt();
                const choiceResult = await deferredPrompt.userChoice;
                
                if (choiceResult.outcome === 'accepted') {
                    console.log('✅ App installed successfully');
                    showToast('App installed successfully! 🎉', 'success', 'Installation Complete');
                } else {
                    console.log('❌ App installation dismissed');
                }
                deferredPrompt = null;
            });
        });

        // Back to top functionality
                const backToTop = document.createElement('button');
                backToTop.innerHTML = '<i class="bi bi-arrow-up"></i>';
                backToTop.classList.add('btn', 'btn-primary', 'rounded-circle', 'shadow', 'position-fixed');
                backToTop.style.cssText = `
                    bottom: 160px;
                    right: 20px;
                    display: none;
                    z-index: 1050;
                    width: 50px;
                    height: 50px;
                    min-height: 44px;
                `;
                document.body.appendChild(backToTop);

        window.addEventListener('scroll', function () {
            if (window.pageYOffset > 200) {
                backToTop.style.display = 'block';
            } else {
                backToTop.style.display = 'none';
            }
        });

        backToTop.addEventListener('click', () => {
            window.scrollTo({ top: 0, behavior: 'smooth' });
        });
    </script>
</body>
</html>
