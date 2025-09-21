@extends('layouts.guest')

@section('content')
<style>
    @import url('https://fonts.googleapis.com/css2?family=Roboto:wght@400;500&display=swap');

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
        padding: 40px 20px;
    }

    .login-card {
        background-color: #ffffff;
        border-radius: 15px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
        padding: 30px;
        width: 100%;
        max-width: 400px;
        transition: background-color 0.3s ease, color 0.3s ease;
    }

    body.dark-mode .login-card {
        background-color: #1e1e1e;
        color: #e0e0e0;
        border-color: #333;
    }

    .login-logo {
        text-align: center;
        margin-bottom: 20px;
    }

    .login-logo img {
        width: 80px;
        height: auto;
    }

    .form-control {
        border-radius: 12px;
        font-size: 1rem;
    }

    .form-control:focus {
        box-shadow: 0 0 0 2px rgba(0,123,255,0.25);
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

    .btn-primary, .btn-success {
        background-color: #63189e !important;
        border-color: #63189e !important;
    }
    .btn-primary:hover, .btn-success:hover {
        background-color: #531185 !important;
        border-color: #531185 !important;
    }

    .small-links {
        font-size: 0.85rem;
        margin-top: 18px;
        text-align: center;
    }

    .small-links a {
        color: #7207a0;
        text-decoration: none;
        margin: 0 8px;
    }

    .small-links a:hover {
        text-decoration: underline;
    }
</style>

<div class="login-page-wrapper">
    <div class="login-card">
        <div class="login-logo">
            <img src="{{ asset('images/ebili-logo.png') }}" alt="Logo">
            <h5 class="mt-2 fw-bold">Enter Reset Code</h5>
        </div>

        @if (session('status'))
            <div class="alert alert-success small">{{ session('status') }}</div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger small">{{ $errors->first() }}</div>
        @endif

        <form method="POST" action="{{ route('password.sms.reset') }}">
            @csrf

            <div class="mb-3">
                <label for="mobile_number" class="form-label">Mobile Number</label>
                <input type="text" name="mobile_number" id="mobile_number" class="form-control" 
                    required value="{{ old('mobile_number') }}" placeholder="09XXXXXXXXX"
                    maxlength="11" minlength="11" pattern="[0-9]{11}" inputmode="numeric"
                    oninput="this.value = this.value.replace(/\D/g, '')">
            </div>

            <div class="mb-3">
                <label for="token" class="form-label">Reset Code</label>
                <input type="text" name="token" id="token" class="form-control" 
                    required placeholder="Enter 6-digit code">
            </div>

            <div class="mb-3 position-relative">
                <label for="password" class="form-label">New Password</label>
                <input type="password" name="password" id="passwordInput" class="form-control" 
                    required placeholder="New password">
                <span class="eye-icon" onclick="togglePassword('passwordInput', 'toggleIcon')">
                    <i id="toggleIcon" class="bi bi-eye-slash fs-5"></i>
                </span>
            </div>

            <div class="mb-4 position-relative">
                <label for="password_confirmation" class="form-label">Confirm Password</label>
                <input type="password" name="password_confirmation" id="confirmPasswordInput" class="form-control" 
                    required placeholder="Confirm new password">
                <span class="eye-icon" onclick="togglePassword('confirmPasswordInput', 'toggleIconConfirm')">
                    <i id="toggleIconConfirm" class="bi bi-eye-slash fs-5"></i>
                </span>
            </div>

            <button type="submit" class="btn btn-success w-100">Reset Password</button>
        </form>

        <div class="small-links">
            <a href="{{ route('login') }}" class="small">Back to Login</a>
        </div>
    </div>
</div>

<hr>
<p class="small text-muted text-center mb-3">Shop to Save, Share to Earn! &copy; 2025</p>
@endsection
