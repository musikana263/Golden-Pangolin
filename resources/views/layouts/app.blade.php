<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Admin Dashboard') — Golden Pangolin</title>

    <!-- Fonts & Icons -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Playfair+Display:wght@400;700&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">

    <!-- Bootstrap 5.3 + Admin Theme — self-hosted (CDN was unreliable on some mobile networks) -->
    <link href="{{ asset('css/bootstrap5.min.css') }}" rel="stylesheet">
    <link href="{{ asset('css/admin.css') }}" rel="stylesheet">


    <link rel="icon" type="image/png" href="{{ asset('images/logo.png') }}">

    @yield('css')
</head>

<body class="admin-body">
    <!-- Topbar -->
    <nav class="admin-topbar navbar navbar-expand-md">
        <div class="container-fluid px-4">
            <a class="navbar-brand d-flex align-items-center gap-2" href="{{ url('/') }}">
                <img src="{{ asset('images/logo.png') }}" alt="Golden Pangolin"
                     style="height:34px; width:auto;">
                <span>Golden <span class="brand-accent">Pangolin</span> Admin</span>
            </a>
            <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse"
                data-bs-target="#adminNav" aria-controls="adminNav" aria-expanded="false">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="adminNav">
                {{-- Mobile-only admin menu: replicates the sidebar so admins
                     can navigate from the hamburger on phones. Hidden on >= md
                     where the sidebar takes over. --}}
                @auth
                <ul class="navbar-nav d-md-none gp-mobile-admin-menu w-100">
                    <li class="nav-item nav-section">Main</li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('home') ? 'active' : '' }}" href="{{ route('home') }}">
                            <i class="fas fa-chart-pie"></i> Dashboard
                        </a>
                    </li>
                    @if (auth()->user()->isAdmin())
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('users.*') ? 'active' : '' }}" href="{{ route('users.index') }}">
                                <i class="fas fa-users"></i> Users
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('settings.*') ? 'active' : '' }}" href="{{ route('settings.edit') }}">
                                <i class="fas fa-sliders"></i> Settings
                            </a>
                        </li>
                    @endif

                    <li class="nav-item nav-section">Content</li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('destinations.*') ? 'active' : '' }}" href="{{ route('destinations.index') }}">
                            <i class="fas fa-map-marker-alt"></i> Destinations
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('activities.*') ? 'active' : '' }}" href="{{ route('activities.index') }}">
                            <i class="fas fa-hiking"></i> Activities
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('tour-packages.*') ? 'active' : '' }}" href="{{ route('tour-packages.index') }}">
                            <i class="fas fa-suitcase-rolling"></i> Packages
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('accommodations.*') ? 'active' : '' }}" href="{{ route('accommodations.index') }}">
                            <i class="fas fa-hotel"></i> Accommodations
                        </a>
                    </li>
                    @if (auth()->user()->isAdmin())
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('accommodation-types.*') ? 'active' : '' }}" href="{{ route('accommodation-types.index') }}">
                                <i class="fas fa-bed"></i> Accommodation Types
                            </a>
                        </li>
                    @endif
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('categories.*') ? 'active' : '' }}" href="{{ route('categories.index') }}">
                            <i class="fas fa-folder"></i> Categories
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('blog.*') ? 'active' : '' }}" href="{{ route('blog.index') }}">
                            <i class="fas fa-newspaper"></i> Blog Posts
                        </a>
                    </li>

                    <li class="nav-item nav-divider"></li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('trash.*') ? 'active' : '' }}" href="{{ route('trash.index') }}">
                            <i class="fas fa-trash-can-arrow-up"></i> Trash
                        </a>
                    </li>

                    <li class="nav-item nav-section">Account</li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ url('/') }}" target="_blank">
                            <i class="fas fa-external-link-alt"></i> View Site
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('users.profile') }}">
                            <i class="fas fa-user-edit"></i> My Profile
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-danger" href="{{ route('logout') }}"
                           onclick="event.preventDefault(); document.getElementById('logout-form-mobile').submit();">
                            <i class="fas fa-sign-out-alt"></i> Logout
                        </a>
                        <form id="logout-form-mobile" action="{{ route('logout') }}" method="POST" class="d-none">
                            @csrf
                        </form>
                    </li>
                </ul>
                @endauth

                {{-- Desktop-only top-right: keeps the existing View Site +
                     username dropdown for >= md screens. Hidden on phones. --}}
                <ul class="navbar-nav ms-auto align-items-center d-none d-md-flex">
                    <li class="nav-item me-2">
                        <a class="nav-link" href="{{ url('/') }}" target="_blank">
                            <i class="fas fa-external-link-alt me-1"></i> View Site
                        </a>
                    </li>
                    @guest
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('login') }}">{{ __('Login') }}</a>
                        </li>
                        @if (Route::has('register'))
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('register') }}">{{ __('Register') }}</a>
                        </li>
                        @endif
                    @else
                        <li class="nav-item dropdown">
                            <a id="navbarDropdown" class="nav-link dropdown-toggle d-flex align-items-center gap-2" href="#"
                                role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <span class="admin-user-avatar">
                                    <i class="fas fa-user"></i>
                                </span>
                                {{ Auth::user()->name }}
                            </a>
                            <div class="dropdown-menu dropdown-menu-end">
                                <a class="dropdown-item" href="{{ route('users.profile') }}">
                                    <i class="fas fa-user-edit me-2"></i>{{ __('My Profile') }}
                                </a>
                                <hr class="dropdown-divider">
                                <a class="dropdown-item text-danger" href="{{ route('logout') }}"
                                    onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                    <i class="fas fa-sign-out-alt me-2"></i>{{ __('Logout') }}
                                </a>
                                <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                    @csrf
                                </form>
                            </div>
                        </li>
                    @endguest
                </ul>
            </div>
        </div>
    </nav>

    <!-- Main Layout -->
    @auth
    <div class="container-fluid px-4">
        {{-- Flash messages — small auto-dismissing toast in the top-right
             corner. Slides in, lingers, fades out automatically. --}}
        @if (session()->has('success') || session()->has('error'))
            @php
                $isError = session()->has('error');
                $msg     = $isError ? session()->get('error') : session()->get('success');
            @endphp
            <div class="gp-toast {{ $isError ? 'gp-toast--error' : 'gp-toast--success' }}"
                 role="status" aria-live="polite" data-gp-toast>
                <i class="fas {{ $isError ? 'fa-exclamation-circle' : 'fa-check-circle' }}"></i>
                <span>{{ $msg }}</span>
                <button type="button" class="gp-toast__close" aria-label="Dismiss">&times;</button>
            </div>
            <style>
                .gp-toast {
                    position: fixed;
                    top: 80px;
                    right: 20px;
                    z-index: 1090;
                    display: inline-flex;
                    align-items: center;
                    gap: .6rem;
                    background: #fff;
                    color: #1a1208;
                    border-left: 4px solid #16a34a;
                    border-radius: 10px;
                    padding: .7rem 2.4rem .7rem 1rem;
                    box-shadow: 0 10px 28px rgba(0, 0, 0, .12);
                    font-size: .92rem;
                    font-family: 'Inter', system-ui, sans-serif;
                    opacity: 0;
                    transform: translateY(-8px);
                    transition: opacity .25s ease, transform .25s ease;
                    max-width: calc(100vw - 40px);
                }
                .gp-toast[data-shown="1"] {
                    opacity: 1;
                    transform: translateY(0);
                }
                .gp-toast--success i { color: #16a34a; }
                .gp-toast--error   { border-left-color: #dc2626; }
                .gp-toast--error i { color: #dc2626; }
                .gp-toast__close {
                    position: absolute;
                    top: 4px;
                    right: 8px;
                    background: transparent;
                    border: 0;
                    font-size: 1.2rem;
                    line-height: 1;
                    color: #6b7280;
                    cursor: pointer;
                    padding: 4px 6px;
                    border-radius: 6px;
                }
                .gp-toast__close:hover { background: rgba(0, 0, 0, .06); color: #1a1208; }
                @media (max-width: 575.98px) {
                    .gp-toast { top: 72px; right: 12px; left: 12px; max-width: none; }
                }
            </style>
            <script>
                (function () {
                    var toast = document.querySelector('[data-gp-toast]');
                    if (!toast) return;
                    requestAnimationFrame(function () {
                        toast.setAttribute('data-shown', '1');
                    });
                    function dismiss() {
                        toast.removeAttribute('data-shown');
                        setTimeout(function () { toast.remove(); }, 300);
                    }
                    toast.querySelector('.gp-toast__close').addEventListener('click', dismiss);
                    setTimeout(dismiss, 3500); // auto-dismiss
                })();
            </script>
        @endif

        {{-- Full-width hero strip slot — sits above sidebar + content.
             Pages can fill it with @section('hero'). --}}
        @hasSection('hero')
            <div class="mt-3">
                @yield('hero')
            </div>
        @endif

        <div class="row g-4 mt-0">
            <!-- Sidebar (hidden on phones — hamburger menu replaces it there) -->
            <div class="col-lg-2 col-md-3 d-none d-md-block">
                <div class="admin-sidebar">
                    <div class="admin-sidebar-header">
                        <div class="icon-circle"><i class="fas fa-shield-halved"></i></div>
                        <h6>Admin Panel</h6>
                        <small>Manage platform</small>
                    </div>

                    <div class="admin-sidebar-label">Main</div>
                    <a href="{{ route('home') }}" class="admin-nav-item {{ request()->routeIs('home') ? 'active' : '' }}">
                        <i class="fas fa-chart-pie"></i> Dashboard
                    </a>

                    @if (auth()->user()->isAdmin())
                    <a href="{{ route('users.index') }}" class="admin-nav-item {{ request()->routeIs('users.*') ? 'active' : '' }}">
                        <i class="fas fa-users"></i> Users
                    </a>
                    <a href="{{ route('settings.edit') }}" class="admin-nav-item {{ request()->routeIs('settings.*') ? 'active' : '' }}">
                        <i class="fas fa-sliders"></i> Settings
                    </a>
                    @endif


                    <div class="admin-sidebar-label">Content</div>
                    <a href="{{ route('destinations.index') }}" class="admin-nav-item {{ request()->routeIs('destinations.*') ? 'active' : '' }}">
                        <i class="fas fa-map-marker-alt"></i> Destinations
                    </a>
                    <a href="{{ route('activities.index') }}" class="admin-nav-item {{ request()->routeIs('activities.*') ? 'active' : '' }}">
                        <i class="fas fa-hiking"></i> Activities
                    </a>
                    <a href="{{ route('tour-packages.index') }}" class="admin-nav-item {{ request()->routeIs('tour-packages.*') ? 'active' : '' }}">
                        <i class="fas fa-suitcase-rolling"></i> Packages
                    </a>
                    <a href="{{ route('accommodations.index') }}" class="admin-nav-item {{ request()->routeIs('accommodations.*') ? 'active' : '' }}">
                        <i class="fas fa-hotel"></i> Accommodations
                    </a>
                    @if (auth()->user()->isAdmin())
                    <a href="{{ route('accommodation-types.index') }}" class="admin-nav-item {{ request()->routeIs('accommodation-types.*') ? 'active' : '' }}">
                        <i class="fas fa-bed"></i> Accommodation Types
                    </a>
                    @endif
                    <a href="{{ route('categories.index') }}" class="admin-nav-item {{ request()->routeIs('categories.*') ? 'active' : '' }}">
                        <i class="fas fa-folder"></i> Categories
                    </a>
                    <a href="{{ route('blog.index') }}" class="admin-nav-item {{ request()->routeIs('blog.*') ? 'active' : '' }}">
                        <i class="fas fa-newspaper"></i> Blog Posts
                    </a>

                    <div class="admin-nav-divider"></div>
                    <a href="{{ route('trash.index') }}" class="admin-nav-item {{ request()->routeIs('trash.*') ? 'active' : '' }}">
                        <i class="fas fa-trash-can-arrow-up"></i> Trash
                    </a>

                    <div class="admin-nav-divider"></div>
                    <a href="{{ url('/') }}" class="admin-nav-item">
                        <i class="fas fa-arrow-left"></i> Back to Site
                    </a>
                </div>
            </div>

            <!-- Content -->
            <div class="col-lg-10 col-md-9 col-12">
                <div class="admin-content">
                    @yield('content')
                </div>
            </div>
        </div>
    </div>
    @else
        @yield('content')
    @endauth

    @if (session('status'))
    <div class="container-fluid px-4">
        <div class="admin-alert admin-alert-success alert alert-dismissible fade show mt-3">
            {{ session('status') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    </div>
    @endif

    <!-- Scripts -->
    <script src="{{ asset('js/bootstrap5.bundle.min.js') }}"></script>
    @yield('scripts')

    {{-- Wires drag-and-drop reordering on any table with [data-sortable-url].
         Loads SortableJS from CDN and only attaches when sortable containers
         are present, so it's a no-op on pages without lists. --}}
    @include('partials.sortable-tables')

    <script>
        // Client-side cap for [type=file] inputs with [data-max-files]. Gives
        // instant feedback so admins don't wait for the upload to fail
        // server-side. Server still re-validates — this is just UX.
        document.addEventListener('change', function (e) {
            var inp = e.target;
            if (inp.tagName !== 'INPUT' || inp.type !== 'file') return;
            var cap = parseInt(inp.dataset.maxFiles || '', 10);
            if (!cap || !inp.files) return;
            if (inp.files.length > cap) {
                inp.value = '';
                alert('You can attach at most ' + cap + ' photo' + (cap === 1 ? '' : 's') + ' here. ' +
                      'Please pick ' + cap + ' or fewer.');
            }
        });

        // Close any open Bootstrap navbar-collapse when the user taps outside
        // it (and outside its toggler). Without this the menu stays open
        // until you either tap a link or the toggler again, which feels
        // sticky on phones.
        document.addEventListener('click', function (e) {
            document.querySelectorAll('.navbar-collapse.show').forEach(function (menu) {
                if (menu.contains(e.target)) return;
                // The toggler that opens this menu — don't double-close it.
                var toggler = document.querySelector('[data-bs-target="#' + menu.id + '"]');
                if (toggler && toggler.contains(e.target)) return;

                var instance = bootstrap.Collapse.getInstance(menu)
                            || new bootstrap.Collapse(menu, { toggle: false });
                instance.hide();
            });
        });
    </script>
</body>

</html>