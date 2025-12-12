<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password — Brgy. San Antonio</title>

    @vite(['resources/css/Login.css'])
</head>

<body>
<div class="login-container">
    <div class="header-band"></div>

    <div class="main-content">

        <h1>Reset Password</h1>

        @if (session('status'))
            <div class="success-message">
                {{ session('status') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="error-message">
                <ul class="error-list">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form class="login-form" method="POST" action="{{ route('password.reset.code.update') }}">
            @csrf

            <div class="input-group">
                <span class="icon"><i class="fa-solid fa-envelope"></i></span>
                <input
                    type="email"
                    name="email"
                    placeholder="Email used for your account"
                    value="{{ old('email') }}"
                    required
                >
            </div>

            <div class="input-group">
                <span class="icon"><i class="fa-solid fa-key"></i></span>
                <input
                    type="text"
                    name="code"
                    placeholder="Enter reset code from email"
                    value="{{ old('code') }}"
                    required
                >
            </div>

            <div class="input-group">
                <span class="icon"><i class="fa-solid fa-lock"></i></span>
                <input
                    type="password"
                    name="password"
                    placeholder="New Password"
                    required
                >
            </div>

            <div class="input-group">
                <span class="icon"><i class="fa-solid fa-lock"></i></span>
                <input
                    type="password"
                    name="password_confirmation"
                    placeholder="Confirm New Password"
                    required
                >
            </div>

            <button type="submit" class="login-button">
                Update Password
            </button>
        </form>

        <p class="back-link">
            <a href="{{ route('login') }}">← Back to Login</a>
        </p>

    </div>
</div>
</body>
</html>