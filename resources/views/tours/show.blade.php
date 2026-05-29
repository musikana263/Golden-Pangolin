@extends('layouts.front')

@section('seo')
    <x-seo :title="$package->name"
           :description="Str::limit($package->summary, 160)"
           :image="$package->cover_url"
           type="article" />
@endsection

@section('page')
@include('partials.navbar')

<section class="tt-page-hero gp-pangolin-bg">
    <div class="tt-page-hero-bg" style="background-image: url('{{ asset('images/about.jpg') }}');"></div>
    <div class="container" data-aos="fade-up">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb justify-content-center">
                <li class="breadcrumb-item"><a href="{{ url('/') }}"><i class="fas fa-home me-1"></i>Home</a></li>
                <li class="breadcrumb-item"><a href="{{ route('tours.index') }}">Packages</a></li>
                <li class="breadcrumb-item active">{{ $package->name }}</li>
            </ol>
        </nav>
        <h1 class="tt-page-title">{{ $package->name }}</h1>
        <p class="tt-page-subtitle">
            @php $countries = $package->countries; @endphp
            @if (! empty($countries))
                <i class="fas fa-globe-africa me-1"></i> {{ implode(' · ', $countries) }}
            @endif
            @if ($package->duration) · <i class="fas fa-clock ms-2 me-1"></i> {{ $package->duration }} @endif
            @if ($package->group_size) · <i class="fas fa-users ms-2 me-1"></i> {{ $package->group_size }} @endif
        </p>
    </div>
</section>

@if ($package->media->isNotEmpty())
<section class="tt-section-sm">
    <div class="container">
        <div class="row g-2" data-aos="fade-up">
            @php $images = $package->media; @endphp
            <div class="col-md-8">
                <a href="{{ $images->first()->url }}">
                    <img src="{{ $images->first()->url }}" alt="{{ $package->name }}"
                         style="width:100%; height:420px; object-fit:cover; border-radius:14px;">
                </a>
            </div>
            <div class="col-md-4">
                <div class="row g-2">
                    @foreach ($images->slice(1, 4) as $media)
                        <div class="col-6">
                            <a href="{{ $media->url }}">
                                <img src="{{ $media->url }}" alt=""
                                     style="width:100%; height:204px; object-fit:cover; border-radius:10px;">
                            </a>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</section>
@endif

<section class="tt-section">
    <div class="container">
        <div class="row g-4">
            <div class="col-lg-8">
                <p class="lead">{{ $package->summary }}</p>

                @if ($package->activities->isNotEmpty())
                    <h4 class="mt-5 mb-3">Activities included</h4>
                    <div class="row g-3">
                        @foreach ($package->activities as $a)
                            <div class="col-md-6">
                                <a href="{{ route('desti.show', $a->destination_id) }}" class="text-decoration-none text-reset gp-activity-tile">
                                    <div class="d-flex gap-3 align-items-center p-3"
                                         style="border:1px solid var(--tt-border); border-radius:12px;">
                                        @if ($a->cover_url)
                                            <img src="{{ $a->cover_url }}" alt=""
                                                 style="width:90px; height:80px; object-fit:cover; border-radius:8px; flex-shrink:0;">
                                        @endif
                                        <div>
                                            <strong>{{ $a->name }}</strong>
                                            @if ($a->destination)
                                                <small class="gp-activity-tile-meta d-block">
                                                    {{ $a->destination->title }}@if ($a->destination->country) · {{ $a->destination->country }}@endif
                                                </small>
                                            @endif
                                            @if ($a->duration)
                                                <small class="gp-activity-tile-meta d-block mt-1"><i class="fas fa-clock me-1"></i>{{ $a->duration }}</small>
                                            @endif
                                        </div>
                                    </div>
                                </a>
                            </div>
                        @endforeach
                    </div>
                @endif

                @if ($package->accommodation)
                    <h4 class="mt-5 mb-3">Featured accommodation</h4>
                    <a href="{{ route('accommodation.show', $package->accommodation) }}" class="text-decoration-none text-reset">
                        <div class="d-flex gap-3 align-items-center p-3"
                             style="border:1px solid var(--tt-border); border-radius:12px;">
                            @if ($package->accommodation->cover_url)
                                <img src="{{ $package->accommodation->cover_url }}" alt=""
                                     style="width:120px; height:90px; object-fit:cover; border-radius:8px;">
                            @endif
                            <div>
                                <strong>{{ $package->accommodation->name }}</strong>
                                @if ($package->accommodation->type)<span class="badge bg-light text-dark border ms-2">{{ $package->accommodation->type->name }}</span>@endif
                                <br>
                                <small class="text-muted">{{ $package->accommodation->location ?: $package->accommodation->country }}</small>
                            </div>
                        </div>
                    </a>
                @endif
            </div>

            <div class="col-lg-4">
                <div class="tt-sidebar-card" data-aos="fade-left">
                    @if ($package->price_from)
                        <h4 style="font-weight:700;">{{ $package->price_from }}</h4>
                        <p class="text-muted mb-3">Per person — based on shared accommodation.</p>
                    @endif

                    @php
                        $waNumber = config('site.whatsapp.number');
                        $msg = rawurlencode("Hello Golden Pangolin! I'd love to book the '{$package->name}' package.");
                    @endphp
                    <a href="https://wa.me/{{ $waNumber }}?text={{ $msg }}" target="_blank" rel="noopener"
                       class="btn-tt-primary w-100 mb-2"
                       style="background:#25D366; border-color:#25D366;">
                        <i class="fab fa-whatsapp me-1"></i> Book on WhatsApp
                    </a>
                    <a href="{{ route('contact') }}" class="btn-tt-outline w-100">
                        <i class="fas fa-envelope me-1"></i> Enquire by Email
                    </a>
                </div>
            </div>
        </div>

        @if ($related->isNotEmpty())
            <hr class="my-5">
            <h3 class="mb-4">Other packages you might like</h3>
            <div class="tt-dest-grid">
                @foreach ($related as $rel)
                    <article class="tt-dest-card" data-aos="fade-up">
                        <div class="tt-dest-card-img" style="position:relative;">
                            @if ($rel->cover_url)
                                <img src="{{ $rel->cover_url }}" alt="{{ $rel->name }}" loading="lazy">
                            @endif
                            <div class="gp-wishlist-overlay">
                                @include('partials.wishlist-button', ['item' => $rel, 'type' => 'package'])
                            </div>
                        </div>
                        <div class="tt-dest-card-body">
                            <h3 class="tt-dest-card-title">
                                <a href="{{ route('tours.show', $rel) }}">{{ $rel->name }}</a>
                            </h3>
                            <p class="tt-dest-card-desc">{{ Str::limit($rel->summary, 100) }}</p>
                            <a href="{{ route('tours.show', $rel) }}" class="tt-dest-card-link">
                                View Package <i class="fas fa-arrow-right"></i>
                            </a>
                        </div>
                    </article>
                @endforeach
            </div>
        @endif
    </div>
</section>

@include('partials.footer')
@endsection
