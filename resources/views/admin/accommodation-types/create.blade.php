@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="mb-0" style="font-weight:600;">
        <i class="fas fa-bed me-2" style="color:var(--admin-primary)"></i>
        {{ isset($type) ? 'Edit Accommodation Type' : 'New Accommodation Type' }}
    </h5>
    <a href="{{ route('accommodation-types.index') }}" class="btn-admin-outline">
        <i class="fas fa-arrow-left me-1"></i> Back
    </a>
</div>

<div class="admin-card">
    <div class="admin-card-body">
        @include('partials.errors')

        <form action="{{ isset($type) ? route('accommodation-types.update', $type) : route('accommodation-types.store') }}"
              method="POST" class="admin-form">
            @csrf
            @if (isset($type))
                @method('PUT')
            @endif

            <div class="mb-3">
                <label for="name" class="form-label">Name</label>
                <input type="text" id="name" name="name" class="form-control"
                       value="{{ old('name', $type->name ?? '') }}" required>
            </div>

            <div class="mb-3">
                <label for="icon" class="form-label">Font Awesome Icon Class</label>
                <input type="text" id="icon" name="icon" class="form-control"
                       value="{{ old('icon', $type->icon ?? '') }}"
                       placeholder="e.g. fas fa-tree">
                <small class="text-muted">Find icons at fontawesome.com/icons (free version).</small>
            </div>

            <div class="mb-3">
                <label for="description" class="form-label">Short Description</label>
                <input type="text" id="description" name="description" class="form-control"
                       value="{{ old('description', $type->description ?? '') }}" maxlength="255">
            </div>

            <button type="submit" class="btn-admin-primary">
                <i class="fas fa-save me-1"></i> {{ isset($type) ? 'Update' : 'Create' }}
            </button>
        </form>
    </div>
</div>
@endsection
