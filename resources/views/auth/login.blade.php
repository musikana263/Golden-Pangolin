@extends('layouts.front')

@section('seo')
    <x-seo title="Sign in" description="Sign in to the Golden Pangolin admin panel." />
@endsection

@section('page')
<div class="gp-login">

    {{-- Left panel — brand, story, no form. Hidden on small screens. --}}
    <aside class="gp-login__brand">
        <div class="gp-login__brand-inner">
            <a href="{{ url('/') }}" class="gp-login__brand-link" aria-label="Golden Pangolin home">
                <span class="gp-login__mark">
                    <img src="{{ asset('images/logo.png') }}" alt="">
                </span>
                <span class="gp-login__wordmark">
                    Golden <span class="gp-login__accent">Pangolin</span>
                </span>
            </a>

            <h1 class="gp-login__headline">
                Welcome back, <span class="gp-login__accent">explorer</span>.
            </h1>
            <p class="gp-login__lede">
                Sign in to manage destinations, packages, and the stories you share with travellers heading to Zimbabwe, Botswana and Zambia.
            </p>

            <ul class="gp-login__bullets">
                <li><i class="fas fa-check"></i> Curate destinations &amp; itineraries</li>
                <li><i class="fas fa-check"></i> Publish blog posts in minutes</li>
                <li><i class="fas fa-check"></i> Reply to enquiries on one dashboard</li>
            </ul>

            <p class="gp-login__legal">
                &copy; {{ date('Y') }} Golden Pangolin &middot;
                <a href="{{ url('/') }}">Back to site</a>
            </p>
        </div>
    </aside>

    {{-- Right panel — the actual sign-in card. --}}
    <main class="gp-login__panel">
        <div class="gp-login__card" data-aos="fade-up">

            <a href="{{ url('/') }}" class="gp-login__mobile-brand" aria-label="Golden Pangolin home">
                <span class="gp-login__mark gp-login__mark--small">
                    <img src="{{ asset('images/logo.png') }}" alt="">
                </span>
                <span>Golden <span class="gp-login__accent">Pangolin</span></span>
            </a>

            <h2 class="gp-login__title">Sign in</h2>
            <p class="gp-login__subtitle">Use the email and password your admin set up for you.</p>

            <form method="POST" action="{{ route('login') }}" class="gp-login__form" novalidate>
                @csrf

                <div class="gp-login__field">
                    <label for="email">Email address</label>
                    <div class="gp-login__input-wrap">
                        <i class="fas fa-envelope" aria-hidden="true"></i>
                        <input id="email" type="email" name="email"
                               value="{{ old('email') }}" placeholder="you@example.com"
                               required autofocus autocomplete="email"
                               class="@error('email') is-invalid @enderror">
                    </div>
                    @error('email')
                        <small class="gp-login__error"><i class="fas fa-exclamation-circle"></i>{{ $message }}</small>
                    @enderror
                </div>

                <div class="gp-login__field">
                    <label for="password" class="d-flex justify-content-between align-items-baseline">
                        <span>Password</span>
                        @if (Route::has('password.request'))
                            <a class="gp-login__forgot" href="{{ route('password.request') }}">Forgot?</a>
                        @endif
                    </label>
                    <div class="gp-login__input-wrap">
                        <i class="fas fa-lock" aria-hidden="true"></i>
                        <input id="password" type="password" name="password"
                               placeholder="••••••••" required autocomplete="current-password"
                               class="@error('password') is-invalid @enderror">
                        <button type="button" class="gp-login__peek" data-peek="password"
                                aria-label="Show password" tabindex="-1">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                    @error('password')
                        <small class="gp-login__error"><i class="fas fa-exclamation-circle"></i>{{ $message }}</small>
                    @enderror
                </div>

                <label class="gp-login__remember">
                    <input type="checkbox" name="remember" {{ old('remember') ? 'checked' : '' }}>
                    <span>Keep me signed in on this device</span>
                </label>

                <button type="submit" class="gp-login__submit">
                    <span>Sign in</span>
                    <i class="fas fa-arrow-right"></i>
                </button>

                <p class="gp-login__help">
                    Don't have an account? <a href="{{ route('contact') }}">Ask an admin</a>
                </p>
            </form>
        </div>
    </main>
</div>

