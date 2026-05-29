@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="mb-0" style="font-weight:600;">
        <i class="fas fa-suitcase-rolling me-2" style="color:var(--admin-primary)"></i>
        Packages
    </h5>
    <a href="{{ route('tour-packages.create') }}" class="btn-admin-primary">
        <i class="fas fa-plus me-1"></i> New Package
    </a>
</div>

<div class="admin-card">
    <div class="admin-card-body">
        @if ($packages->isEmpty())
            <p class="text-muted mb-0">No packages yet. <a href="{{ route('tour-packages.create') }}">Create one</a>.</p>
        @else
            <table class="admin-table">
                <thead>
                    <tr>
                        <th class="gp-mobile-keep" style="width:32px;"></th>
                        <th class="d-none d-md-table-cell"></th>
                        <th class="gp-mobile-keep">Name</th>
                        <th class="d-none d-md-table-cell">Country</th>
                        <th>Activities</th>
                        <th>Duration</th>
                        <th>Price</th>
                        <th class="d-none d-md-table-cell text-center">Featured</th>
                        <th class="d-none d-md-table-cell text-center">Active</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody data-sortable-url="{{ route('admin.sort', 'packages') }}">
                    @foreach ($packages as $package)
                        <tr data-id="{{ $package->id }}">
                            <td class="drag-handle" title="Drag to reorder"><i class="fas fa-grip-vertical"></i></td>
                            <td class="d-none d-md-table-cell" style="width:80px;">
                                @if ($package->cover_url)
                                    <img src="{{ $package->cover_url }}" alt=""
                                         style="width:64px; height:48px; object-fit:cover; border-radius:6px;">
                                @else
                                    <span class="text-muted"><i class="fas fa-image"></i></span>
                                @endif
                            </td>
                            <td class="gp-mobile-keep">
                                <strong>{{ $package->name }}</strong><br>
                                <small class="text-muted">{{ Str::limit($package->summary, 60) }}</small>
                            </td>
                            <td class="d-none d-md-table-cell">{{ $package->country }}</td>
                            <td>
                                @foreach ($package->activities->take(4) as $a)
                                    <span class="badge bg-light text-dark border">{{ $a->name }}</span>
                                @endforeach
                                @if ($package->activities->count() > 4)
                                    <small class="text-muted">+{{ $package->activities->count() - 4 }}</small>
                                @endif
                            </td>
                            <td>{{ $package->duration ?: '—' }}</td>
                            <td>{{ $package->price_from ?: '—' }}</td>
                            <td class="d-none d-md-table-cell text-center">
                                @if ($package->is_featured)
                                    <i class="fas fa-star text-warning"></i>
                                @endif
                            </td>
                            <td class="d-none d-md-table-cell text-center">
                                @if ($package->is_active)
                                    <span class="badge bg-success">Yes</span>
                                @else
                                    <span class="badge bg-secondary">No</span>
                                @endif
                            </td>
                            <td class="text-end">
                                <a href="{{ route('tour-packages.edit', $package) }}" class="btn-admin-sm btn-admin-edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('tour-packages.destroy', $package) }}" method="POST" class="d-inline"
                                      onsubmit="return confirm('Delete this package?');">
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

            <div class="mt-3">{{ $packages->links('pagination::bootstrap-4') }}</div>
        @endif
    </div>
</div>
@endsection
