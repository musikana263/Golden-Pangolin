@extends('layouts.app')

@section('content')
<div class="row justify-content-center">
    <div class="col-12 col-md-10 col-lg-7 col-xl-6">

        <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="mb-0" style="font-weight:600;">
                <i class="fas fa-user-plus me-2" style="color:var(--admin-primary)"></i>Add user
            </h5>
            <a href="{{ route('users.index') }}" class="btn btn-light btn-sm">
                <i class="fas fa-arrow-left me-1"></i> Back
            </a>
        </div>

        <div class="admin-card">
            <div class="admin-card-body">
                @include('partials.errors')

                <form method="POST" action="{{ route('users.store') }}" autocomplete="off">
                    @csrf

                    <div class="mb-3">
                        <label for="name" class="form-label">Full name</label>
                        <input type="text" name="name" id="name" value="{{ old('name') }}"
                               class="form-control" maxlength="120" required autofocus>
                    </div>

                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" name="email" id="email" value="{{ old('email') }}"
                               class="form-control" maxlength="180" required>
                    </div>

                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" name="password" id="password"
                               class="form-control" minlength="8" required>
                        <small class="text-muted">Minimum 8 characters, mixed case, at least one number.</small>
                    </div>

                    <div class="form-check mb-4">
                        <input type="checkbox" name="is_admin" id="is_admin" value="1"
                               class="form-check-input" {{ old('is_admin') ? 'checked' : '' }}>
                        <label for="is_admin" class="form-check-label">Grant admin access</label>
                        <div class="form-text">Admins are the only users who can add or promote other users.</div>
                    </div>

                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-user-plus me-1"></i> Create user
                    </button>
                </form>
            </div>
        </div>

    </div>
</div>
@endsection