<style>
    .gp-login {
        position: relative;
        min-height: 100vh;
        display: grid;
        grid-template-columns: 1fr 1fr;
    }

    /* ----- left brand panel ----- */
    .gp-login__brand {
        position: relative;
        overflow: hidden;
        color: #fbf3e1;
        background:
            radial-gradient(120% 80% at 20% 10%, rgba(212, 160, 60, 0.35), transparent 60%),
            radial-gradient(120% 80% at 90% 90%, rgba(232, 90, 90, 0.20), transparent 60%),
            linear-gradient(135deg, #1a1208 0%, #2a1c10 55%, #0c0c10 100%);
    }
    .gp-login__brand::before {
        content: "";
        position: absolute;
        inset: 0;
        background: url('{{ asset("images/background.svg") }}') center / 520px auto repeat;
        opacity: .07;
        pointer-events: none;
    }
    .gp-login__brand-inner {
        position: relative;
        height: 100%;
        padding: 4rem 4rem 2rem;
        display: flex;
        flex-direction: column;
        max-width: 560px;
    }
    .gp-login__brand-link {
        display: inline-flex;
        align-items: center;
        gap: .85rem;
        text-decoration: none;
        color: inherit;
        margin-bottom: 4rem;
    }
    .gp-login__mark {
        width: 64px;
        height: 64px;
        border-radius: 50%;
        background: radial-gradient(circle at 50% 45%, #fbf3e1 0%, #f3d99a 70%, #d4a03c 100%);
        box-shadow: 0 0 0 1px rgba(243, 199, 121, .45), 0 0 18px 2px rgba(212, 160, 60, .35);
        display: inline-flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }
    .gp-login__mark img { height: 56px; width: auto; display: block; }
    .gp-login__mark--small { width: 44px; height: 44px; }
    .gp-login__mark--small img { height: 38px; }

    .gp-login__wordmark {
        font-family: 'Playfair Display', Georgia, serif;
        font-size: 1.6rem;
        font-weight: 700;
        letter-spacing: -.01em;
    }
    .gp-login__accent { color: #f3c779; }

    .gp-login__headline {
        font-family: 'Playfair Display', Georgia, serif;
        font-size: clamp(2rem, 3.2vw, 2.85rem);
        line-height: 1.1;
        margin-bottom: 1rem;
    }
    .gp-login__lede {
        font-size: 1.02rem;
        line-height: 1.6;
        color: rgba(251, 243, 225, .82);
        max-width: 40ch;
        margin-bottom: 2rem;
    }
    .gp-login__bullets {
        list-style: none;
        padding: 0;
        margin: 0 0 auto;
        display: flex;
        flex-direction: column;
        gap: .65rem;
    }
    .gp-login__bullets li {
        display: flex;
        align-items: center;
        gap: .65rem;
        color: rgba(251, 243, 225, .92);
        font-size: .98rem;
    }
    .gp-login__bullets i {
        color: #f3c779;
        background: rgba(243, 199, 121, .12);
        width: 26px;
        height: 26px;
        border-radius: 50%;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: .75rem;
    }
    .gp-login__legal {
        margin-top: 2.5rem;
        font-size: .82rem;
        color: rgba(251, 243, 225, .55);
    }
    .gp-login__legal a {
        color: #f3c779;
        text-decoration: none;
    }
    .gp-login__legal a:hover { text-decoration: underline; }

    /* ----- right form panel ----- */
    .gp-login__panel {
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 5.5rem 2rem 3rem;
        background:
            radial-gradient(80% 60% at 50% 0%, rgba(212, 160, 60, .06), transparent 70%),
            #fbf3e1;
    }
    html[data-theme="dark"] .gp-login__panel {
        background:
            radial-gradient(80% 60% at 50% 0%, rgba(212, 160, 60, .12), transparent 70%),
            #0c0c10;
    }

    .gp-login__card {
        width: 100%;
        max-width: 420px;
        background: rgba(255, 255, 255, .85);
        backdrop-filter: blur(14px);
        -webkit-backdrop-filter: blur(14px);
        border: 1px solid rgba(212, 160, 60, .25);
        border-radius: 18px;
        padding: 2.25rem 2rem;
        box-shadow:
            0 20px 60px rgba(26, 18, 8, .12),
            0 4px 14px rgba(26, 18, 8, .06);
    }
    html[data-theme="dark"] .gp-login__card {
        background: rgba(20, 18, 14, .72);
        border-color: rgba(212, 160, 60, .35);
        box-shadow:
            0 20px 60px rgba(0, 0, 0, .55),
            0 4px 14px rgba(0, 0, 0, .35);
    }

    .gp-login__mobile-brand {
        display: none;
        align-items: center;
        gap: .65rem;
        color: var(--tt-dark);
        font-family: 'Playfair Display', Georgia, serif;
        font-size: 1.35rem;
        font-weight: 700;
        text-decoration: none;
        margin-bottom: 1.5rem;
    }
    html[data-theme="dark"] .gp-login__mobile-brand { color: #f5f5f5; }

    .gp-login__title {
        font-family: 'Playfair Display', Georgia, serif;
        font-size: 1.85rem;
        font-weight: 700;
        margin-bottom: .35rem;
        color: var(--tt-dark);
    }
    html[data-theme="dark"] .gp-login__title { color: #fbf3e1; }
    .gp-login__subtitle {
        color: var(--tt-text-muted);
        font-size: .92rem;
        margin-bottom: 1.75rem;
    }
    html[data-theme="dark"] .gp-login__subtitle { color: rgba(251, 243, 225, .65); }

    .gp-login__field { margin-bottom: 1.1rem; }
    .gp-login__field label {
        display: block;
        font-size: .82rem;
        font-weight: 600;
        margin-bottom: .4rem;
        color: var(--tt-dark);
        letter-spacing: .01em;
    }
    html[data-theme="dark"] .gp-login__field label { color: #f3eadb; }

    .gp-login__input-wrap {
        position: relative;
        display: flex;
        align-items: center;
    }
    .gp-login__input-wrap > i {
        position: absolute;
        left: 14px;
        color: var(--tt-text-muted);
        font-size: .92rem;
        pointer-events: none;
    }
    .gp-login__input-wrap input {
        width: 100%;
        padding: .75rem .85rem .75rem 2.4rem;
        background: #fff;
        border: 1px solid rgba(0, 0, 0, .12);
        border-radius: 10px;
        font-size: .98rem;
        color: var(--tt-dark);
        transition: border-color .15s ease, box-shadow .15s ease, background .15s ease;
    }
    .gp-login__input-wrap input::placeholder { color: rgba(0, 0, 0, .35); }
    .gp-login__input-wrap input:focus {
        outline: none;
        border-color: var(--tt-accent);
        box-shadow: 0 0 0 4px rgba(212, 160, 60, .15);
    }
    .gp-login__input-wrap input.is-invalid {
        border-color: #d9534f;
        box-shadow: 0 0 0 4px rgba(217, 83, 79, .12);
    }
    html[data-theme="dark"] .gp-login__input-wrap input {
        background: rgba(12, 12, 16, .6);
        border-color: rgba(212, 160, 60, .25);
        color: #fbf3e1;
    }
    html[data-theme="dark"] .gp-login__input-wrap input::placeholder { color: rgba(251, 243, 225, .35); }
    html[data-theme="dark"] .gp-login__input-wrap > i { color: rgba(251, 243, 225, .55); }

    .gp-login__peek {
        position: absolute;
        right: 6px;
        background: transparent;
        border: 0;
        padding: 6px 10px;
        color: var(--tt-text-muted);
        cursor: pointer;
        border-radius: 6px;
    }
    .gp-login__peek:hover { background: rgba(0, 0, 0, .04); color: var(--tt-dark); }
    html[data-theme="dark"] .gp-login__peek:hover { background: rgba(255, 255, 255, .06); color: #fbf3e1; }

    .gp-login__error {
        display: inline-flex;
        align-items: center;
        gap: .35rem;
        color: #d9534f;
        font-size: .8rem;
        margin-top: .35rem;
    }

    .gp-login__forgot {
        font-size: .78rem;
        font-weight: 500;
        color: var(--tt-accent);
        text-decoration: none;
    }
    .gp-login__forgot:hover { text-decoration: underline; }

    .gp-login__remember {
        display: flex;
        align-items: center;
        gap: .55rem;
        font-size: .88rem;
        color: var(--tt-text-muted);
        margin: .25rem 0 1.5rem;
        cursor: pointer;
        user-select: none;
    }
    html[data-theme="dark"] .gp-login__remember { color: rgba(251, 243, 225, .7); }
    .gp-login__remember input { accent-color: var(--tt-accent); }

    .gp-login__submit {
        width: 100%;
        background: linear-gradient(135deg, #d4a03c, #b8872e);
        color: #1a1208;
        border: 0;
        border-radius: 10px;
        padding: .85rem 1rem;
        font-size: 1rem;
        font-weight: 700;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: .55rem;
        cursor: pointer;
        box-shadow: 0 10px 24px rgba(184, 135, 46, .35);
        transition: transform .12s ease, box-shadow .15s ease, filter .15s ease;
    }
    .gp-login__submit:hover {
        transform: translateY(-1px);
        filter: brightness(1.05);
        box-shadow: 0 14px 30px rgba(184, 135, 46, .42);
    }
    .gp-login__submit:active { transform: translateY(0); }

    .gp-login__help {
        margin-top: 1.5rem;
        text-align: center;
        font-size: .85rem;
        color: var(--tt-text-muted);
    }
    .gp-login__help a { color: var(--tt-accent); text-decoration: none; font-weight: 600; }
    .gp-login__help a:hover { text-decoration: underline; }
    html[data-theme="dark"] .gp-login__help { color: rgba(251, 243, 225, .6); }

    /* ----- responsive ----- */
    @media (max-width: 960px) {
        .gp-login { grid-template-columns: 1fr; }
        .gp-login__brand { display: none; }
        .gp-login__mobile-brand { display: inline-flex; }
        .gp-login__panel { padding: 6rem 1.25rem 2rem; }
    }
</style>

<script>
    document.addEventListener('click', function (e) {
        var btn = e.target.closest('.gp-login__peek');
        if (!btn) return;
        var input = document.getElementById(btn.dataset.peek);
        if (!input) return;
        var showing = input.type === 'text';
        input.type = showing ? 'password' : 'text';
        var i = btn.querySelector('i');
        if (i) i.className = showing ? 'fas fa-eye' : 'fas fa-eye-slash';
        btn.setAttribute('aria-label', showing ? 'Show password' : 'Hide password');
    });
</script>
@endsection
