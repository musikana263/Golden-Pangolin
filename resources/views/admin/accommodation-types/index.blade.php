@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="mb-0" style="font-weight:600;">
        <i class="fas fa-bed me-2" style="color:var(--admin-primary)"></i>
        Accommodation Types
    </h5>
    <a href="{{ route('accommodation-types.create') }}" class="btn-admin-primary">
        <i class="fas fa-plus me-1"></i> New Type
    </a>
</div>

<div class="admin-card">
    <div class="admin-card-body">
        @if ($types->isEmpty())
            <p class="text-muted mb-0">No accommodation types yet. Create one to get started.</p>
        @else
            <table class="admin-table">
                <thead>
                    <tr>
                        <th class="gp-mobile-keep">Name</th>
                        <th>Icon</th>
                        <th>Description</th>
                        <th class="text-center">Used by</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($types as $type)
                        <tr>
                            <td class="gp-mobile-keep"><strong>{{ $type->name }}</strong><br><small class="text-muted">{{ $type->slug }}</small></td>
                            <td>@if ($type->icon)<i class="{{ $type->icon }}"></i> <code>{{ $type->icon }}</code>@endif</td>
                            <td class="text-muted">{{ $type->description }}</td>
                            <td class="text-center">{{ $type->accommodations_count }}</td>
                            <td class="text-end">
                                <a href="{{ route('accommodation-types.edit', $type) }}" class="btn-admin-sm btn-admin-edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('accommodation-types.destroy', $type) }}" method="POST" class="d-inline"
                                      onsubmit="return confirm('Delete this type?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn-admin-sm btn-admin-delete">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>
</div>
@endsection
