@extends('layouts.front')

@section('seo')
    <x-seo title="Forgot password" description="Reset your Golden Pangolin password." />
@endsection

@section('page')
<div class="gp-auth">
    <div class="gp-auth__card" data-aos="fade-up">

        <a href="{{ url('/') }}" class="gp-auth__brand" aria-label="Golden Pangolin home">
            <span class="gp-auth__mark"><img src="{{ asset('images/logo.png') }}" alt=""></span>
            <span>Golden <span class="accent">Pangolin</span></span>
        </a>

        <div class="gp-auth__icon"><i class="fas fa-key"></i></div>
        <h1 class="gp-auth__title">Forgot password?</h1>
        <p class="gp-auth__subtitle">Enter your email and we'll send you reset instructions.</p>

        @if (session('status'))
            <div class="gp-auth__status">
                <i class="fas fa-check-circle"></i>
                <span>{{ session('status') }}</span>
            </div>
        @endif

        <form method="POST" action="{{ route('password.email') }}" novalidate>
            @csrf

            <div class="gp-auth__field">
                <label for="email">Email address</label>
                <div class="gp-auth__input-wrap">
                    <i class="fas fa-envelope gp-auth__input-icon" aria-hidden="true"></i>
                    <input id="email" type="email" name="email"
                           value="{{ old('email') }}"
                           required autocomplete="email" autofocus
                           placeholder="you@example.com"
                           class="gp-auth__input @error('email') is-invalid @enderror">
                </div>
                @error('email')
                    <small class="gp-auth__error"><i class="fas fa-exclamation-circle"></i>{{ $message }}</small>
                @enderror
            </div>

            <button type="submit" class="gp-auth__submit">
                <i class="fas fa-paper-plane"></i>
                <span>Send reset link</span>
            </button>

            <a href="{{ route('login') }}" class="gp-auth__link">
                <i class="fas fa-arrow-left"></i> Back to sign in
            </a>
        </form>
    </div>
</div>

@include('auth.passwords._card-styles')
@endsection
