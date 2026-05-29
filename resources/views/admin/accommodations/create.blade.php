@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="mb-0" style="font-weight:600;">
        <i class="fas fa-hotel me-2" style="color:var(--admin-primary)"></i>
        {{ isset($accommodation) ? 'Edit Accommodation' : 'New Accommodation' }}
    </h5>
    <a href="{{ route('accommodations.index') }}" class="btn-admin-outline">
        <i class="fas fa-arrow-left me-1"></i> Back
    </a>
</div>

<div class="admin-card">
    <div class="admin-card-body">
        @include('partials.errors')

        <form action="{{ isset($accommodation) ? route('accommodations.update', $accommodation) : route('accommodations.store') }}"
              method="POST" enctype="multipart/form-data" class="admin-form">
            @csrf
            @if (isset($accommodation))
                @method('PUT')
            @endif

            <div class="row g-3">
                <div class="col-md-8">
                    <label for="name" class="form-label">Name</label>
                    <input type="text" id="name" name="name" class="form-control"
                           value="{{ old('name', $accommodation->name ?? '') }}" required>
                </div>

                <div class="col-md-4">
                    <label for="accommodation_type_id" class="form-label">Type</label>
                    <select id="accommodation_type_id" name="accommodation_type_id" class="form-select">
                        <option value="">— Choose —</option>
                        @foreach ($types as $type)
                            <option value="{{ $type->id }}"
                                @selected(old('accommodation_type_id', $accommodation->accommodation_type_id ?? null) == $type->id)>
                                {{ $type->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-4">
                    <label for="country" class="form-label">Country</label>
                    <select id="country" name="country" class="form-select" required>
                        @foreach (config('site.countries') as $country)
                            <option value="{{ $country }}"
                                @selected(old('country', $accommodation->country ?? 'Zimbabwe') === $country)>
                                {{ $country }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-4">
                    <label for="location" class="form-label">Location</label>
                    <input type="text" id="location" name="location" class="form-control"
                           value="{{ old('location', $accommodation->location ?? '') }}"
                           placeholder="e.g. Victoria Falls, Zimbabwe">
                </div>

                <div class="col-md-4">
                    <label for="price_from" class="form-label">Price</label>
                    <input type="text" id="price_from" name="price_from" class="form-control"
                           value="{{ old('price_from', $accommodation->price_from ?? '') }}"
                           placeholder="e.g. From USD 220 / night">
                </div>

                <div class="col-12">
                    <label for="description" class="form-label">Short Description</label>
                    <textarea id="description" name="description" class="form-control" rows="2"
                              maxlength="500" required>{{ old('description', $accommodation->description ?? '') }}</textarea>
                </div>

                <div class="col-12">
                    <label class="form-label">Amenities</label>
                    @php
                        $selectedAmenities = old('amenities', isset($accommodation) ? ($accommodation->amenities ?? []) : []);
                    @endphp
                    <div class="gp-multiselect" data-gp-multiselect>
                        <button type="button" class="gp-multiselect-trigger" data-gp-multiselect-trigger>
                            <span class="gp-multiselect-chips" data-gp-multiselect-chips>
                                <span class="gp-multiselect-placeholder">Pick amenities…</span>
                            </span>
                            <i class="fas fa-chevron-down gp-multiselect-caret"></i>
                        </button>

                        <div class="gp-multiselect-panel" hidden>
                            <div class="gp-multiselect-search-wrap">
                                <i class="fas fa-search"></i>
                                <input type="search" class="gp-multiselect-search" placeholder="Search amenities…">
                            </div>
                            <div class="gp-multiselect-options">
                                @foreach (config('site.amenities') as $key => $a)
                                    <label class="gp-multiselect-option"
                                           data-search="{{ Str::lower($a['label']) }}"
                                           data-label="{{ $a['label'] }}"
                                           data-icon="{{ $a['icon'] }}">
                                        <input type="checkbox" name="amenities[]" value="{{ $key }}"
                                               @checked(in_array($key, $selectedAmenities, true))>
                                        <i class="{{ $a['icon'] }} gp-multiselect-option-icon"></i>
                                        <span>{{ $a['label'] }}</span>
                                        <i class="fas fa-check gp-multiselect-option-check"></i>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    <small class="text-muted">Pick all that apply.</small>
                </div>

                <div class="col-12">
                    <label class="form-label">Add Images</label>
                    @include('partials.photo-picker', [
                        'name'  => 'images',
                        'max'   => 6,
                        'error' => $errors->first('images'),
                        'hint'  => 'Pick up to six photos. The first becomes the cover. Remove existing ones above to free up slots.',
                    ])
                </div>

                @if (isset($accommodation) && $accommodation->media->isNotEmpty())
                    <div class="col-12">
                        <label class="form-label">Current Images</label>
                        <div class="d-flex flex-wrap gap-3">
                            @foreach ($accommodation->media as $media)
                                <div style="position:relative;">
                                    <img src="{{ $media->url }}" alt=""
                                         style="width:140px; height:100px; object-fit:cover; border-radius:8px;">
                                    <label style="position:absolute; top:6px; right:6px; background:rgba(255,255,255,.92); border-radius:4px; padding:2px 6px; font-size:.8rem; cursor:pointer;">
                                        <input type="checkbox" name="delete_media[]" value="{{ $media->id }}"> Delete
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
                               @checked(old('is_active', $accommodation->is_active ?? true))>
                        <label class="form-check-label" for="is_active">Visible on public site</label>
                    </div>
                </div>
            </div>

            <div class="mt-4">
                <button type="submit" class="btn-admin-primary">
                    <i class="fas fa-save me-1"></i> {{ isset($accommodation) ? 'Update' : 'Create' }}
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
(function () {
    document.querySelectorAll('[data-gp-multiselect]').forEach(function (root) {
        var trigger = root.querySelector('[data-gp-multiselect-trigger]');
        var panel   = root.querySelector('.gp-multiselect-panel');
        var chips   = root.querySelector('[data-gp-multiselect-chips]');
        var search  = root.querySelector('.gp-multiselect-search');
        var options = Array.from(root.querySelectorAll('.gp-multiselect-option'));

        function renderChips() {
            chips.innerHTML = '';
            var selected = options.filter(function (o) { return o.querySelector('input').checked; });
            if (selected.length === 0) {
                var p = document.createElement('span');
                p.className = 'gp-multiselect-placeholder';
                p.textContent = 'Pick amenities…';
                chips.appendChild(p);
                return;
            }
            selected.forEach(function (opt) {
                var label = opt.dataset.label;
                var icon  = opt.dataset.icon;
                var chip = document.createElement('span');
                chip.className = 'gp-multiselect-chip';
                chip.innerHTML = '<i class="' + icon + '"></i>' + label +
                    '<button type="button" class="gp-multiselect-chip-x" aria-label="Remove">&times;</button>';
                chip.querySelector('.gp-multiselect-chip-x').addEventListener('click', function (e) {
                    e.stopPropagation();
                    opt.querySelector('input').checked = false;
                    opt.classList.remove('is-checked');
                    renderChips();
                });
                chips.appendChild(chip);
            });
        }

        // Initial render
        options.forEach(function (o) {
            if (o.querySelector('input').checked) o.classList.add('is-checked');
        });
        renderChips();

        // Open / close
        trigger.addEventListener('click', function () {
            var isOpen = !panel.hasAttribute('hidden');
            if (isOpen) {
                panel.setAttribute('hidden', '');
                root.classList.remove('is-open');
            } else {
                panel.removeAttribute('hidden');
                root.classList.add('is-open');
                setTimeout(function () { search && search.focus(); }, 50);
            }
        });

        // Click outside closes
        document.addEventListener('click', function (e) {
            if (! root.contains(e.target)) {
                panel.setAttribute('hidden', '');
                root.classList.remove('is-open');
            }
        });

        // Option toggle
        options.forEach(function (opt) {
            opt.addEventListener('click', function (e) {
                // Let the native click handler flip the checkbox first
                setTimeout(function () {
                    opt.classList.toggle('is-checked', opt.querySelector('input').checked);
                    renderChips();
                }, 0);
            });
        });

        // Search filter
        if (search) {
            search.addEventListener('input', function () {
                var q = search.value.trim().toLowerCase();
                options.forEach(function (opt) {
                    var match = ! q || opt.dataset.search.indexOf(q) !== -1;
                    opt.style.display = match ? '' : 'none';
                });
            });
        }
    });
})();
</script>
@endsection
