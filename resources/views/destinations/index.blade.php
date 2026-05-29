@extends('layouts.app')

@php $isTrashed = $isTrashed ?? false; @endphp

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="mb-0" style="font-weight:600;">
        <i class="fas {{ $isTrashed ? 'fa-trash-can-arrow-up' : 'fa-map-marker-alt' }} me-2" style="color:var(--admin-primary)"></i>
        {{ $isTrashed ? 'Trashed destinations' : 'Destinations' }}
    </h5>
    @unless ($isTrashed)
        <a href="{{ route('destinations.create') }}" class="btn-admin-accent">
            <i class="fas fa-plus me-1"></i> Add Destination
        </a>
    @else
        <a href="{{ route('destinations.index') }}" class="btn btn-light btn-sm">
            <i class="fas fa-arrow-left me-1"></i> Back to destinations
        </a>
    @endunless
</div>

<div class="admin-card">
    <div class="admin-card-body p-0">
        @if ($destinations->count() > 0)
        <table class="admin-table">
            <thead>
                <tr>
                    @unless ($isTrashed)<th class="gp-mobile-keep" style="width:32px;"></th>@endunless
                    <th>Image</th>
                    <th class="gp-mobile-keep">Title</th>
                    <th>Country</th>
                    <th>Activities</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody @unless ($isTrashed) data-sortable-url="{{ route('admin.sort', 'destinations') }}" @endunless>
                @foreach ($destinations as $destination)
                <tr data-id="{{ $destination->id }}">
                    @unless ($isTrashed)
                        <td class="drag-handle" title="Drag to reorder"><i class="fas fa-grip-vertical"></i></td>
                    @endunless
                    <td>
                        <img src="{{ $destination->image_url }}" class="img-thumb" alt="{{ $destination->title }}">
                    </td>
                    <td class="gp-mobile-keep"><strong>{{ $destination->title }}</strong></td>
                    <td>{{ $destination->country ?? 'Zimbabwe' }}</td>
                    <td>{{ $destination->activities_count ?? $destination->activities()->count() }}</td>
                    <td class="text-end">
                        <div class="d-flex gap-1 justify-content-end">
                            @if ($destination->trashed())
                            <form action="{{ route('restore-destinations', $destination->id) }}" method="POST">
                                @csrf
                                @method('PUT')
                                <button type="submit" class="btn-admin-sm btn-admin-restore">
                                    <i class="fas fa-undo me-1"></i>Restore
                                </button>
                            </form>
                            @else
                            <a href="{{ route('destinations.edit', $destination->id) }}" class="btn-admin-sm btn-admin-edit">
                                <i class="fas fa-pen"></i>
                            </a>
                            @endif
                            <form action="{{ route('destinations.destroy', $destination->id) }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn-admin-sm btn-admin-delete">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @else
        <div class="admin-empty">
            @if ($isTrashed)
                <i class="fas fa-trash-can"></i>
                <h5>Trash is empty</h5>
                <p>Deleted destinations appear here. Restore or permanently remove from this page.</p>
            @else
                <i class="fas fa-map"></i>
                <h5>No Destinations Yet</h5>
                <p>Add your first destination to get started</p>
            @endif
        </div>
        @endif
    </div>
</div>
@endsection
