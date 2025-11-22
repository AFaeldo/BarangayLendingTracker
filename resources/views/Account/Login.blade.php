<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Brgy. San Antonio Lending Tracker — Login</title>

    {{-- Load CSS --}}
    @vite(['resources/css/Login.css'])

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Protest+Strike&display=swap" rel="stylesheet">

    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>

<body>

    <div class="login-container">
        <div class="header-band"></div>

        <div class="main-content">

            <div class="logo-area">
                <img src="{{ asset('image/logo.png') }}" alt="logo">
            </div>

            <h1>Brgy. San Antonio<br>Lending Tracker</h1>

            {{-- ERROR MESSAGE --}}
            @if ($errors->has('login'))
                <div class="error-message">
                    {{ $errors->first('login') }}
                </div>
            @endif

            <form class="login-form" method="POST" action="{{ route('login.attempt') }}">
                @csrf

                <div class="input-group">
                    <span class="icon"><i class="fa-solid fa-user"></i></span>
                    <input
                        type="text"
                        name="username"
                        placeholder="Username"
                        value="{{ old('username') }}"
                        required
                    >
                </div>

                <div class="input-group">
                    <span class="icon"><i class="fa-solid fa-lock"></i></span>
                    <input
                        type="password"
                        name="password"
                        placeholder="Password"
                        required
                    >
                </div>

                <button type="submit" class="login-button">Login</button>
            </form>

            {{-- Forgot password link --}}
            <p class="forgot-link">
                <a href="{{ route('forgot.password') }}">Forgot password?</a>
            </p>

            {{-- Show Create Account link ONLY if no users exist --}}
            @if (\App\Models\User::count() === 0)
                <p class="signup-link">
                    Don't have an account yet?
                    <a href="{{ route('register') }}">Create account</a>
                </p>
            @endif

        </div>
    </div>

</body>

</html>
