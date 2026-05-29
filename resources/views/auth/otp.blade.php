@extends('layouts.front')

@section('seo')
    <x-seo title="Verify it's you" description="Enter the 6-digit code we sent to your email to finish signing in." />
@endsection

@section('page')
<div class="gp-otp-wrap">
    <div class="gp-otp-card" data-aos="fade-up">

        <a href="{{ url('/') }}" class="gp-otp-brand" aria-label="Golden Pangolin home">
            <img src="{{ asset('images/logo.png') }}" alt="">
        </a>

        <h1 class="gp-otp-title">Verify it's you</h1>
        <p class="gp-otp-sub">
            We sent a 6-digit code to<br>
            <strong>{{ $maskedEmail }}</strong>
        </p>

        @if (session('status'))
            <div class="gp-otp-status">{{ session('status') }}</div>
        @endif

        <form method="POST" action="{{ route('otp.verify') }}" class="gp-otp-form" autocomplete="off">
            @csrf

            <input id="code" name="code" type="text" inputmode="numeric" pattern="[0-9]{6}"
                   maxlength="6" autocomplete="one-time-code" required autofocus
                   placeholder="••••••"
                   class="gp-otp-input @error('code') is-invalid @enderror">

            @error('code')
                <div class="gp-otp-error">{{ $message }}</div>
            @enderror

            <button type="submit" class="gp-otp-submit">Continue</button>

            <div class="gp-otp-foot">
                <button type="submit" formaction="{{ route('otp.resend') }}" formnovalidate class="gp-otp-textbtn">
                    Resend code
                </button>
                <button type="submit" formaction="{{ route('otp.cancel') }}" formnovalidate class="gp-otp-textbtn gp-otp-textbtn--muted">
                    Cancel
                </button>
            </div>
        </form>

        <p class="gp-otp-expires">Codes expire in 10 minutes.</p>
    </div>
</div>

<style>
    .gp-otp-wrap {
        min-height: 100vh;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 24px 16px;
        background:
            radial-gradient(60% 50% at 50% -10%, rgba(212, 160, 60, .14), transparent 70%),
            #0c0c10;
    }

    .gp-otp-card {
        width: 100%;
        max-width: 380px;
        background: rgba(20, 18, 14, .9);
        border: 1px solid rgba(212, 160, 60, .25);
        border-radius: 18px;
        padding: 36px 32px 28px;
        text-align: center;
        backdrop-filter: blur(12px);
        -webkit-backdrop-filter: blur(12px);
        box-shadow: 0 30px 80px rgba(0, 0, 0, .5);
    }

    .gp-otp-brand {
        display: inline-block;
        margin-bottom: 20px;
    }
    .gp-otp-brand img {
        height: 48px;
        width: auto;
        filter:
            drop-shadow(0 0 1px rgba(255, 230, 170, .95))
            drop-shadow(0 0 8px rgba(212, 160, 60, .55));
    }

    .gp-otp-title,
    html[data-theme="dark"] .gp-otp-title,
    html:not([data-theme="dark"]) .gp-otp-title {
        font-family: 'Playfair Display', Georgia, serif;
        font-size: 1.55rem;
        font-weight: 700;
        color: #ffffff !important;
        text-shadow: none !important;
        -webkit-text-fill-color: #ffffff !important;
        background: none !important;
        margin: 0 0 6px;
    }
    .gp-otp-sub {
        font-size: .88rem;
        line-height: 1.45;
        color: rgba(251, 243, 225, .65);
        margin: 0 0 24px;
    }
    .gp-otp-sub strong { color: #f3c779; font-weight: 600; }

    .gp-otp-status {
        background: rgba(37, 211, 102, .12);
        border: 1px solid rgba(37, 211, 102, .35);
        color: #6ff09a;
        padding: 8px 12px;
        border-radius: 10px;
        font-size: .82rem;
        margin-bottom: 14px;
    }

    .gp-otp-input {
        display: block;
        width: 100%;
        background: rgba(12, 12, 16, .8);
        border: 1.5px solid rgba(212, 160, 60, .35);
        border-radius: 12px;
        padding: 14px 16px;
        font-family: 'Courier New', monospace;
        font-size: 1.9rem;
        font-weight: 700;
        letter-spacing: .55rem;
        text-align: center;
        color: #fbf3e1;
        transition: border-color .15s ease, box-shadow .15s ease, background .15s ease;
    }
    .gp-otp-input::placeholder { color: rgba(251, 243, 225, .25); letter-spacing: .35rem; }
    .gp-otp-input:focus {
        outline: none;
        border-color: #d4a03c;
        background: rgba(12, 12, 16, 1);
        box-shadow: 0 0 0 4px rgba(212, 160, 60, .18);
    }
    .gp-otp-input.is-invalid {
        border-color: #e85a5a;
        box-shadow: 0 0 0 4px rgba(232, 90, 90, .12);
    }

    .gp-otp-error {
        color: #f5a5a3;
        font-size: .82rem;
        margin: 8px 2px 0;
        text-align: left;
    }

    .gp-otp-submit {
        margin-top: 18px;
        display: block;
        width: 100%;
        background: linear-gradient(135deg, #d4a03c, #b8872e);
        color: #1a1208;
        border: 0;
        border-radius: 12px;
        padding: 13px 16px;
        font-weight: 700;
        font-size: 1rem;
        cursor: pointer;
        transition: filter .12s ease, transform .12s ease, box-shadow .15s ease;
        box-shadow: 0 8px 20px rgba(184, 135, 46, .35);
    }
    .gp-otp-submit:hover { filter: brightness(1.06); transform: translateY(-1px); }

    .gp-otp-foot {
        display: flex;
        justify-content: center;
        gap: 16px;
        margin-top: 18px;
    }
    .gp-otp-textbtn {
        background: transparent;
        border: 0;
        color: #f3c779;
        font-size: .85rem;
        font-weight: 600;
        cursor: pointer;
        padding: 4px;
    }
    .gp-otp-textbtn:hover { text-decoration: underline; }
    .gp-otp-textbtn--muted { color: rgba(251, 243, 225, .55); }

    .gp-otp-expires {
        margin: 20px 0 0;
        font-size: .76rem;
        color: rgba(251, 243, 225, .40);
        letter-spacing: .04em;
    }
</style>
@endsection
