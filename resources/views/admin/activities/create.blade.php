@extends('layouts.app')

@section('css')
<style>
    .gp-price-mode {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
        gap: .5rem;
    }
    .gp-price-mode__opt {
        display: flex;
        align-items: flex-start;
        gap: .55rem;
        padding: .75rem .9rem;
        border: 1px solid var(--admin-border, #e5e7eb);
        border-radius: 10px;
        cursor: pointer;
        background: #fff;
        transition: border-color .15s ease, background .15s ease;
        margin: 0;
    }
    .gp-price-mode__opt:hover { border-color: rgba(212, 160, 60, .55); }
    .gp-price-mode__opt input[type="radio"] {
        margin-top: .2rem;
        accent-color: #d4a03c;
        flex-shrink: 0;
    }
    .gp-price-mode__opt:has(input:checked) {
        border-color: #d4a03c;
        background: #fdf5e6;
    }
    .gp-price-mode__lbl { font-size: .9rem; line-height: 1.35; }

    [data-gp-price-pane] { display: none; }
    [data-gp-price-pane].is-active { display: block; }

    [data-gp-park-pane] { display: none; }
    [data-gp-park-pane].is-active { display: block; }
</style>
@endsection

@section('content')
@php $isEdit = isset($activity); @endphp

<div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="mb-0" style="font-weight:600;">
        <i class="fas fa-hiking me-2" style="color:var(--admin-primary)"></i>
        {{ $isEdit ? 'Edit Activity' : 'New Activity' }}
    </h5>
    <a href="{{ route('activities.index') }}" class="btn-admin-outline">
        <i class="fas fa-arrow-left me-1"></i> Back
    </a>
</div>

<div class="admin-card">
    <div class="admin-card-body">
        @include('partials.errors')

        <form action="{{ $isEdit ? route('activities.update', $activity) : route('activities.store') }}"
              method="POST" enctype="multipart/form-data" class="admin-form">
            @csrf
            @if ($isEdit) @method('PUT') @endif

            <div class="row g-3">
                <div class="col-12">
                    <label for="name" class="form-label">Activity Name</label>
                    <input type="text" id="name" name="name" class="form-control"
                           value="{{ old('name', $activity->name ?? '') }}"
                           placeholder="e.g. Zambezi Sunset Cruise" required>
                </div>

                <div class="col-md-6">
                    <label for="destination_id" class="form-label">Destination</label>
                    <select id="destination_id" name="destination_id" class="form-select" required>
                        <option value="">— Choose destination —</option>
                        @php $selectedDest = old('destination_id', $activity->destination_id ?? ($selectedDestinationId ?? null)); @endphp
                        @foreach ($destinations as $dest)
                            <option value="{{ $dest->id }}" @selected($selectedDest == $dest->id)>
                                {{ $dest->title }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-6">
                    <label for="category_id" class="form-label">Category</label>
                    <select id="category_id" name="category_id" class="form-select">
                        <option value="">— No category —</option>
                        @foreach ($categories as $cat)
                            <option value="{{ $cat->id }}"
                                @selected(old('category_id', $activity->category_id ?? null) == $cat->id)>
                                {{ $cat->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-6">
                    <label for="duration" class="form-label">Duration</label>
                    <input type="text" id="duration" name="duration" class="form-control"
                           value="{{ old('duration', $activity->duration ?? '') }}"
                           placeholder="e.g. 2 hours, Half day, Full day">
                </div>

                @php
                    $currentMode = old('price_mode', $activity->price_mode ?? \App\Activity::PRICE_MODE_FIXED);
                @endphp

                <div class="col-12">
                    <label class="form-label mb-2">
                        <i class="fas fa-coins me-1"></i> Pricing mode
                    </label>
                    <div class="gp-price-mode" data-gp-price-mode>
                        @foreach (\App\Activity::PRICE_MODES as $value => $label)
                            <label class="gp-price-mode__opt">
                                <input type="radio" name="price_mode" value="{{ $value }}"
                                       @checked($currentMode === $value)>
                                <span class="gp-price-mode__lbl">{{ $label }}</span>
                            </label>
                        @endforeach
                    </div>
                    @error('price_mode')<small class="text-danger d-block mt-1">{{ $message }}</small>@enderror
                </div>

                {{-- FIXED — one price for everyone --}}
                <div class="col-12" data-gp-price-pane="fixed">
                    <label for="unit_price" class="form-label">Fixed price (USD)</label>
                    <div class="input-group" style="max-width:260px;">
                        <span class="input-group-text">$</span>
                        <input type="number" step="0.01" min="0" id="unit_price" name="unit_price"
                               class="form-control @error('unit_price') is-invalid @enderror"
                               value="{{ old('unit_price', $activity->unit_price ?? '') }}"
                               placeholder="0.00">
                    </div>
                    @error('unit_price')<small class="text-danger d-block mt-1">{{ $message }}</small>@enderror
                    <small class="text-muted d-block mt-1">Charged the same to local, African, and international visitors.</small>
                </div>

                {{-- FREE --}}
                <div class="col-12" data-gp-price-pane="free">
                    <div class="alert alert-info mb-0 py-2 px-3" style="font-size:.9rem;">
                        <i class="fas fa-gift me-1"></i> This activity will be shown as <strong>Free</strong> on the public site. No additional inputs needed.
                    </div>
                </div>

                {{-- TIERED --}}
                <div class="col-12" data-gp-price-pane="tiered">
                    <div class="d-flex justify-content-between align-items-baseline mb-1">
                        <label class="form-label mb-0"><i class="fas fa-tags me-1"></i> Tier prices (USD)</label>
                        <small class="text-muted">Set at least one. Leave a tier blank to hide it from that group; set to 0 to mark it free for that group.</small>
                    </div>
                    <div class="row g-2">
                        <div class="col-md-4">
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-home me-1"></i> Local</span>
                                <span class="input-group-text">$</span>
                                <input type="number" step="0.01" min="0" name="price_local"
                                       class="form-control @error('price_local') is-invalid @enderror"
                                       value="{{ old('price_local', $activity->price_local ?? '') }}"
                                       placeholder="0.00">
                            </div>
                            @error('price_local')<small class="text-danger d-block mt-1">{{ $message }}</small>@enderror
                        </div>
                        <div class="col-md-4">
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-globe-africa me-1"></i> African</span>
                                <span class="input-group-text">$</span>
                                <input type="number" step="0.01" min="0" name="price_african"
                                       class="form-control @error('price_african') is-invalid @enderror"
                                       value="{{ old('price_african', $activity->price_african ?? '') }}"
                                       placeholder="0.00">
                            </div>
                            @error('price_african')<small class="text-danger d-block mt-1">{{ $message }}</small>@enderror
                        </div>
                        <div class="col-md-4">
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-plane me-1"></i> Int'l</span>
                                <span class="input-group-text">$</span>
                                <input type="number" step="0.01" min="0" name="price_international"
                                       class="form-control @error('price_international') is-invalid @enderror"
                                       value="{{ old('price_international', $activity->price_international ?? '') }}"
                                       placeholder="0.00">
                            </div>
                            @error('price_international')<small class="text-danger d-block mt-1">{{ $message }}</small>@enderror
                        </div>
                    </div>
                </div>

                {{-- FROM --}}
                <div class="col-12" data-gp-price-pane="from">
                    <label for="price_from" class="form-label">Display label</label>
                    <input type="text" id="price_from" name="price_from"
                           class="form-control @error('price_from') is-invalid @enderror"
                           value="{{ old('price_from', $activity->price_from ?? '') }}"
                           placeholder="e.g. From $50 per person">
                    @error('price_from')<small class="text-danger d-block mt-1">{{ $message }}</small>@enderror
                    <small class="text-muted">Free-text shown as-is on cards.</small>
                </div>

                @php
                    $hasParkFee = old('has_park_fee', (! empty($activity->park_fee) ? '1' : '0')) === '1';
                @endphp

                <div class="col-12">
                    <div class="form-check">
                        <input type="hidden" name="has_park_fee" value="0">
                        <input class="form-check-input" type="checkbox" id="has_park_fee" name="has_park_fee" value="1"
                               data-gp-park-toggle @checked($hasParkFee)>
                        <label class="form-check-label" for="has_park_fee">
                            <i class="fas fa-tree me-1"></i> This activity has a park / conservation fee
                        </label>
                    </div>
                </div>

                <div class="col-12" data-gp-park-pane @class(['is-active' => $hasParkFee])>
                    <div class="input-group" style="max-width:260px;">
                        <span class="input-group-text">$</span>
                        <input type="number" step="0.01" min="0" name="park_fee"
                               class="form-control @error('park_fee') is-invalid @enderror"
                               value="{{ old('park_fee', $activity->park_fee ?? '') }}"
                               placeholder="0.00">
                    </div>
                    @error('park_fee')<small class="text-danger d-block mt-1">{{ $message }}</small>@enderror
                    <small class="text-muted d-block mt-1">Shown on the public site as <strong>Park fee: ${amount}</strong>.</small>
                </div>

                <div class="col-12">
                    <label for="description" class="form-label">Short Description</label>
                    <textarea id="description" name="description" class="form-control" rows="3"
                              maxlength="500" required>{{ old('description', $activity->description ?? '') }}</textarea>
                    <small class="text-muted">One- to two-sentence summary. Shown on the destination page.</small>
                </div>

                <div class="col-12">
                    <label class="form-label">Photos</label>
                    @include('partials.photo-picker', [
                        'name'  => 'images',
                        'max'   => 3,
                        'error' => $errors->first('images'),
                        'hint'  => 'Pick up to three photos. The first becomes the cover; the rest cycle in a hover carousel on the destination page.',
                    ])
                </div>

                @if ($isEdit && $activity->media->isNotEmpty())
                    <div class="col-12">
                        <label class="form-label">Current Photos</label>
                        <div class="d-flex flex-wrap gap-3">
                            @foreach ($activity->media as $m)
                                <div style="position:relative;">
                                    <img src="{{ $m->url }}" alt=""
                                         style="width:160px; height:120px; object-fit:cover; border-radius:8px;">
                                    <label style="position:absolute; top:6px; right:6px; background:rgba(255,255,255,.92); border-radius:4px; padding:2px 6px; font-size:.8rem; cursor:pointer;">
                                        <input type="checkbox" name="delete_media[]" value="{{ $m->id }}"> Delete
                                    </label>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                <div class="col-12">
                    <div class="form-check form-switch">
                        <input type="hidden" name="is_active" value="0">
                        <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1"
                               @checked(old('is_active', $activity->is_active ?? true))>
                        <label class="form-check-label" for="is_active">Visible on public site</label>
                    </div>
                </div>
            </div>

            <div class="mt-4">
                <button type="submit" class="btn-admin-primary">
                    <i class="fas fa-save me-1"></i>
                    {{ $isEdit ? 'Update Activity' : 'Create Activity' }}
                </button>
            </div>
        </form>
    </div>
</div>

@endsection

@section('scripts')
<script>
    (function () {
        var root = document.querySelector('[data-gp-price-mode]');
        if (!root) return;

        var panes = document.querySelectorAll('[data-gp-price-pane]');
        function syncPanes() {
            var checked = root.querySelector('input[name="price_mode"]:checked');
            var mode = checked ? checked.value : null;
            panes.forEach(function (p) {
                p.classList.toggle('is-active', p.getAttribute('data-gp-price-pane') === mode);
            });
        }
        root.addEventListener('change', function (e) {
            if (e.target.name === 'price_mode') syncPanes();
        });
        syncPanes();
    })();

    // Park-fee toggle — show the inputs only when the checkbox is ticked.
    // If unticked on submit, the controller treats park_fee/park_fee_label
    // as blank (handled below).
    (function () {
        var cb = document.querySelector('[data-gp-park-toggle]');
        var pane = document.querySelector('[data-gp-park-pane]');
        if (!cb || !pane) return;
        function sync() { pane.classList.toggle('is-active', cb.checked); }
        cb.addEventListener('change', sync);
        sync();
    })();
</script>
@endsection
