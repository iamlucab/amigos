@extends('layouts.guest')

@section('content')

<style>
    @import url('https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500&display=swap');
    @import url('https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css');

    html, body {
        height: 100%;
        margin: 0;
        padding: 0;
        font-family: 'Roboto', sans-serif;
        background-color: #f8f9fa;
        transition: background-color 0.3s ease;
    }

    body.dark-mode {
        background-color: #121212;
        color: #e0e0e0;
    }

    .login-page-wrapper {
        min-height: 100vh;
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        padding: 20px;
    }

    .login-card {
        background-color: #ffffff;
        border-radius: 15px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
        padding: 30px;
        width: 100%;
        max-width: 380px;
        transition: background-color 0.3s ease, color 0.3s ease;
    }

    body.dark-mode .login-card {
        background-color: #1e1e1e;
        color: #e0e0e0;
        border-color: #333;
    }

    .login-logo {
        text-align: center;
        margin-bottom: 25px;
    }

    .login-logo img {
        width: 70px;
        height: auto;
    }

    .login-logo h5 {
        font-size: 1.3rem;
        font-weight: 500;
        margin-top: 10px;
        margin-bottom: 0;
    }

    .form-control {
        border-radius: 10px;
        padding: 12px 15px;
        font-size: 0.9rem;
        border: 1px solid #ddd;
        transition: all 0.3s ease;
        margin-bottom: 15px;
    }

    .form-control:focus {
        box-shadow: 0 0 0 2px rgba(99, 24, 158, 0.25);
        border-color: #63189e;
    }

    .form-control::placeholder {
        font-size: 0.85rem;
        color: #999;
    }

    .eye-icon {
        position: absolute;
        top: 50%;
        right: 15px;
        transform: translateY(-50%);
        cursor: pointer;
        color: #6c757d;
        z-index: 10;
    }

    .btn-primary {
        background-color: #63189e !important;
        border-color: #63189e !important;
        border-radius: 10px;
        padding: 12px;
        font-weight: 500;
        font-size: 0.9rem;
        transition: all 0.3s ease;
        width: 100%;
        margin-bottom: 15px;
    }

    .btn-primary:hover {
        background-color: #531185 !important;
        border-color: #531185 !important;
        transform: translateY(-1px);
    }

    /* Social Login Icons */
    .social-login {
        text-align: center;
        margin: 20px 0;
    }

    .social-login-title {
        font-size: 0.8rem;
        color: #6c757d;
        margin-bottom: 15px;
        position: relative;
    }

    .social-login-title::before {
        content: '';
        position: absolute;
        top: 50%;
        left: 0;
        right: 0;
        height: 1px;
        background-color: #ddd;
        z-index: 1;
    }

    .social-login-title span {
        background-color: #ffffff;
        padding: 0 15px;
        position: relative;
        z-index: 2;
    }

    body.dark-mode .social-login-title span {
        background-color: #1e1e1e;
    }

    .social-icons {
        display: flex;
        justify-content: center;
        gap: 15px;
        margin-bottom: 20px;
    }

    .social-icon {
        width: 45px;
        height: 45px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        text-decoration: none;
        transition: all 0.3s ease;
        font-size: 1.2rem;
        color: white;
    }

    .social-icon:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
    }

    .social-icon.google {
        background-color: #f6ce04;
    }

    .social-icon.google:hover {
        background-color: #109817;
        color: white;
    }

    .social-icon.facebook {
        background-color: #160d97;
    }

    .social-icon.facebook:hover {
        background-color: #1650ce;
        color: white;
    }

    /* Alternative Login Options */
    .alt-login-options {
        text-align: center;
        margin-top: 20px;
    }

    .alt-login-btn {
        display: inline-block;
        padding: 8px 16px;
        margin: 5px;
        border: 1px solid #103dc4;
        border-radius: 20px;
        color: #1324d8;
        text-decoration: none;
        font-size: 0.8rem;
        font-weight: 500;
        transition: all 0.3s ease;
    }

    .alt-login-btn:hover {
        background-color: #149bea;
        color: white;
        text-decoration: none;
    }

    .alt-login-btn i {
        margin-right: 5px;
        font-size: 0.75rem;
    }

    /* Small Links */
    .small-links {
        font-size: 0.8rem;
        margin-top: 20px;
        text-align: center;
    }

    .small-links a {
        color: #175480;
        text-decoration: none;
        margin: 0 8px;
    }

    .small-links a:hover {
        text-decoration: none;
    }

    /* OTP Modal Styles */
    .otp-modal {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.5);
        z-index: 1000;
    }

    .otp-modal-content {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        background-color: white;
        border-radius: 15px;
        padding: 30px;
        width: 90%;
        max-width: 350px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
    }

    body.dark-mode .otp-modal-content {
        background-color: #1e1e1e;
        color: #e0e0e0;
    }

    .otp-input {
        text-align: center;
        font-size: 1.5rem;
        letter-spacing: 0.5rem;
        font-weight: bold;
        margin: 20px 0;
    }

    .countdown-timer {
        font-size: 0.8rem;
        color: #6c757d;
        text-align: center;
        margin: 10px 0;
    }

    .loading-spinner {
        display: inline-block;
        width: 16px;
        height: 16px;
        border: 2px solid #ffffff;
        border-radius: 50%;
        border-top-color: transparent;
        animation: spin 1s ease-in-out infinite;
    }

    @keyframes spin {
        to { transform: rotate(360deg); }
    }

    /* Responsive Design */
    @media (max-width: 576px) {
        .login-card {
            margin: 10px;
            padding: 25px;
        }
        
        .social-icon {
            width: 40px;
            height: 40px;
            font-size: 1.1rem;
        }

        .alt-login-btn {
            font-size: 0.75rem;
            padding: 6px 12px;
        }
    }
