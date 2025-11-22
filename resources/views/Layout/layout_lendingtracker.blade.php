{{-- resources/views/layouts/lendingtracker.blade.php --}}
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>@yield('title', 'Brgy. San Antonio')</title>

    {{-- Load CSS & JS from resources via Vite --}}
    @vite(['resources/css/style.css', 'resources/js/script.js'])

    {{-- Font Awesome --}}
    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>

<body>
<div class="overlay" aria-hidden="true"></div>
<div class="app">
    {{-- SIDEBAR --}}
    <aside class="sidebar" role="navigation" aria-label="Primary">
        <div class="brand">
           <img src="{{ asset('image/logo.png') }}" alt="logo">
            <h1>Brgy. San Antonio</h1>
        </div>
        <h2>WELCOME ADMINISTRATOR!</h2>
        <hr class="divider">

        <nav class="nav" aria-label="Main nav">
            <a href="{{ route('dashboard') }}"
               class="{{ request()->routeIs('dashboard') ? 'active' : '' }}">
                <i class="fas fa-tachometer-alt"></i> Dashboard
            </a>
            <a href="{{ route('residents.index') }}"
               class="{{ request()->routeIs('residents.*') ? 'active' : '' }}">
                <i class="fas fa-users"></i> Residents
            </a>
            <a href="{{ route('items.index') }}"
               class="{{ request()->routeIs('items.*') ? 'active' : '' }}">
                <i class="fas fa-boxes"></i> Items
            </a>
            <a href="{{ route('borrowing.index') }}"
               class="{{ request()->routeIs('borrowing.*') ? 'active' : '' }}">
                <i class="fas fa-file-alt"></i> Borrowed Record
            </a>
            <a href="{{ route('reports.index') }}"
               class="{{ request()->routeIs('reports.*') ? 'active' : '' }}">
                <i class="fas fa-chart-line"></i> Reports
            </a>
        </nav>
    </aside>

    {{-- MAIN --}}
    <main class="main">
        <header class="header">
            <div class="left-controls">
                <button class="btn-hamburger" aria-label="Toggle sidebar" aria-expanded="false">
                    <span></span>
                    <span></span>
                    <span></span>
                </button>

                <div class="page-title" style="text-transform: uppercase;">
                    @yield('page-title', 'Dashboard')
                </div>

            </div>
            <div class="header-right">
                <div class="profile" id="profile-menu" role="button" tabindex="0">
                    <i class="fas fa-user-circle"></i>
                    <div class="name">Admin</div>
                </div>
                <div class="dropdown" id="dropdown-menu">
    <form method="POST" action="{{ route('logout') }}">
        @csrf
        <button type="submit" style="background:none; border:none; width:100%; text-align:left; padding:10px 16px; cursor:pointer;">
            <i class="fas fa-sign-out-alt"></i> Logout
        </button>
    </form>
</div>

            </div>
        </header>

        <section class="content" role="main">
            @yield('content')
        </section>
    </main>
</div>

@stack('scripts')
</body>
</html>
