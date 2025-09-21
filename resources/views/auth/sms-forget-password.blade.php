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
            <h5 class="mt-2 fw-bold">Reset via SMS</h5>
        </div>

        @if (session('status'))
            <div class="alert alert-success small">{{ session('status') }}</div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger small">{{ $errors->first() }}</div>
        @endif

        <form method="POST" action="{{ route('password.sms.send') }}">
            @csrf

            <div class="mb-3">
                <label for="mobile_number" class="form-label">Mobile Number</label>
                <input type="text" name="mobile_number" id="mobile_number" class="form-control" 
                    value="{{ old('mobile_number') }}" required placeholder="09XXXXXXXXX" 
                    inputmode="numeric" maxlength="11" minlength="11" pattern="[0-9]{11}"
                    oninput="this.value = this.value.replace(/\D/g, '')">
            </div>

            <button type="submit" class="btn btn-primary w-100">Send Reset Code</button>
        </form>

        <div class="small-links">
            <a href="{{ route('login') }}" class="small">Back to Login</a>
        </div>
    </div>
</div>

<hr>
<p class="small text-muted text-center mb-3">Shop to Save, Share to Earn! &copy; 2025</p>
@endsection
