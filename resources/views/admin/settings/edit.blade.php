@extends('layouts.app')

@section('content')
<div class="row justify-content-center">
    <div class="col-12 col-md-10 col-lg-7 col-xl-6">

        <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="mb-0" style="font-weight:600;">
                <i class="fas fa-sliders me-2" style="color:var(--admin-primary)"></i>Site settings
            </h5>
        </div>

        <div class="admin-card">
            <div class="admin-card-body">
                @include('partials.errors')

                <form method="POST" action="{{ route('settings.update') }}">
                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <label for="whatsapp_number" class="form-label">WhatsApp number</label>
                        <div class="input-group">
                            <span class="input-group-text">+</span>
                            <input type="text" id="whatsapp_number" name="whatsapp_number"
                                   value="{{ old('whatsapp_number', $whatsappNumber) }}"
                                   inputmode="numeric" pattern="[0-9]{8,15}"
                                   class="form-control @error('whatsapp_number') is-invalid @enderror"
                                   required>
                        </div>
                        <small class="text-muted">
                            Country code first, no plus / spaces / dashes. Example:
                            <code>26371527381</code> for +263 71 527 381.
                        </small>
                    </div>

                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-1"></i> Save settings
                    </button>
                </form>
            </div>
        </div>

    </div>
</div>
@endsection
