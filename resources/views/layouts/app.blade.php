<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/svg+xml" href="{{ asset('images/logo/logo.svg') }}">
    <title>Volcanic - @yield('title', 'Home')</title>
    @vite(['resources/css/styles.css', 'resources/js/main.js'])
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <!-- Additional CSS -->
    @yield('additional_css')

    <!-- Additional JS -->
    @yield('head_js')
</head>
<body class="@yield('body_class')">
    <!-- Header with Navigation -->
    <header>
        <nav class="modern-nav">
            <div class="nav-bubble-container">
                <svg xmlns="http://www.w3.org/2000/svg">
                    <defs>
                        <filter id="goo">
                            <feGaussianBlur in="SourceGraphic" stdDeviation="8" result="blur" />
                            <feColorMatrix in="blur" mode="matrix" values="1 0 0 0 0  0 1 0 0 0  0 0 1 0 0  0 0 0 18 -8"
                                result="goo" />
                            <feBlend in="SourceGraphic" in2="goo" />
                        </filter>
                    </defs>
                </svg>
                <div class="nav-gradient-container">
                    <div class="nav-g1"></div>
                    <div class="nav-g2"></div>
                    <div class="nav-g3"></div>
                    <div class="nav-g4"></div>
                    <div class="nav-g5"></div>
                    <div class="nav-g6"></div>
                    <div class="nav-g7"></div>
                    <div class="nav-g8"></div>
                    <div class="nav-interactive"></div>
                </div>
            </div>
            <div class="nav-container">
                <!-- Logo -->
                <nav class="nav-logo">
                    <a href="{{ route('home') }}" aria-label="Home">
                        <img src="{{ asset('images/logo/volcanic.svg') }}" alt="Volcanic Logo" class="nav-logo-img">
                    </a>
                </nav>
                <!-- NavBar Links -->
                <div class="nav-links">
                    <a href="{{ route('home') }}" class="nav-link {{ request()->routeIs('home') ? 'active' : '' }}">
                        <i class="fa-solid fa-house"></i>
                        Home
                    </a>
                    {{-- Show "My Volcanoes" only when logged in --}}
                    @auth
                        <a href="{{ route('my-volcanoes') }}" class="nav-link {{ request()->routeIs('my-volcanoes') ? 'active' : '' }}">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 640"
                                style="width: 28px; height: 28px; fill: currentColor; vertical-align: middle;">
                                <path d="M224 208C188.7 208 160 179.3 160 144C160 108.7 188.7 80 224 80C239.7 80 254 85.6 265.2 95C276.4 76.4 296.8 64 320 64C343.2 64 363.6 76.4 374.8 95C385.9 85.6 400.3 80 416 80C451.3 80 480 108.7 480 144C480 179.3 451.3 208 416 208C401.3 208 387.7 203 376.9 194.7L344.9 242.7C339.3 251 330 256 320 256C310 256 300.7 251 295.1 242.7L263.1 194.7C252.3 203 238.7 208 224 208zM208 416L256.4 391.8C266.6 386.7 278 384 289.4 384C309 384 327.8 391.8 341.6 405.6L374.1 438.1C380.4 444.4 389 448 397.9 448C409.2 448 419.7 442.4 425.9 433L435.6 418.4L376.6 352.1C367.5 341.9 354.4 336 340.7 336L298.9 336C285.2 336 272.1 341.9 263 352.1L203.1 419.5L207.8 416zM227.4 320.2C245.6 299.7 271.7 288 299.2 288L341 288C368.4 288 394.5 299.7 412.8 320.2L563 489.2C571.5 498.7 576.2 511.1 576.2 523.9C576.2 552.7 552.8 576.1 524 576.1L116.2 576C87.4 576 64 552.6 64 523.8C64 511.1 68.7 498.7 77.2 489.2L227.4 320.2z" />
                            </svg>
                            My Volcanoes
                        </a>

                        {{-- Control Dashboard (only for admins) --}}
                        @if(Auth::user()->is_admin)
                            <a href="{{ route('admin.dashboard') }}" class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                                <i class="fa-solid fa-crown"></i>
                                Control Dashboard
                            </a>
                        @endif
                    @endauth
                </div>

                <!-- User and LogIn -->
                <div class="nav-actions">
                    {{-- Show only when logged in --}}
                    @auth
                        <a href="{{ route('profile') }}" class="profile-link {{ request()->routeIs('profile') ? 'active' : '' }}">
                            <i class="fa-solid fa-user"></i>
                            Profile
                        </a>

                        <form action="{{ route('logout') }}" method="POST" style="display:inline;">
                            @csrf
                            <button type="submit" class="login-btn" style="border:none;">
                                <i class="fa-solid fa-right-from-bracket"></i> Logout
                            </button>
                        </form>
                    @endauth

                    {{-- Show only when not logged in --}}
                    @guest
                        <a href="{{ route('login') }}" class="login-btn {{ request()->routeIs('login') ? 'active' : '' }}">
                            <i class="fa-solid fa-right-to-bracket"></i> Log in
                        </a>
                    @endguest
                </div>
            </div>
        </nav>
    </header>

    <!-- Main Content -->
    <main>
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="glass-footer">
        <div class="footer-container">
            <div class="footer-content">
                <div class="footer-info">
                    <span>Made by Group 3 SDU Software Students © <span id="currentYear">{{ date('Y') }}</span> Volcanic. All
                        rights reserved.</span>
                </div>
                <div class="footer-volcano">
                    <i class="fa-solid fa-volcano"></i>
                </div>
            </div>
        </div>
    </footer>

    <!-- Scripts -->
    @yield('scripts')

    <!-- Back to Top Button -->
    <button id="back-to-top" class="back-to-top-btn">
        <i class="fas fa-arrow-up"></i>
    </button>

</body>
</html>