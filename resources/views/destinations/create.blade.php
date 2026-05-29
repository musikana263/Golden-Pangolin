@extends('layouts.app')

@section('content')
@php $isEdit = isset($destinations); @endphp

<div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="mb-0" style="font-weight:600;">
        <i class="fas fa-map-marker-alt me-2" style="color:var(--admin-primary)"></i>
        {{ $isEdit ? 'Edit Destination' : 'New Destination' }}
    </h5>
    <a href="{{ route('destinations.index') }}" class="btn-admin-outline">
        <i class="fas fa-arrow-left me-1"></i> Back
    </a>
</div>

<div class="admin-card">
    <div class="admin-card-body">
        @include('partials.errors')

        <form action="{{ $isEdit ? route('destinations.update', $destinations->id) : route('destinations.store') }}"
              method="POST" enctype="multipart/form-data" class="admin-form">
            @csrf
            @if ($isEdit) @method('PUT') @endif

            <div class="row g-3">
                <div class="col-md-8">
                    <label for="title" class="form-label">Title</label>
                    <input type="text" class="form-control" name="title" id="title"
                           value="{{ old('title', $isEdit ? $destinations->title : '') }}"
                           placeholder="e.g. Victoria Falls" required>
                </div>

                <div class="col-md-4">
                    <label for="country" class="form-label">Country</label>
                    <select id="country" name="country" class="form-select">
                        @foreach (config('site.countries') as $country)
                            <option value="{{ $country }}"
                                @selected(old('country', $isEdit ? $destinations->country : 'Zimbabwe') === $country)>
                                {{ $country }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-12">
                    <label for="description" class="form-label">Short Description</label>
                    <textarea name="description" class="form-control" id="description" rows="3"
                              maxlength="500" required>{{ old('description', $isEdit ? $destinations->description : '') }}</textarea>
                    <small class="text-muted">Shown on the destination's public page. Keep it concise &mdash; one or two sentences.</small>
                </div>

                {{-- The long-form "About this destination" rich-text editor was
                     removed because the public page only shows the short
                     description. The `content` column is kept in the DB for
                     backwards compatibility and any future re-introduction. --}}

                @if ($isEdit && $destinations->image)
                    <div class="col-12">
                        <label class="form-label">Current Cover Image</label>
                        <div>
                            <img src="{{ $destinations->image_url }}" alt=""
                                 style="max-width:320px; border-radius:8px;">
                        </div>
                    </div>
                @endif

                <div class="col-12">
                    <label for="image" class="form-label">
                        {{ $isEdit ? 'Replace Cover Image' : 'Cover Image' }}
                    </label>
                    <input type="file" class="form-control" name="image" id="image" accept="image/*"
                           {{ $isEdit ? '' : 'required' }}>
                    <small class="text-muted">A single representative image. Activities have their own galleries.</small>
                </div>
            </div>

            <div class="mt-4">
                <button type="submit" class="btn-admin-primary">
                    <i class="fas fa-save me-1"></i>
                    {{ $isEdit ? 'Update Destination' : 'Create Destination' }}
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/trix/1.2.3/trix.js"></script>
@endsection

@section('css')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/trix/1.2.3/trix.css">
@endsection
