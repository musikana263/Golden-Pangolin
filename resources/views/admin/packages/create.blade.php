@extends('layouts.app')

@section('content')
@php $isEdit = isset($package); @endphp

<div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="mb-0" style="font-weight:600;">
        <i class="fas fa-suitcase-rolling me-2" style="color:var(--admin-primary)"></i>
        {{ $isEdit ? 'Edit Package' : 'New Package' }}
    </h5>
    <a href="{{ route('tour-packages.index') }}" class="btn-admin-outline">
        <i class="fas fa-arrow-left me-1"></i> Back
    </a>
</div>

<div class="admin-card">
    <div class="admin-card-body">
        @include('partials.errors')

        <form action="{{ $isEdit ? route('tour-packages.update', $package) : route('tour-packages.store') }}"
              method="POST" enctype="multipart/form-data" class="admin-form">
            @csrf
            @if ($isEdit) @method('PUT') @endif

            <div class="row g-3">
                <div class="col-md-8">
                    <label for="name" class="form-label">Package Name</label>
                    <input type="text" id="name" name="name" class="form-control"
                           value="{{ old('name', $package->name ?? '') }}" required>
                </div>

                <!-- Dynamic Destinations -->
                <div class="col-md-6">
                    <label class="form-label">Destinations</label>
                    <div id="destinations-wrapper">
                        @php
                            $selectedDestinations = old('destinations', $isEdit ? $package->destinations->pluck('id')->toArray() : []);
                        @endphp
                        @foreach ($selectedDestinations as $id)
                            <div class="d-flex mb-2 align-items-center">
                                <select name="destinations[]" class="form-select me-2">
                                    @foreach ($destinations as $d)
                                        <option value="{{ $d->id }}" @selected($d->id == $id)>{{ $d->title }}</option>
                                    @endforeach
                                </select>
                                <button type="button" class="btn btn-sm btn-danger remove-item">Remove</button>
                            </div>
                        @endforeach
                    </div>
                    <button type="button" class="btn btn-sm btn-outline-primary mt-2" id="add-destination">
                        + Add Destination
                    </button>
                </div>

                <!-- Dynamic Activities -->
                <div class="col-md-6">
                    <label class="form-label">Activities</label>
                    <div id="activities-wrapper">
                        @php
                            $selectedActivities = old('activities', $isEdit ? $package->activities->pluck('id')->toArray() : []);
                        @endphp
                        @foreach ($selectedActivities as $id)
                            <div class="d-flex mb-2 align-items-center">
                                <select name="activities[]" class="form-select me-2">
                                    @foreach ($activities as $activity)
                                        <option value="{{ $activity->id }}" @selected($activity->id == $id)>
                                            {{ $activity->name }} — {{ $activity->destination->title ?? '—' }}
                                        </option>
                                    @endforeach
                                </select>
                                <button type="button" class="btn btn-sm btn-danger remove-item">Remove</button>
                            </div>
                        @endforeach
                    </div>
                    <button type="button" class="btn btn-sm btn-outline-primary mt-2" id="add-activity">
                        + Add Activity
                    </button>
                </div>

                <!-- Other fields -->
                <div class="col-12">
                    <label for="summary" class="form-label">Short Description</label>
                    <textarea id="summary" name="summary" class="form-control" rows="3"
                              maxlength="500" required>{{ old('summary', $package->summary ?? '') }}</textarea>
                </div>

                <div class="col-md-4">
                    <label for="duration" class="form-label">Duration</label>
                    <input type="text" id="duration" name="duration" class="form-control"
                           value="{{ old('duration', $package->duration ?? '') }}"
                           placeholder="e.g. 7 Days / 6 Nights">
                </div>

                <div class="col-md-4">
                    <label for="group_size" class="form-label">Group Size</label>
                    <input type="text" id="group_size" name="group_size" class="form-control"
                           value="{{ old('group_size', $package->group_size ?? '') }}"
                           placeholder="e.g. 2-10 People">
                </div>

                <div class="col-md-4">
                    <label for="price_from" class="form-label">Price</label>
                    <input type="text" id="price_from" name="price_from" class="form-control"
                           value="{{ old('price_from', $package->price_from ?? '') }}"
                           placeholder="From USD 1,890 per person">
                </div>

                <div class="col-md-4">
                    <label for="accommodation_id" class="form-label">Accommodation (optional)</label>
                    <select id="accommodation_id" name="accommodation_id" class="form-select">
                        <option value="">— None —</option>
                        @foreach ($accommodations as $a)
                            <option value="{{ $a->id }}"
                                @selected(old('accommodation_id', $package->accommodation_id ?? null) == $a->id)>
                                {{ $a->name }} ({{ $a->country }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Image upload -->
                <div class="col-12">
                    <label for="image" class="form-label">
                        {{ $isEdit && $package->media->isNotEmpty() ? 'Replace Image' : 'Image' }}
                    </label>
                    <input type="file" id="image" name="image" class="form-control" accept="image/*">
                </div>

                <!-- Toggles -->
                <div class="col-md-6">
                    <div class="form-check form-switch">
                        <input type="hidden" name="is_active" value="0">
                        <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1"
                               @checked(old('is_active', $package->is_active ?? true))>
                        <label class="form-check-label" for="is_active">Visible on public site</label>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-check form-switch">
                        <input type="hidden" name="is_featured" value="0">
                        <input class="form-check-input" type="checkbox" id="is_featured" name="is_featured" value="1"
                               @checked(old('is_featured', $package->is_featured ?? false))>
                        <label class="form-check-label" for="is_featured">Featured package</label>
                    </div>
                </div>

                <div class="col-12">
                    <div class="form-check form-switch">
                        <input type="hidden" name="is_hero" value="0">
                        <input class="form-check-input" type="checkbox" id="is_hero" name="is_hero" value="1"
                               @checked(old('is_hero', $package->is_hero ?? false))>
                        <label class="form-check-label" for="is_hero">
                            <i class="fas fa-star me-1" style="color:var(--admin-primary)"></i>
                            Show this package on the homepage hero
                        </label>
                        <div class="form-text">
                            Only one package can hold the hero slot. Turning this on automatically removes it from any other package.
                        </div>
                    </div>
                </div>

                {{-- Tier availability — which traveller groups this package is offered to.
                     Default for new packages = all three (== legacy NULL behaviour). --}}
                <div class="col-12">
                    @php
                        $tierOptions = [
                            'local'         => 'Local travellers',
                            'african'       => 'Regional (African) travellers',
                            'international' => 'International travellers',
                        ];
                        $existing = $package->tiers ?? null;
                        // Empty/null on an existing package = available to everyone.
                        // For NEW packages we also tick all three by default.
                        $selectedTiers = old('tiers', ($existing === null || $existing === [])
                            ? array_keys($tierOptions)
                            : $existing);
                    @endphp
                    <label class="form-label mb-2">
                        <i class="fas fa-users me-1"></i> Available to
                    </label>
                    <div class="d-flex flex-wrap gap-3">
                        @foreach ($tierOptions as $value => $label)
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox"
                                       id="tier_{{ $value }}" name="tiers[]" value="{{ $value }}"
                                       @checked(in_array($value, $selectedTiers, true))>
                                <label class="form-check-label" for="tier_{{ $value }}">{{ $label }}</label>
                            </div>
                        @endforeach
                    </div>
                    <div class="form-text">
                        Untick a group to hide this package from those visitors. Tick all three (default) to offer it to everyone.
                    </div>
                </div>
            </div>

            <div class="mt-4">
                <button type="submit" class="btn-admin-primary">
                    <i class="fas fa-save me-1"></i>
                    {{ $isEdit ? 'Update Package' : 'Create Package' }}
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const destinations = @json($destinations);
    const activities = @json($activities);

    function createSelect(name, options) {
        const wrapper = document.createElement('div');
        wrapper.classList.add('d-flex','mb-2','align-items-center');

        const select = document.createElement('select');
        select.name = name + '[]';
        select.classList.add('form-select','me-2');

        options.forEach(opt => {
            const option = document.createElement('option');
            option.value = opt.id;
            option.textContent = opt.title || opt.name;
            select.appendChild(option);
        });

        const removeBtn = document.createElement('button');
        removeBtn.type = 'button';
        removeBtn.classList.add('btn','btn-sm','btn-danger','remove-item');
        removeBtn.textContent = 'Remove';
        removeBtn.addEventListener('click', () => wrapper.remove());

        wrapper.appendChild(select);
        wrapper.appendChild(removeBtn);

        return wrapper;
    }

    document.getElementById('add-destination').addEventListener('click', () => {
        document.getElementById('destinations-wrapper').appendChild(createSelect('destinations', destinations));
    });

    document.getElementById('add-activity').addEventListener('click', () => {
        document.getElementById('activities-wrapper').appendChild(createSelect('activities', activities));
    });

    // Enable remove on preloaded items
    document.querySelectorAll('.remove-item').forEach(btn => {
        btn.addEventListener('click', e => e.target.closest('div').remove());
    });
});
</script>
@endsection
