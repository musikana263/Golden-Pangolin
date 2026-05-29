@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="mb-0" style="font-weight:600;">
        <i class="fas fa-users me-2" style="color:var(--admin-primary)"></i>Users
    </h5>
    <a href="{{ route('users.create') }}" class="btn btn-primary btn-sm">
        <i class="fas fa-user-plus me-1"></i> Add user
    </a>
</div>

<div class="admin-card">
    <div class="admin-card-body p-0">
        @if ($users->count() > 0)
        <table class="admin-table">
            <thead>
                <tr>
                    <th>Avatar</th>
                    <th class="gp-mobile-keep">Name</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($users as $user)
                <tr>
                    <td>
                        <img class="admin-user-thumb" src="{{ Gravatar::get($user->email) }}" alt="{{ $user->name }}">
                    </td>
                    <td class="gp-mobile-keep">
                        <strong>{{ $user->name }}</strong>
                        @if ($user->id === auth()->id())
                            <small class="text-muted ms-1">(you)</small>
                        @endif
                    </td>
                    <td>{{ $user->email }}</td>
                    <td>
                        @if ($user->isAdmin())
                            <span class="badge bg-warning text-dark" style="background:linear-gradient(135deg,#d4a03c,#b8872e) !important;">
                                <i class="fas fa-shield-halved me-1"></i> Admin
                            </span>
                        @else
                            <span class="text-muted">Employee</span>
                        @endif
                    </td>
                    <td class="text-end">
                        <div class="d-flex gap-1 justify-content-end">
                            <a href="{{ route('users.edit', $user) }}" class="btn-admin-sm btn-admin-edit" title="Edit user">
                                <i class="fas fa-pen"></i>
                            </a>
                            @if ($user->id !== auth()->id())
                                <form action="{{ route('users.destroy', $user) }}" method="POST"
                                      onsubmit="return confirm('Delete {{ $user->name }}? This can\'t be undone.');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn-admin-sm btn-admin-delete" title="Delete user">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @else
        <div class="admin-empty">
            <i class="fas fa-users"></i>
            <h5>No Users Yet</h5>
        </div>
        @endif
    </div>
</div>
@endsection