</style>

<div class="login-page-wrapper">
    <div class="login-card">
        <div class="login-logo">
            <img src="{{ asset('logo.png') }}" alt="Logo">
            <h5 class="mt-2 fw-bold">Amigos '98 </h5>
        </div>

       {{-- ✅ Mobile & PWA-Friendly Alerts --}}
<style>
    .alert-mobile {
        border-radius: 12px;
        padding: 10px 14px;
        font-size: 0.9rem;
        margin-bottom: 10px;
        display: flex;
        align-items: center;
        gap: 8px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.08);
    }
    .alert-mobile i {
        font-size: 1.1rem;
    }
    .alert-tiny-close {
        margin-left: auto;
        background: transparent;
        border: none;
        color: inherit;
        font-size: 1rem;
        cursor: pointer;
    }
</style>

{{-- Error --}}
@if(session('error'))
<div class="alert alert-danger alert-mobile">
    <i class="bi bi-exclamation-circle-fill"></i>
    <span class="flex-fill">{{ session('error') }}</span>
    <button class="alert-tiny-close" onclick="this.parentElement.remove()">×</button>
</div>
@endif

{{-- Success --}}
@if(session('success'))
<div class="alert alert-success alert-mobile">
    <i class="bi bi-check-circle-fill"></i>
    <span class="flex-fill">{{ session('success') }}</span>
    <button class="alert-tiny-close" onclick="this.parentElement.remove()">×</button>
</div>
@endif

{{-- Status --}}
@if(session('status'))
<div class="alert alert-info alert-mobile">
    <i class="bi bi-info-circle-fill"></i>
    <span class="flex-fill">{{ session('status') }}</span>
    <button class="alert-tiny-close" onclick="this.parentElement.remove()">×</button>
</div>
@endif

{{-- Validation Errors --}}
@if ($errors->any())
<div class="alert alert-danger alert-mobile">
    <i class="bi bi-x-circle-fill"></i>
    <span class="flex-fill">{{ $errors->first() }}</span>
    <button class="alert-tiny-close" onclick="this.parentElement.remove()">×</button>
</div>
@endif

        <!-- Main Login Form -->
        <form method="POST" action="{{ route('login') }}">
            @csrf
            <div class="mb-3">
                <input type="text" name="login" id="login" class="form-control"
                    placeholder="Mobile number or email"
                    value="{{ old('login') }}" required>
            </div>

            <div class="mb-3 position-relative">
                <input type="password" class="form-control" id="passwordInput" name="password"
                    placeholder="Password" required>
                <span class="eye-icon" onclick="togglePassword('passwordInput', 'toggleIcon')">
                    <i id="toggleIcon" class="bi bi-eye-slash"></i>
                </span>
            </div>

            <button type="submit" class="btn btn-primary">
                <i class="fas fa-sign-in-alt me-1"></i> Login
            </button>
        </form>

        <!-- Social Login -->
        <div class="social-login">
            <div class="social-login-title">
                <span>or continue with</span>
            </div>
            
            {{-- <div class="social-icons">
                <a href="{{ route('social.redirect', 'google') }}" class="social-icon google" title="Login with Google">
                    <i class="fab fa-google"></i>
                </a>
                <a href="{{ route('social.redirect', 'facebook') }}" class="social-icon facebook" title="Login with Facebook">
                    <i class="fab fa-facebook-f"></i>
                </a>
            </div> --}}
        </div>

        <!-- Alternative Login Options -->
        <div class="alt-login-options">
            <a href="{{ route('password.request') }}" class="alt-login-btn">
                <i class="fas fa-key"></i> Forgot Password
            </a>
            {{-- <a href="#" class="alt-login-btn" onclick="showOtpModal()">
                <i class="fas fa-sms"></i> Login with OTP
            </a> --}}
            
               <a href="{{ route('guest.register') }}" class="alt-login-btn">
                <i class="fas fa-user"></i> Create Account
            </a>
        </div>

        {{-- <div class="small-links">
            <a href="{{ route('guest.register') }}">Create Account</a>
        </div> --}}
    </div>
