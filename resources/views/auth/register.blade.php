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
        padding-right: 2.5rem;
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

  .btn-primary {
    background-color: #63189e !important;
    border-color: #63189e !important;
}
.btn-primary:hover {
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
            <h5 class="mt-2 fw-bold">Register a new membership</h5>
        </div>

        @if(session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif
        @if(session('status'))
            <div class="alert alert-danger">{{ session('status') }}</div>
        @endif
        @if ($errors->any())
            <div class="alert alert-danger">{{ $errors->first() }}</div>
        @endif

        <form action="{{ route('register') }}" method="POST">
            @csrf

            <div class="mb-3 position-relative">
                <input type="text" name="name" class="form-control" placeholder="Full Name" required>
            </div>

            <div class="mb-3 position-relative">
                <input type="text" name="mobile_number" id="mobile_number" class="form-control" 
                    placeholder="Mobile Number" required
                    maxlength="11" minlength="11" pattern="[0-9]{11}" inputmode="numeric"
                    oninput="validateMobile(this)">
                <small id="mobileError" class="text-danger d-none">Your 11-digits mobile number</small>
            </div>

            <div class="mb-3 position-relative">
                <input type="password" name="password" id="passwordInput" class="form-control" placeholder="Password" required>
                <span class="eye-icon" onclick="togglePassword('passwordInput', 'toggleIcon')">
                    <i id="toggleIcon" class="bi bi-eye-slash fs-5"></i>
                </span>
            </div>

            <div class="mb-3 position-relative">
                <input type="password" name="password_confirmation" id="confirmPasswordInput" class="form-control" placeholder="Confirm Password" required>
                <span class="eye-icon" onclick="togglePassword('confirmPasswordInput', 'toggleIconConfirm')">
                    <i id="toggleIconConfirm" class="bi bi-eye-slash fs-5"></i>
                </span>
            </div>

            <button type="submit" class="btn btn-primary btn-md w-100">
                Register <i class="fa fa-user-plus ms-1"></i>
            </button>
        </form>

        <div class="small-links">
            <a href="{{ route('login') }}">I already have a membership</a>
        </div>
    </div>
</div>

<hr>
<p class="small text-muted text-center mb-3">Shop to Save, Share to Earn! &copy; 2025</p>

<script>
    function validateMobile(input) {
        input.value = input.value.replace(/\D/g, '');
        const errorMsg = document.getElementById('mobileError');
        errorMsg.classList.toggle('d-none', input.value.length === 11);
    }

    document.addEventListener('DOMContentLoaded', function () {
        const nameInput = document.querySelector('input[name="name"]');
        if (nameInput) nameInput.focus();
    });
</script>

@endsection
