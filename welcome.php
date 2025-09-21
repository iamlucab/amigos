<!DOCTYPE html>
<html lang="en" data-bs-theme="light">
<head>
    <meta charset="UTF-8">
    <title>Welcome to Amigos '98</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    {{-- PWA & Icons --}}
    <link rel="icon" type="image/png" href="{{ asset('favicon-32x32.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('favicon-32x32.png') }}">
    <link rel="manifest" href="{{ asset('manifest.json') }}">

    {{-- Bootstrap & Icons --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    {{-- Font --}}
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500&display=swap" rel="stylesheet">

    <meta name="theme-color" content="#63189e">

    <style>
        body {
            font-family: 'Roboto', sans-serif;
            background: #fff;
            color: #212529;
            transition: background-color 0.3s ease, color 0.3s ease;
        }

        .container {
            max-width: 480px;
            margin: auto;
            padding: 2rem 1rem;
        }

        h1, h3 {
            text-align: center;
        }

        .form-control {
            border-radius: 10px;
            transition: background-color 0.3s ease, color 0.3s ease;
        }

        .btn-primary {
            border-radius: 12px;
            background-color: #63189e !important;
            border-color: #63189e !important;
        }
        
        .btn-primary:hover {
            background-color: #531185 !important;
            border-color: #531185 !important;
        }

        .card-disclaimer {
            font-size: 0.75rem;
            padding: 1rem;
            border-radius: 12px;
            background-color: #e8f4fc;
            border: 1px solid #d3d3d3;
            text-align: center;
            transition: background-color 0.3s ease, color 0.3s ease;
        }

        footer {
            text-align: center;
            font-size: 0.85rem;
            color: #999;
            margin-top: 2rem;
            transition: color 0.3s ease;
        }

        /* Dark Mode */
        body.dark-mode {
            background-color: #121212;
            color: #e0e0e0;
        }

        body.dark-mode .card-disclaimer {
            background-color: #2c2c2c;
            border-color: #444;
            color: #ccc;
        }

        body.dark-mode .form-control,
        body.dark-mode .form-check-label,
        body.dark-mode .btn,
        body.dark-mode input::placeholder {
            background-color: #1f1f1f;
            color: #e0e0e0;
            border-color: #444;
        }

        body.dark-mode .btn-outline-secondary {
            color: #ddd;
            border-color: #666;
        }

        body.dark-mode .btn-outline-secondary:hover {
            background-color: #444;
        }

        body.dark-mode .form-check-input {
            background-color: #333;
            border-color: #555;
        }

        body.dark-mode footer {
            color: #aaa;
        }
        
        /* Mobile Footer */
        .mobile-footer {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            background-color: #fff;
            box-shadow: 0 -2px 10px rgba(0,0,0,0.1);
            padding: 10px 0;
            z-index: 1000;
            transition: background-color 0.3s ease;
        }
        
        body.dark-mode .mobile-footer {
            background-color: #1e1e1e;
            border-top: 1px solid #333;
        }
        
        .mobile-footer .nav-link {
            display: flex;
            flex-direction: column;
            align-items: center;
            font-size: 0.7rem;
            color: #666;
            transition: color 0.3s ease;
        }
        
        body.dark-mode .mobile-footer .nav-link {
            color: #aaa;
        }
        
        .mobile-footer .nav-link.active {
            color: #63189e;
        }
        
        body.dark-mode .mobile-footer .nav-link.active {
            color: #a35edb;
        }
        
        .mobile-footer .nav-link i {
            font-size: 1.2rem;
            margin-bottom: 2px;
        }
        
        /* Add padding to bottom to account for fixed footer */
        body {
            padding-bottom: 60px;
        }
    </style>
</head>
<body>

@if (session('success'))
    <div class="position-fixed bottom-0 end-0 p-3" style="z-index: 9999">
        <div id="joinSuccessToast" class="toast align-items-center text-bg-success border-0" role="alert">
            <div class="d-flex">
                <div class="toast-body">
                    🎉 Thank you for registering. Please wait for admin approval.
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
        </div>
    </div>
@endif

<div class="container">
    <div class="text-end mb-3">
        <button id="darkModeToggle" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-moon"></i> Dark Mode
        </button>
    </div>

    <img src="{{ asset('images/ebili-logo.png') }}" class="img-fluid mb-4" style="max-width: 100px; display: block; margin: auto;" alt="Logo">
    <h1><strong>E-BILI ONLINE</strong></h1>
    <hr>
    <h3 class="mb-4">Join and be a Member</h3>

    <form action="{{ route('guest.register.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <div class="mb-3">
            <label for="first_name">First Name</label>
            <input type="text" name="first_name" class="form-control" required value="{{ old('first_name') }}">
            @error('first_name') <small class="text-danger">{{ $message }}</small> @enderror
        </div>

        <div class="mb-3">
            <label for="middle_name">Middle Name</label>
            <input type="text" name="middle_name" class="form-control" value="{{ old('middle_name') }}">
            @error('middle_name') <small class="text-danger">{{ $message }}</small> @enderror
        </div>

        <div class="mb-3">
            <label for="last_name">Last Name</label>
            <input type="text" name="last_name" class="form-control" required value="{{ old('last_name') }}">
            @error('last_name') <small class="text-danger">{{ $message }}</small> @enderror
        </div>

        <div class="mb-3">
            <label for="birthday">Birthday</label>
            <input type="date" name="birthday" class="form-control" required value="{{ old('birthday') }}">
            @error('birthday') <small class="text-danger">{{ $message }}</small> @enderror
        </div>

        <div class="mb-3">
            <label for="mobile_number">Mobile Number</label>
            <input type="tel" name="mobile_number" id="mobile_number" class="form-control" placeholder="e.g. 09171234567"
                maxlength="11" minlength="11" pattern="[0-9]{11}" inputmode="numeric" required oninput="validateMobile(this)" value="{{ old('mobile_number') }}">
            <small id="mobileError" class="text-danger d-none">Mobile number must be exactly 11 digits.</small>
            @error('mobile_number') <small class="text-danger d-block">{{ $message }}</small> @enderror
        </div>

        <div class="mb-3">
            <label for="occupation">Occupation (Optional)</label>
            <input type="text" name="occupation" class="form-control" value="{{ old('occupation') }}">
            @error('occupation') <small class="text-danger">{{ $message }}</small> @enderror
        </div>

        {{-- Password --}}
        <div class="mb-3 position-relative">
            <label for="password" class="small">Create Password</label>
            <div class="input-group">
                <input type="password" name="password" id="password" class="form-control form-control-sm" required minlength="6" autocomplete="new-password">
                <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                    <i class="bi bi-eye-slash" id="passwordIcon"></i>
                </button>
            </div>
            @error('password') <small class="text-danger">{{ $message }}</small> @enderror
        </div>

        <div class="mb-3">
            <label for="photo">Upload Photo (optional)</label>
            <input type="file" name="photo" class="form-control" accept="image/*">
            @error('photo') <small class="text-danger">{{ $message }}</small> @enderror
        </div>

        {{-- Terms --}}
        <div class="form-check mb-3 small">
            <input class="form-check-input" type="checkbox" id="agreeTerms">
            <label class="form-check-label" for="agreeTerms">
                I agree to the collection and use of my data for membership purposes.
            </label>
        </div>

        <button type="submit" class="btn btn-primary w-100" id="submitBtn" disabled>Register</button>
    </form>
    <div class="text-center mt-3">
        <a href="{{ route('login') }}" class="text-decoration-none"><small>Already have an account? Login</a></small>  

    <div class="card-disclaimer mt-4">
        Thank you for your interest in E-Bili Online.<br>
        All data is strictly used for community-building and app participation.<br>
        <strong>Note: Your registration is subject to admin approval. Admin will assign your sponsor and membership code.</strong>
    </div>
</div>

<footer>
    &copy; {{ date('Y') }} E-Bili Online - Shop to Save, Share to Earn!
</footer>

<!-- Mobile Footer -->
<div class="mobile-footer">
    <div class="container">
        <div class="row text-center">
            <div class="col">
                <a href="{{ route('login') }}" class="nav-link">
                    <i class="bi bi-box-arrow-in-right"></i>
                    <span>Login</span>
                </a>
            </div>
            <div class="col">
                <a href="/welcome.php" class="nav-link active">
                    <i class="bi bi-person-plus"></i>
                    <span>Register</span>
                </a>
            </div>
            <div class="col">
                <a href="{{ route('password.request') }}" class="nav-link">
                    <i class="bi bi-key"></i>
                    <span>Reset</span>
                </a>
            </div>
            <div class="col">
                <a href="{{ url('/') }}" class="nav-link">
                    <i class="bi bi-house"></i>
                    <span>Home</span>
                </a>
            </div>
        </div>
    </div>
</div>

{{-- Scripts --}}
<script>
    function validateMobile(input) {
        input.value = input.value.replace(/\D/g, '');
        const error = document.getElementById('mobileError');
        error.classList.toggle('d-none', input.value.length === 11);
    }

    document.addEventListener('DOMContentLoaded', () => {
        const terms = document.getElementById('agreeTerms');
        const btn = document.getElementById('submitBtn');
        terms.addEventListener('change', () => btn.disabled = !terms.checked);

        const togglePassword = document.getElementById('togglePassword');
        const passwordInput = document.getElementById('password');
        const passwordIcon = document.getElementById('passwordIcon');

        togglePassword.addEventListener('click', function () {
            const type = passwordInput.type === 'password' ? 'text' : 'password';
            passwordInput.type = type;
            passwordIcon.classList.toggle('bi-eye');
            passwordIcon.classList.toggle('bi-eye-slash');
        });

        const darkToggle = document.getElementById('darkModeToggle');
        darkToggle.addEventListener('click', () => {
            document.body.classList.toggle('dark-mode');
            const icon = darkToggle.querySelector('i');
            icon.classList.toggle('bi-moon');
            icon.classList.toggle('bi-sun');
            localStorage.setItem('darkMode', document.body.classList.contains('dark-mode') ? 'enabled' : 'disabled');
        });

        if (localStorage.getItem('darkMode') === 'enabled') {
            document.body.classList.add('dark-mode');
            const icon = document.querySelector('#darkModeToggle i');
            icon.classList.remove('bi-moon');
            icon.classList.add('bi-sun');
        }

        @if (session('success'))
            const toast = new bootstrap.Toast(document.getElementById('joinSuccessToast'));
            toast.show();
            setTimeout(() => window.location.href = "{{ url('/') }}", 4000);
        @endif
    });
</script>

{{-- install prompt  --}}
<script>
    let deferredPrompt;
    const installBtn = document.createElement('button');
    installBtn.id = "installAppBtn";
    installBtn.className = "btn btn-warning w-100 mb-3";
    installBtn.innerHTML = '<i class="bi bi-download"></i> Install E-Bili App';
    installBtn.style.display = 'none';

    // Append the install button inside the container (top of form)
    document.addEventListener('DOMContentLoaded', () => {
        const container = document.querySelector('.container');
        container.insertBefore(installBtn, container.firstChild);
    });

    window.addEventListener('beforeinstallprompt', (e) => {
        e.preventDefault();
        deferredPrompt = e;
        installBtn.style.display = 'block';

        installBtn.addEventListener('click', async () => {
            installBtn.style.display = 'none';
            deferredPrompt.prompt();
            const result = await deferredPrompt.userChoice;
            console.log('[PWA] Install result:', result.outcome);
            deferredPrompt = null;
        });
    });

    window.addEventListener('appinstalled', () => {
        console.log('[PWA] App was installed');
        installBtn.remove();
    });
</script>

{{-- site manifest --}}
<script>
    if ('serviceWorker' in navigator) {
        navigator.serviceWorker.register('/sw.js').then(function (reg) {
            console.log('[SW] Registered', reg);
        }).catch(function (err) {
            console.error('[SW] Registration failed:', err);
        });
    }
</script>

</body>
</html>