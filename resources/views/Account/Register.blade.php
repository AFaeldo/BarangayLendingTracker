<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Account — Brgy. San Antonio Lending Tracker</title>

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

            <h1>Create Admin Account</h1>

            @if ($errors->any())
                <div class="error-message" style="margin-bottom:12px;">
                    <ul style="margin:0; padding-left:18px; text-align:left;">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

<form class="login-form" method="POST" action="{{ route('register.store') }}">
    @csrf

    <div class="input-group">
        <span class="icon"><i class="fa-solid fa-user"></i></span>
        <input
            type="text"
            name="name"
            placeholder="Full Name"
            value="{{ old('name') }}"
            required
        >
    </div>

    <div class="input-group">
        <span class="icon"><i class="fa-solid fa-id-badge"></i></span>
        <input
            type="text"
            name="username"
            placeholder="Username"
            value="{{ old('username') }}"
            required
        >
    </div>

    <div class="input-group">
        <span class="icon"><i class="fa-solid fa-envelope"></i></span>
        <input
            type="email"
            name="email"
            placeholder="Email"
            value="{{ old('email') }}"
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

    <div class="input-group">
        <span class="icon"><i class="fa-solid fa-lock"></i></span>
        <input
            type="password"
            name="password_confirmation"
            placeholder="Confirm Password"
            required
        >
    </div>

    <button type="submit" class="login-button">Create Account</button>
</form>


          

        </div>
    </div>

</body>

</html>
