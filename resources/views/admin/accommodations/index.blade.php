@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="mb-0" style="font-weight:600;">
        <i class="fas fa-hotel me-2" style="color:var(--admin-primary)"></i>
        Accommodations
    </h5>
    <a href="{{ route('accommodations.create') }}" class="btn-admin-primary">
        <i class="fas fa-plus me-1"></i> New Accommodation
    </a>
</div>

<div class="admin-card">
    <div class="admin-card-body">
        @if ($accommodations->isEmpty())
            <p class="text-muted mb-0">No accommodations yet. <a href="{{ route('accommodations.create') }}">Add one</a>.</p>
        @else
            <table class="admin-table">
                <thead>
                    <tr>
                        <th class="gp-mobile-keep" style="width:32px;"></th>
                        <th></th>
                        <th class="gp-mobile-keep">Name</th>
                        <th>Type</th>
                        <th>Country</th>
                        <th>Price</th>
                        <th class="text-center">Active</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody data-sortable-url="{{ route('admin.sort', 'accommodations') }}">
                    @foreach ($accommodations as $accommodation)
                        <tr data-id="{{ $accommodation->id }}">
                            <td class="drag-handle" title="Drag to reorder"><i class="fas fa-grip-vertical"></i></td>
                            <td style="width:80px;">
                                @if ($accommodation->cover_url)
                                    <img src="{{ $accommodation->cover_url }}" alt=""
                                         style="width:64px; height:48px; object-fit:cover; border-radius:6px;">
                                @else
                                    <span class="text-muted"><i class="fas fa-image"></i></span>
                                @endif
                            </td>
                            <td class="gp-mobile-keep">
                                <strong>{{ $accommodation->name }}</strong>
                                @if ($accommodation->location)<br><small class="text-muted">{{ $accommodation->location }}</small>@endif
                            </td>
                            <td>{{ $accommodation->type->name ?? '—' }}</td>
                            <td>{{ $accommodation->country }}</td>
                            <td>{{ $accommodation->price_from ?: '—' }}</td>
                            <td class="text-center">
                                @if ($accommodation->is_active)
                                    <span class="badge bg-success">Yes</span>
                                @else
                                    <span class="badge bg-secondary">No</span>
                                @endif
                            </td>
                            <td class="text-end">
                                <a href="{{ route('accommodations.edit', $accommodation) }}" class="btn-admin-sm btn-admin-edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('accommodations.destroy', $accommodation) }}" method="POST"
                                      class="d-inline"
                                      onsubmit="return confirm('Delete this accommodation?');">
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

            <div class="mt-3">{{ $accommodations->links('pagination::bootstrap-4') }}</div>
        @endif
    </div>
</div>
@endsection
