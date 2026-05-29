@extends('layouts.front')

@section('seo')
    <x-seo title="Confirm password" description="Confirm your password to continue." />
@endsection

@section('page')
<div class="gp-auth">
    <div class="gp-auth__card" data-aos="fade-up">

        <a href="{{ url('/') }}" class="gp-auth__brand" aria-label="Golden Pangolin home">
            <span class="gp-auth__mark"><img src="{{ asset('images/logo.png') }}" alt=""></span>
            <span>Golden <span class="accent">Pangolin</span></span>
        </a>

        <div class="gp-auth__icon"><i class="fas fa-shield-halved"></i></div>
        <h1 class="gp-auth__title">Confirm password</h1>
        <p class="gp-auth__subtitle">For your security, please confirm your password before continuing.</p>

        <form method="POST" action="{{ route('password.confirm') }}" novalidate>
            @csrf

            <div class="gp-auth__field">
                <label for="password">Password</label>
                <div class="gp-auth__input-wrap">
                    <i class="fas fa-lock gp-auth__input-icon" aria-hidden="true"></i>
                    <input id="password" type="password" name="password"
                           required autocomplete="current-password" autofocus
                           placeholder="Your account password"
                           class="gp-auth__input @error('password') is-invalid @enderror">
                </div>
                @error('password')
                    <small class="gp-auth__error"><i class="fas fa-exclamation-circle"></i>{{ $message }}</small>
                @enderror
            </div>

            <button type="submit" class="gp-auth__submit">
                <i class="fas fa-check"></i>
                <span>Confirm password</span>
            </button>

            @if (Route::has('password.request'))
                <a href="{{ route('password.request') }}" class="gp-auth__link">
                    <i class="fas fa-question-circle"></i> Forgot your password?
                </a>
            @endif
        </form>
    </div>
</div>

@include('auth.passwords._card-styles')
@endsection
