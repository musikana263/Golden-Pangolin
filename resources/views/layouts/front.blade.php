<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    @hasSection('seo')
        @yield('seo')
    @else
        <x-seo />
    @endif

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Playfair+Display:wght@400;600;700&display=swap" rel="stylesheet">

    <!-- Bootstrap 5.3 — self-hosted so the layout never depends on a CDN being reachable.
         (On flaky mobile networks the CDN copy was sometimes failing to load, leaving
         the page rendering as bare HTML.) -->
    <link href="{{ asset('css/bootstrap5.min.css') }}" rel="stylesheet">

    <!-- Font Awesome 6 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <!-- Preload hero background so it appears immediately on all devices -->
    <link rel="preload" as="image" href="{{ asset('images/place-1.jpg') }}">

    <!-- AOS (animations) -->
    <link rel="stylesheet" href="https://unpkg.com/aos@2.3.1/dist/aos.css">

    <!-- Unified Theme -->
    <link rel="stylesheet" href="{{ asset('css/theme.css') }}">
    <link rel="stylesheet" href="{{ asset('css/dark-theme.css') }}">


    <link rel="icon" type="image/png" href="{{ asset('images/logo.png') }}">

    {{-- Apply stored theme before paint to avoid flash. Light is the default;
         auth pages always force dark for focus. --}}
    @php
        $forceDark = request()->routeIs('login', 'register', 'password.*', 'verification.*', 'logout');
    @endphp
    <script>
        (function () {
            try {
                localStorage.removeItem('gp_theme');
                localStorage.removeItem('gp_theme_v2');
                var forceDark = {{ $forceDark ? 'true' : 'false' }};
                var t = localStorage.getItem('gp_theme_v3');
                if (forceDark || t === 'dark') {
                    document.documentElement.setAttribute('data-theme', 'dark');
                }
            } catch (e) {
                @if ($forceDark)
                    document.documentElement.setAttribute('data-theme', 'dark');
                @endif
            }
        })();
    </script>

    @stack('styles')
</head>
<body>
    <!-- Animated pangolin glow bloom -->
    <div class="gp-glow-overlay" aria-hidden="true"></div>

    <!-- Accessibility skip link -->
    <a href="#main-content" class="skip-link">Skip to main content</a>

    <!-- Flash Messages -->
    <x-flash-messages />

    @yield('page')

    <main id="main-content">
        @yield('content')
    </main>

    {{-- WhatsApp float — context-aware.
         - On the wishlist page: "Send wishlist on WhatsApp", shown to everyone.
         - Otherwise: "Chat with us", guests only, never on auth pages.
         The partial reads the `mode` variable to decide which version to render. --}}
    @php
        // Auth pages (login, OTP, password reset, email verify) get no
        // floating chat button — keep focus on the form.
        $isAuthPage = request()->routeIs('login', 'register', 'logout',
                                         'password.*', 'verification.*',
                                         'otp.*');
        $waMode = null;
        // Show "Chat with us" only to guests, on non-auth public pages, and
        // never on the wishlist (which has its own inline send-wishlist modal).
        if (! auth()->check() && ! $isAuthPage && ! request()->routeIs('wishlist.index')) {
            $waMode = 'chat';
        }
    @endphp
    @if ($waMode)
        @include('partials.whatsapp-float', ['mode' => $waMode])
    @endif
    {{-- Visitor-facing hints + consent are skipped for admins viewing the site. --}}
    @if (! (auth()->check() && auth()->user()->isAdmin()))
        @include('partials.cookie-consent')
        @include('partials.wishlist-hint')
    @endif
    @include('partials.theme-hint')

    <!-- Bootstrap 5 JS — self-hosted (see CSS note above) -->
    <script src="{{ asset('js/bootstrap5.bundle.min.js') }}"></script>

    <!-- AOS -->
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>

    <script>
        // Init AOS
        AOS.init({ duration: 800, easing: 'ease-out', once: true, offset: 60 });

        // Navbar scroll class
        window.addEventListener('scroll', () => {
            const nav = document.querySelector('.tt-navbar');
            if (nav) nav.classList.toggle('scrolled', window.scrollY > 40);
        });

        // Theme toggle — dark is the default; we persist explicit choice.
        document.addEventListener('click', function (e) {
            var btn = e.target.closest('[data-gp-theme-toggle]');
            if (!btn) return;
            var current = document.documentElement.getAttribute('data-theme') === 'dark' ? 'dark' : 'light';
            var next = current === 'dark' ? 'light' : 'dark';
            if (next === 'dark') {
                document.documentElement.setAttribute('data-theme', 'dark');
            } else {
                document.documentElement.removeAttribute('data-theme');
            }
            try { localStorage.setItem('gp_theme_v3', next); } catch (err) {}
            // Dismiss the first-visit hint once the user actually uses it.
            try { localStorage.setItem('gp_theme_hint_seen_v2', '1'); } catch (err) {}
            var hint = document.getElementById('gp-theme-hint');
            if (hint) hint.classList.add('gp-theme-hint--gone');
        });

        // Close any open Bootstrap navbar-collapse when the user taps outside
        // it (and outside its toggler). Without this the hamburger menu
        // stays open until a link or the toggler is tapped.
        document.addEventListener('click', function (e) {
            document.querySelectorAll('.navbar-collapse.show').forEach(function (menu) {
                if (menu.contains(e.target)) return;
                var toggler = document.querySelector('[data-bs-target="#' + menu.id + '"]');
                if (toggler && toggler.contains(e.target)) return;

                var instance = bootstrap.Collapse.getInstance(menu)
                            || new bootstrap.Collapse(menu, { toggle: false });
                instance.hide();
            });
        });
    </script>

    @stack('scripts')
</body>
</html>