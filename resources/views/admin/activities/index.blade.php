@extends('layouts.app')

@section('title', 'Activities')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="mb-0" style="font-weight:600;">
        <i class="fas fa-hiking me-2" style="color:var(--admin-primary)"></i>
        Activities
    </h5>
    <a href="{{ route('activities.create') }}" class="btn-admin-primary">
        <i class="fas fa-plus me-1"></i> New Activity
    </a>
</div>

<div class="admin-card">
    <div class="admin-card-body">
        @if ($activities->isEmpty())
            <p class="text-muted mb-0">No activities yet. <a href="{{ route('activities.create') }}">Add the first one</a>.</p>
        @else
            <table class="admin-table">
                <thead>
                    <tr>
                        <th class="gp-mobile-keep" style="width:32px;"></th>
                        <th class="d-none d-md-table-cell"></th>
                        <th class="gp-mobile-keep">Name</th>
                        <th>Destination</th>
                        <th class="d-none d-md-table-cell">Category</th>
                        <th>Duration</th>
                        <th>Price</th>
                        <th class="d-none d-md-table-cell text-center">Images</th>
                        <th class="text-center">Active</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody data-sortable-url="{{ route('admin.sort', 'activities') }}">
                    @foreach ($activities as $activity)
                        <tr data-id="{{ $activity->id }}">
                            <td class="drag-handle" title="Drag to reorder"><i class="fas fa-grip-vertical"></i></td>
                            <td class="d-none d-md-table-cell" style="width:80px;">
                                @if ($activity->cover_url)
                                    <img src="{{ $activity->cover_url }}" alt=""
                                         style="width:64px; height:48px; object-fit:cover; border-radius:6px;">
                                @else
                                    <span class="text-muted"><i class="fas fa-image"></i></span>
                                @endif
                            </td>
                            <td class="gp-mobile-keep">
                                <strong>{{ $activity->name }}</strong><br>
                                <small class="text-muted">{{ Str::limit($activity->description, 60) }}</small>
                            </td>
                            <td>{{ $activity->destination->title ?? '—' }}</td>
                            <td class="d-none d-md-table-cell">{{ $activity->category->name ?? '—' }}</td>
                            <td>{{ $activity->duration ?: '—' }}</td>
                            <td>
                                @php
                                    $mode = $activity->price_mode;
                                    if ($mode === \App\Activity::PRICE_MODE_FREE) {
                                        $label = 'Free';
                                    } elseif ($mode === \App\Activity::PRICE_MODE_FIXED) {
                                        $label = $activity->unit_price !== null
                                            ? '$'.number_format((float) $activity->unit_price, 2)
                                            : '—';
                                    } elseif ($mode === \App\Activity::PRICE_MODE_FROM) {
                                        $label = $activity->price_from ?: '—';
                                    } else {
                                        // Tiered (or legacy null) — show the lowest set tier as a hint.
                                        $tiers = array_filter([
                                            $activity->price_local,
                                            $activity->price_african,
                                            $activity->price_international,
                                        ], fn ($p) => $p !== null);
                                        if (empty($tiers)) {
                                            $label = $activity->price_from ?: '—';
                                        } else {
                                            $min = min($tiers);
                                            $label = (float) $min === 0.0
                                                ? 'From Free'
                                                : 'From $'.number_format((float) $min, 2);
                                        }
                                    }
                                @endphp
                                {{ $label }}
                            </td>
                            <td class="d-none d-md-table-cell text-center">{{ $activity->media->count() }}</td>
                            <td class="text-center">
                                @if ($activity->is_active)
                                    <span class="badge bg-success">Yes</span>
                                @else
                                    <span class="badge bg-secondary">No</span>
                                @endif
                            </td>
                            <td class="text-end">
                                <a href="{{ route('activities.edit', $activity) }}" class="btn-admin-sm btn-admin-edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('activities.destroy', $activity) }}" method="POST"
                                      class="d-inline" onsubmit="return confirm('Delete this activity?');">
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

            <div class="mt-3">{{ $activities->links('pagination::bootstrap-4') }}</div>
        @endif
    </div>
</div>
@endsection
