@extends('layouts.app')

@section('content')
<div class="row justify-content-center">
    <div class="col-12 col-md-10 col-lg-8 col-xl-7">

        <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="mb-0" style="font-weight:600;">
                <i class="fas fa-user-circle me-2" style="color:var(--admin-primary)"></i>My profile
            </h5>
        </div>

        @php $isAdmin = $user->isAdmin(); @endphp

        {{-- Profile details — admin can edit, everyone else just sees them. --}}
        <div class="admin-card mb-3">
            <div class="admin-card-header">
                <h5><i class="fas fa-id-card me-2"></i> Details</h5>
                @if (! $isAdmin)
                    <span class="text-muted small"><i class="fas fa-lock me-1"></i> View only</span>
                @endif
            </div>
            <div class="admin-card-body">
                @include('partials.errors')

                @if ($isAdmin)
                    <form method="POST" action="{{ route('users.update-profile') }}">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label for="name" class="form-label">Full name</label>
                            <input type="text" id="name" name="name" value="{{ old('name', $user->name) }}"
                                   class="form-control" maxlength="120" required>
                        </div>

                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" id="email" name="email" value="{{ old('email', $user->email) }}"
                                   class="form-control" maxlength="180" required>
                        </div>

                        <div class="mb-3">
                            <label for="about" class="form-label">About <span class="text-muted">(optional)</span></label>
                            <textarea id="about" name="about" class="form-control" rows="3" maxlength="1000">{{ old('about', $user->about) }}</textarea>
                        </div>

                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-1"></i> Save details
                        </button>
                    </form>
                @else
                    <dl class="row mb-0">
                        <dt class="col-sm-3">Full name</dt>
                        <dd class="col-sm-9">{{ $user->name }}</dd>

                        <dt class="col-sm-3">Email</dt>
                        <dd class="col-sm-9">{{ $user->email }}</dd>

                        <dt class="col-sm-3">Role</dt>
                        <dd class="col-sm-9">
                            <span class="badge bg-secondary">Employee</span>
                        </dd>

                        @if ($user->about)
                            <dt class="col-sm-3">About</dt>
                            <dd class="col-sm-9">{{ $user->about }}</dd>
                        @endif
                    </dl>
                    <small class="text-muted d-block mt-2">
                        Only admins can edit profile details. Ask an admin if anything here needs updating.
                    </small>
                @endif
            </div>
        </div>

        {{-- Password change — anyone can update their own password. --}}
        <div class="admin-card">
            <div class="admin-card-header">
                <h5><i class="fas fa-key me-2"></i> Change password</h5>
            </div>
            <div class="admin-card-body">
                <form method="POST" action="{{ route('users.change-password') }}" autocomplete="off">
                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <label for="current_password" class="form-label">Current password</label>
                        <input type="password" id="current_password" name="current_password"
                               class="form-control @error('current_password') is-invalid @enderror" required>
                        @error('current_password')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="password" class="form-label">New password</label>
                            <input type="password" id="password" name="password"
                                   class="form-control @error('password') is-invalid @enderror"
                                   minlength="8" required>
                            <small class="text-muted">Min 8 chars, mixed case, at least one number.</small>
                            @error('password')
                                <small class="text-danger d-block mt-1">{{ $message }}</small>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="password_confirmation" class="form-label">Confirm new password</label>
                            <input type="password" id="password_confirmation" name="password_confirmation"
                                   class="form-control" minlength="8" required>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-key me-1"></i> Update password
                    </button>
                </form>
            </div>
        </div>

    </div>
</div>
@endsection