</div>

<!-- OTP Modal -->
<div id="otpModal" class="otp-modal">
    <div class="otp-modal-content">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h6 class="mb-0">Login with OTP</h6>
            <button type="button" class="btn-close" onclick="hideOtpModal()"></button>
        </div>
        
        <div id="mobile-step">
            <div class="mb-3">
                <input type="tel" id="otp_mobile_number" class="form-control"
                    placeholder="Mobile number (09XXXXXXXXX)"
                    maxlength="11" minlength="11" pattern="[0-9]{11}" inputmode="numeric" required>
                <small class="text-muted">Enter your registered mobile number</small>
            </div>
            <button type="button" id="sendOtpBtn" class="btn btn-primary">
                <i class="fas fa-paper-plane me-1"></i> Send OTP
            </button>
        </div>
        
        <div id="otp-step" class="d-none">
            <div class="mb-3">
                <input type="text" id="otp_code" class="form-control otp-input"
                    placeholder="000000" maxlength="6" inputmode="numeric" required>
                <small class="text-muted">Enter the 6-digit code sent to your mobile</small>
            </div>
            <div class="countdown-timer" id="countdown"></div>
            <button type="button" id="verifyOtpBtn" class="btn btn-primary mb-2">
                <i class="fas fa-check me-1"></i> Verify OTP
            </button>
            <button type="button" id="resendOtpBtn" class="btn btn-outline-secondary w-100 d-none">
                <i class="fas fa-redo me-1"></i> Resend OTP
            </button>
            <button type="button" id="backToMobileBtn" class="btn btn-link w-100 p-0 mt-2">
                <i class="fas fa-arrow-left me-1"></i> Back
            </button>
        </div>
    </div>
</div>

<hr>
<p class="small text-muted text-center mb-3">Buy to Save, Share to Earn!  | &copy; 2025 by Batch '98</p>

