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
        padding-bottom: 80px; /* Add padding for mobile footer */
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
</style>

<div class="login-page-wrapper">
    <div class="login-card">
        <div class="login-logo">
            <img src="{{ asset('images/ebili-logo.png') }}" alt="Logo">
            <h5 class="mt-2 fw-bold">Reset Your Password</h5>
        </div>

        @if (session('status'))
            <div class="alert alert-success text-center">
                {{ session('status') }}
            </div>
        @endif

        <form method="POST" action="{{ route('password.email') }}">
            @csrf

            <div class="mb-3">
                <label for="email"><small>Enter your email address</small></label>
                <input type="email" class="form-control" name="email" required autofocus>
                @error('email') <small class="text-danger">{{ $message }}</small> @enderror
            </div>

            <button type="submit" class="btn btn-primary text-white w-100"><small>Send Password Reset Link</small></button>
            <div class="small-links">
                <small>Remembered your password? <a href="{{ route('login') }}">Login</a></small>
            </div>
        </form>
    </div>
</div>

<hr>
<p class="small text-muted text-center mb-3">Shop to Save, Share to Earn! &copy; 2025</p>

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
                <a href="{{ route('guest.register') }}" class="nav-link">
                    <i class="bi bi-person-plus"></i>
                    <span>Register</span>
                </a>
            </div>
            <div class="col">
                <a href="{{ route('password.request') }}" class="nav-link active">
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
@endsection
