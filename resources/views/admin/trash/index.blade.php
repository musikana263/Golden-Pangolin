@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="mb-0" style="font-weight:600;">
        <i class="fas fa-trash-can-arrow-up me-2" style="color:var(--admin-primary)"></i>
        Trash
    </h5>
    <span class="text-muted small">Showing up to 5 per section</span>
</div>

<div class="row g-3">
    @foreach ($sections as $section)
        <div class="col-12 col-lg-6">
            <div class="admin-card h-100">
                <div class="admin-card-header">
                    <h5><i class="{{ $section['icon'] }} me-2"></i> {{ $section['label'] }}</h5>
                    <span class="text-muted small">{{ $section['items']->count() }} in trash</span>
                </div>
                <div class="admin-card-body p-0">
                    @if ($section['items']->isEmpty())
                        <div class="admin-empty py-4">
                            <i class="fas fa-circle-check"></i>
                            <p class="mb-0">Nothing here</p>
                        </div>
                    @else
                        <ul class="list-unstyled m-0">
                            @foreach ($section['items'] as $item)
                                <li class="gp-trash-row">
                                    <div class="gp-trash-row__body">
                                        @php
                                            $title = $item->title
                                                ?? $item->name
                                                ?? $item->summary
                                                ?? 'Untitled';
                                            $thumb = method_exists($item, 'getImageUrlAttribute') && $item->image_url
                                                ? $item->image_url
                                                : ($item->cover_url ?? null);
                                        @endphp
                                        @if ($thumb)
                                            <img src="{{ $thumb }}" alt="" class="gp-trash-row__thumb">
                                        @else
                                            <span class="gp-trash-row__thumb gp-trash-row__thumb--placeholder">
                                                <i class="{{ $section['icon'] }}"></i>
                                            </span>
                                        @endif
                                        <div class="gp-trash-row__meta">
                                            <strong>{{ Str::limit($title, 38) }}</strong>
                                            <small class="text-muted">
                                                Deleted {{ optional($item->deleted_at)->diffForHumans() ?? '—' }}
                                            </small>
                                        </div>
                                    </div>
                                    <div class="gp-trash-row__actions">
                                        <form method="POST"
                                              action="{{ route('trash.restore', ['type' => $section['key'], 'id' => $item->id]) }}"
                                              class="d-inline">
                                            @csrf
                                            @method('PUT')
                                            <button type="submit" class="btn-admin-sm btn-admin-restore" title="Restore">
                                                <i class="fas fa-rotate-left"></i>
                                            </button>
                                        </form>
                                        <form method="POST"
                                              action="{{ route('trash.force-destroy', ['type' => $section['key'], 'id' => $item->id]) }}"
                                              class="d-inline"
                                              onsubmit="return confirm('Permanently delete this {{ Str::lower($section['label']) }}? This cannot be undone.');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn-admin-sm btn-admin-delete" title="Delete forever">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </div>
            </div>
        </div>
    @endforeach
</div>

<style>
    .gp-trash-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        padding: 12px 18px;
        border-bottom: 1px solid var(--admin-border);
    }
    .gp-trash-row:last-child { border-bottom: 0; }
    .gp-trash-row__body { display: flex; align-items: center; gap: 12px; flex: 1 1 auto; min-width: 0; }
    .gp-trash-row__thumb {
        width: 48px; height: 48px;
        object-fit: cover;
        border-radius: 8px;
        flex-shrink: 0;
        background: rgba(212, 160, 60, .12);
        display: inline-flex;
        align-items: center;
        justify-content: center;
        color: var(--admin-primary);
        font-size: 1.1rem;
    }
    .gp-trash-row__meta {
        display: flex;
        flex-direction: column;
        min-width: 0;
    }
    .gp-trash-row__meta strong {
        font-size: .92rem;
        color: var(--admin-text);
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    .gp-trash-row__meta small { font-size: .78rem; }
    .gp-trash-row__actions { display: flex; gap: 4px; flex-shrink: 0; }
</style>
@endsection
