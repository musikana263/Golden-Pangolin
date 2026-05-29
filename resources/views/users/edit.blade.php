@extends('layouts.app')

@section('content')
<div class="row justify-content-center">
    <div class="col-12 col-md-10 col-lg-7 col-xl-6">

        <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="mb-0" style="font-weight:600;">
                <i class="fas fa-user-pen me-2" style="color:var(--admin-primary)"></i>Edit user
            </h5>
            <a href="{{ route('users.index') }}" class="btn btn-light btn-sm">
                <i class="fas fa-arrow-left me-1"></i> Back
            </a>
        </div>

        <div class="admin-card">
            <div class="admin-card-body">
                @include('partials.errors')

                @if (session('error'))
                    <div class="alert alert-warning">{{ session('error') }}</div>
                @endif

                <form method="POST" action="{{ route('users.update', $user) }}" autocomplete="off">
                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <label for="name" class="form-label">Full name</label>
                        <input type="text" name="name" id="name" value="{{ old('name', $user->name) }}"
                               class="form-control" maxlength="120" required autofocus>
                    </div>

                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" name="email" id="email" value="{{ old('email', $user->email) }}"
                               class="form-control" maxlength="180" required>
                    </div>

                    <hr class="my-4">

                    <div class="mb-3">
                        <label for="password" class="form-label">
                            Reset password <span class="text-muted">(optional)</span>
                        </label>
                        <input type="password" name="password" id="password"
                               class="form-control" minlength="8" autocomplete="new-password"
                               placeholder="Leave blank to keep the current password">
                        <small class="text-muted">
                            Fill this only if you want to force a new password. Minimum 8 characters,
                            mixed case, at least one number.
                        </small>
                    </div>

                    <div class="form-check mb-4">
                        <input type="checkbox" name="is_admin" id="is_admin" value="1"
                               class="form-check-input"
                               @checked(old('is_admin', $user->isAdmin()))
                               @disabled($user->id === auth()->id())>
                        <label for="is_admin" class="form-check-label">Grant admin access</label>
                        <div class="form-text">
                            @if ($user->id === auth()->id())
                                You can't change your own admin status here.
                            @else
                                Admins can add, edit, and delete other users. Regular users can manage content but can't touch user accounts.
                            @endif
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-1"></i> Save changes
                    </button>
                </form>

                @if ($user->id !== auth()->id())
                    <hr class="my-4">
                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                        <div>
                            <strong class="d-block">Danger zone</strong>
                            <small class="text-muted">Deletes this account permanently. Their wishlist / content stays.</small>
                        </div>
                        <form method="POST" action="{{ route('users.destroy', $user) }}"
                              onsubmit="return confirm('Delete {{ $user->name }}? This can\'t be undone.');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn-admin-sm btn-admin-delete">
                                <i class="fas fa-trash me-1"></i> Delete user
                            </button>
                        </form>
                    </div>
                @endif
            </div>
        </div>

    </div>
</div>
@endsection
