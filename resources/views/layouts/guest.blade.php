<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Amigos '98 Community</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- AdminLTE CSS -->
    <link rel="stylesheet" href="{{ asset('vendor/adminlte/dist/css/adminlte.min.css') }}">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet" integrity="sha512-iecdLmaskl7CVkqkXNQ/ZH/XLlvWZOJyj7Yy7tcenmpD1ypASozpmT/E0iPtmFIB46ZmdtAc9eNBvH0H/ZpiBw==" crossorigin="anonymous" referrerpolicy="no-referrer">
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    <link rel="manifest" href="/manifest.json">
    <!-- Custom & Bootstrap Icons -->
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    
    {{-- E-Bili Theme --}}
    <link rel="stylesheet" href="{{ asset('css/ebili-theme.css') }}">

    <style>
        body.dark-mode {
            background-color: #121212 !important;
            color: #e0e0e0;
        }
        .dark-mode .form-login {
            background-color: #1e1e1e;
            color: #e0e0e0;
            border-color: #333;
        }
        .dark-mode .form-control {
            background-color: #2c2c2c;
            color: #f1f1f1;
            border-color: #444;
        }
        .dark-mode .form-control::placeholder {
            color: #aaa;
        }
        .dark-mode .btn-primary {
            background-color: #3a3a3a;
            border-color: #444;
        }



.btn-primary {
    background-color: #106ae0 !important;
    border-color: #1725c2 !important;
}
.btn-primary:hover {
    background-color: #134ca3 !important;
    border-color: #2534d3 !important;
}


    </style>
</head>
<body class="hold-transition login-page bg-light">
    <div class="container pt-5">
        <!-- 🌙 Dark Mode Toggle -->
        <div class="text-end mb-3">
            <button id="darkModeBtn" class="btn btn-sm btn-outline-secondary" onclick="toggleDarkMode()">
                <i class="bi bi-moon-fill"></i> Dark Mode
            </button>
        </div>

        @yield('content')
    </div>

    {{-- 📱 Reusable Mobile Footer --}}
    {{-- @include('partials.reusable-mobile-footer') --}}

    <!-- Scripts -->
    <script src="{{ asset('vendor/adminlte/plugins/jquery/jquery.min.js') }}"></script>
    <script src="{{ asset('vendor/adminlte/plugins/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('vendor/adminlte/dist/js/adminlte.min.js') }}"></script>

    <script>
        function toggleDarkMode() {
            document.body.classList.toggle('dark-mode');
            const isDark = document.body.classList.contains('dark-mode');
            localStorage.setItem('dark-mode', isDark ? 'enabled' : 'disabled');
            document.getElementById("darkModeBtn").innerHTML = isDark
                ? '<i class="bi bi-sun-fill"></i> Light Mode'
                : '<i class="bi bi-moon-fill"></i> Dark Mode';
        }

       function togglePassword(inputId, iconId) {
    const input = document.getElementById(inputId);
    const icon = document.getElementById(iconId);
    const isHidden = input.type === "password";
    input.type = isHidden ? "text" : "password";
    icon.className = isHidden ? "bi bi-eye-slash fs-5" : "bi bi-eye fs-5";
}

        document.addEventListener('DOMContentLoaded', function () {
            if (localStorage.getItem('dark-mode') === 'enabled') {
                document.body.classList.add('dark-mode');
                document.getElementById("darkModeBtn").innerHTML = '<i class="bi bi-sun-fill"></i> Light Mode';
            }
        });
    </script>
</body>
</html>
