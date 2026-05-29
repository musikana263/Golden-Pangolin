@extends('layouts.front')

@section('seo')
    <x-seo title="Reset password" description="Set a new password for your Golden Pangolin account." />
@endsection

@section('page')
<div class="gp-auth">
    <div class="gp-auth__card" data-aos="fade-up">

        <a href="{{ url('/') }}" class="gp-auth__brand" aria-label="Golden Pangolin home">
            <span class="gp-auth__mark"><img src="{{ asset('images/logo.png') }}" alt=""></span>
            <span>Golden <span class="accent">Pangolin</span></span>
        </a>

        <div class="gp-auth__icon"><i class="fas fa-lock-open"></i></div>
        <h1 class="gp-auth__title">Reset password</h1>
        <p class="gp-auth__subtitle">Choose a new password for your account.</p>

        <form method="POST" action="{{ route('password.update') }}" novalidate>
            @csrf
            <input type="hidden" name="token" value="{{ $token }}">

            <div class="gp-auth__field">
                <label for="email">Email address</label>
                <div class="gp-auth__input-wrap">
                    <i class="fas fa-envelope gp-auth__input-icon" aria-hidden="true"></i>
                    <input id="email" type="email" name="email"
                           value="{{ $email ?? old('email') }}"
                           required autocomplete="email" autofocus
                           placeholder="you@example.com"
                           class="gp-auth__input @error('email') is-invalid @enderror">
                </div>
                @error('email')
                    <small class="gp-auth__error"><i class="fas fa-exclamation-circle"></i>{{ $message }}</small>
                @enderror
            </div>

            <div class="gp-auth__field">
                <label for="password">New password</label>
                <div class="gp-auth__input-wrap">
                    <i class="fas fa-lock gp-auth__input-icon" aria-hidden="true"></i>
                    <input id="password" type="password" name="password"
                           required autocomplete="new-password"
                           placeholder="At least 8 characters"
                           class="gp-auth__input @error('password') is-invalid @enderror">
                </div>
                @error('password')
                    <small class="gp-auth__error"><i class="fas fa-exclamation-circle"></i>{{ $message }}</small>
                @enderror
            </div>

            <div class="gp-auth__field">
                <label for="password-confirm">Confirm new password</label>
                <div class="gp-auth__input-wrap">
                    <i class="fas fa-lock gp-auth__input-icon" aria-hidden="true"></i>
                    <input id="password-confirm" type="password" name="password_confirmation"
                           required autocomplete="new-password"
                           placeholder="Type it again"
                           class="gp-auth__input">
                </div>
            </div>

            <button type="submit" class="gp-auth__submit">
                <i class="fas fa-check-circle"></i>
                <span>Reset password</span>
            </button>

            <a href="{{ route('login') }}" class="gp-auth__link">
                <i class="fas fa-arrow-left"></i> Back to sign in
            </a>
        </form>
    </div>
</div>

@include('auth.passwords._card-styles')
@endsection