<script>
    // Password toggle functionality
    function togglePassword(inputId, iconId) {
        const input = document.getElementById(inputId);
        const icon = document.getElementById(iconId);
        
        if (input.type === 'password') {
            input.type = 'text';
            icon.classList.remove('bi-eye-slash');
            icon.classList.add('bi-eye');
        } else {
            input.type = 'password';
            icon.classList.remove('bi-eye');
            icon.classList.add('bi-eye-slash');
        }
    }

    // Mobile number validation
    function validateMobile(input) {
        input.value = input.value.replace(/\D/g, '');
    }

    // OTP Modal functionality
    function showOtpModal() {
        document.getElementById('otpModal').style.display = 'block';
        document.getElementById('otp_mobile_number').focus();
    }

    function hideOtpModal() {
        document.getElementById('otpModal').style.display = 'none';
        resetOtpModal();
    }

    function resetOtpModal() {
        document.getElementById('mobile-step').classList.remove('d-none');
        document.getElementById('otp-step').classList.add('d-none');
        document.getElementById('otp_mobile_number').value = '';
        document.getElementById('otp_code').value = '';
        clearCountdown();
    }

    // OTP Login functionality
    document.addEventListener('DOMContentLoaded', function () {
        const sendOtpBtn = document.getElementById('sendOtpBtn');
        const verifyOtpBtn = document.getElementById('verifyOtpBtn');
        const resendOtpBtn = document.getElementById('resendOtpBtn');
        const backToMobileBtn = document.getElementById('backToMobileBtn');
        const mobileStep = document.getElementById('mobile-step');
        const otpStep = document.getElementById('otp-step');
        const mobileInput = document.getElementById('otp_mobile_number');
        const otpInput = document.getElementById('otp_code');
        const countdownEl = document.getElementById('countdown');
        
        let countdownTimer;
        let countdownSeconds = 300; // 5 minutes

        // Focus on login input when page loads
        const loginInput = document.getElementById('login');
        if (loginInput) loginInput.focus();

        // Mobile number input validation
        mobileInput.addEventListener('input', function() {
            validateMobile(this);
        });

        // OTP input validation
        otpInput.addEventListener('input', function() {
            this.value = this.value.replace(/\D/g, '');
        });

        // Close modal when clicking outside
        window.addEventListener('click', function(event) {
            const modal = document.getElementById('otpModal');
            if (event.target === modal) {
                hideOtpModal();
            }
        });

        // Send OTP
        sendOtpBtn.addEventListener('click', function() {
            const mobileNumber = mobileInput.value;
            
            if (!mobileNumber || mobileNumber.length !== 11) {
                showAlert('Please enter a valid 11-digit mobile number', 'danger');
                return;
            }

            setButtonLoading(sendOtpBtn, true);

            fetch('{{ route("otp.send") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({ mobile_number: mobileNumber })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showOtpStep();
                    startCountdown();
                    showAlert(data.message, 'success');
                } else {
                    showAlert(data.message, 'danger');
                }
            })
            .catch(error => {
                showAlert('Network error. Please try again.', 'danger');
            })
            .finally(() => {
                setButtonLoading(sendOtpBtn, false);
            });
        });

        // Verify OTP
        verifyOtpBtn.addEventListener('click', function() {
            const mobileNumber = mobileInput.value;
            const otp = otpInput.value;
            
            if (!otp || otp.length !== 6) {
                showAlert('Please enter a valid 6-digit OTP', 'danger');
                return;
            }

            setButtonLoading(verifyOtpBtn, true);

            fetch('{{ route("otp.verify") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({ 
                    mobile_number: mobileNumber,
                    otp: otp 
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showAlert(data.message, 'success');
                    setTimeout(() => {
                        window.location.href = data.redirect_url;
                    }, 1000);
                } else {
                    showAlert(data.message, 'danger');
                }
            })
            .catch(error => {
                showAlert('Network error. Please try again.', 'danger');
            })
            .finally(() => {
                setButtonLoading(verifyOtpBtn, false);
            });
        });

        // Resend OTP
        resendOtpBtn.addEventListener('click', function() {
            sendOtpBtn.click();
        });

        // Back to mobile step
        backToMobileBtn.addEventListener('click', function() {
            showMobileStep();
            clearCountdown();
        });

        function showOtpStep() {
            mobileStep.classList.add('d-none');
            otpStep.classList.remove('d-none');
            otpInput.focus();
        }

        function showMobileStep() {
            otpStep.classList.add('d-none');
            mobileStep.classList.remove('d-none');
            otpInput.value = '';
            mobileInput.focus();
        }

        function startCountdown() {
            countdownSeconds = 300;
            resendOtpBtn.classList.add('d-none');
            
            countdownTimer = setInterval(() => {
                const minutes = Math.floor(countdownSeconds / 60);
                const seconds = countdownSeconds % 60;
                countdownEl.textContent = `Resend available in ${minutes}:${seconds.toString().padStart(2, '0')}`;
                
                countdownSeconds--;
                
                if (countdownSeconds < 0) {
                    clearInterval(countdownTimer);
                    countdownEl.textContent = '';
                    resendOtpBtn.classList.remove('d-none');
                }
            }, 1000);
        }

        function clearCountdown() {
            if (countdownTimer) {
                clearInterval(countdownTimer);
                countdownEl.textContent = '';
                resendOtpBtn.classList.add('d-none');
            }
        }

        function setButtonLoading(button, loading) {
            if (loading) {
                button.disabled = true;
                const originalText = button.innerHTML;
                button.setAttribute('data-original-text', originalText);
                button.innerHTML = '<span class="loading-spinner me-2"></span>Loading...';
            } else {
                button.disabled = false;
                button.innerHTML = button.getAttribute('data-original-text');
            }
        }

        function showAlert(message, type) {
            // Remove existing alerts
            const existingAlerts = document.querySelectorAll('.alert');
            existingAlerts.forEach(alert => alert.remove());
            
            // Create new alert
            const alertDiv = document.createElement('div');
            alertDiv.className = `alert alert-${type} alert-dismissible fade show`;
            alertDiv.innerHTML = `
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            `;
            
            // Insert alert at the top of the login card
            const loginCard = document.querySelector('.login-card');
            const logoDiv = document.querySelector('.login-logo');
            loginCard.insertBefore(alertDiv, logoDiv.nextSibling);
        }
    });

    // PWA Install functionality (existing code)
    let deferredPrompt = null;

    function showInstallToast() {
        const toast = document.getElementById('installToast');
        if (toast) {
            toast.style.display = 'block';
            new bootstrap.Toast(toast).show();
        }
    }

    function hideInstallToast() {
        const toast = document.getElementById('installToast');
        if (toast) {
            toast.style.display = 'none';
        }
    }

    window.addEventListener('beforeinstallprompt', (e) => {
        e.preventDefault();
        deferredPrompt = e;
        showInstallToast();
    });

    const installBtn = document.getElementById('installBtn');
    if (installBtn) {
        installBtn.addEventListener('click', async () => {
            if (deferredPrompt) {
                deferredPrompt.prompt();
                const { outcome } = await deferredPrompt.userChoice;
                if (outcome === 'accepted') {
                    console.log('User installed the app');
                }
                deferredPrompt = null;
                hideInstallToast();
            }
        });
    }
</script>

@endsection
