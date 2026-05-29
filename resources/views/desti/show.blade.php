@extends('layouts.front')

@section('seo')
    <x-seo :title="$destinations->title"
           :description="Str::limit($destinations->description, 160)"
           :image="$destinations->image_url"
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
                <li class="breadcrumb-item"><a href="{{ route('packages') }}">Destinations</a></li>
                <li class="breadcrumb-item active">{{ $destinations->title }}</li>
            </ol>
        </nav>
        <h1 class="tt-page-title">{{ $destinations->title }}</h1>
        <p class="tt-page-subtitle">
            <i class="fas fa-globe-africa me-1"></i> {{ $destinations->country ?? 'Zimbabwe' }}
        </p>
    </div>
</section>

<section class="tt-section">
    <div class="container">
        {{-- Short description only — the "About this destination" header and
             long-form content body were removed so the page leads straight
             with the summary and gets to the activities faster. --}}
        <p class="lead text-center" style="max-width:880px; margin:0 auto;" data-aos="fade-up">
            {{ $destinations->description }}
        </p>

        @if ($destinations->activities->isNotEmpty())
            <hr class="my-5">
            <div class="tt-section-header text-center mb-4" data-aos="fade-up">
                <div class="tt-pretitle">Things to do</div>
                <h3 class="mb-2">Activities in <span class="accent">{{ $destinations->title }}</span></h3>
                <p class="text-muted mb-0">Experiences to slot into your trip.</p>
            </div>

            <div class="tt-activity-list" data-aos="fade-up">
                @foreach ($destinations->activities as $activity)
                    <article class="tt-activity-row">
                        <div class="tt-activity-row-img">
                            @if ($activity->media->isNotEmpty())
                                @php $carouselId = 'activityCarousel-'.$activity->id; @endphp
                                <div id="{{ $carouselId }}" class="carousel slide carousel-fade gp-activity-carousel"
                                     data-bs-interval="false">
                                    <div class="carousel-inner h-100">
                                        @foreach ($activity->media as $idx => $m)
                                            <div class="carousel-item h-100 {{ $idx === 0 ? 'active' : '' }}">
                                                <img src="{{ $m->url }}" alt="{{ $activity->name }}"
                                                     class="d-block w-100 h-100" style="object-fit:cover;" loading="lazy">
                                            </div>
                                        @endforeach
                                    </div>
                                    @if ($activity->media->count() > 1)
                                        <button class="carousel-control-prev" type="button"
                                                data-bs-target="#{{ $carouselId }}" data-bs-slide="prev"
                                                aria-label="Previous photo">
                                            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                                        </button>
                                        <button class="carousel-control-next" type="button"
                                                data-bs-target="#{{ $carouselId }}" data-bs-slide="next"
                                                aria-label="Next photo">
                                            <span class="carousel-control-next-icon" aria-hidden="true"></span>
                                        </button>
                                        <div class="carousel-indicators gp-activity-indicators">
                                            @foreach ($activity->media as $idx => $m)
                                                <button type="button" data-bs-target="#{{ $carouselId }}"
                                                        data-bs-slide-to="{{ $idx }}"
                                                        class="{{ $idx === 0 ? 'active' : '' }}"
                                                        aria-label="Photo {{ $idx + 1 }}"></button>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>
                            @else
                                <div class="tt-activity-row-placeholder">
                                    <i class="fas fa-image"></i>
                                </div>
                            @endif
                        </div>
                        <div class="tt-activity-row-content">
                            <div class="d-flex justify-content-between align-items-start gap-3 flex-wrap">
                                <div class="flex-grow-1">
                                    <h4 class="mb-1">{{ $activity->name }}</h4>
                                    <div class="tt-activity-row-meta">
                                        @if ($activity->duration)
                                            <span><i class="fas fa-clock"></i> {{ $activity->duration }}</span>
                                        @endif
                                        <span class="tt-activity-row-price" title="{{ App\Services\TravellerTier::TIERS[$currentTravellerTier] ?? '' }} price">
                                            {{ $activity->applicable_price_formatted }}
                                        </span>
                                        @if ($activity->hasParkFee())
                                            <span class="tt-activity-row-fee" title="Charged separately">
                                                <i class="fas fa-tree"></i>
                                                + {{ $activity->park_fee_formatted }} {{ $activity->park_fee_display_label }}
                                            </span>
                                        @endif
                                    </div>
                                </div>
                                <div class="d-flex align-items-center">
                                    @include('partials.wishlist-button', ['item' => $activity, 'type' => 'activity'])
                                </div>
                            </div>
                            <p class="tt-activity-row-desc">{{ $activity->description }}</p>
                        </div>
                    </article>
                @endforeach
            </div>
        @endif

        @if ($relatedPackages->isNotEmpty())
            <hr class="my-5">
            <div class="mb-3">
                <h3 class="mb-0">Packages featuring {{ $destinations->title }}</h3>
            </div>

            <div class="tt-dest-grid">
                @foreach ($relatedPackages as $pkg)
                    <article class="tt-dest-card" data-aos="fade-up">
                        <div class="tt-dest-card-img" style="position:relative;">
                            @if ($pkg->cover_url)
                                <img src="{{ $pkg->cover_url }}" alt="{{ $pkg->name }}" loading="lazy">
                            @endif
                            <div class="gp-wishlist-overlay">
                                @include('partials.wishlist-button', ['item' => $pkg, 'type' => 'package'])
                            </div>
                        </div>
                        <div class="tt-dest-card-body">
                            <div class="tt-dest-card-meta">
                                @if ($pkg->duration) <span><i class="fas fa-clock"></i> {{ $pkg->duration }}</span> @endif
                                @if ($pkg->country) <span><i class="fas fa-globe-africa"></i> {{ $pkg->country }}</span> @endif
                            </div>
                            <h3 class="tt-dest-card-title">
                                <a href="{{ route('tours.show', $pkg) }}">{{ $pkg->name }}</a>
                            </h3>
                            <p class="tt-dest-card-desc">{{ Str::limit($pkg->summary, 110) }}</p>
                            @if ($pkg->price_from)
                                <div class="mb-2"><strong>{{ $pkg->price_from }}</strong></div>
                            @endif
                            <a href="{{ route('tours.show', $pkg) }}" class="tt-dest-card-link">
                                View Package <i class="fas fa-arrow-right"></i>
                            </a>
                        </div>
                    </article>
                @endforeach
            </div>

            <div class="text-center mt-4">
                <a href="{{ route('tours.index', ['destination' => $destinations->id]) }}" class="btn-tt-outline">
                    See all <i class="fas fa-arrow-right ms-1"></i>
                </a>
            </div>
        @endif

        @if ($nearbyStays->isNotEmpty())
            <hr class="my-5">
            <div class="mb-3">
                <h3 class="mb-0">Where to stay near {{ $destinations->title }}</h3>
            </div>

            <div class="tt-dest-grid">
                @foreach ($nearbyStays as $stay)
                    <article class="tt-dest-card" data-aos="fade-up">
                        <div class="tt-dest-card-img" style="position:relative;">
                            @if ($stay->cover_url)
                                <img src="{{ $stay->cover_url }}" alt="{{ $stay->name }}" loading="lazy">
                            @endif
                            @if ($stay->type)
                                <span class="card-badge" style="position:absolute; top:12px; left:12px;">
                                    @if ($stay->type->icon)<i class="{{ $stay->type->icon }} me-1"></i>@endif
                                    {{ $stay->type->name }}
                                </span>
                            @endif
                        </div>
                        <div class="tt-dest-card-body">
                            <div class="tt-dest-card-meta">
                                <span><i class="fas fa-map-marker-alt"></i> {{ $stay->location ?: $stay->country }}</span>
                            </div>
                            <h3 class="tt-dest-card-title">
                                <a href="{{ route('accommodation.show', $stay) }}">{{ $stay->name }}</a>
                            </h3>
                            <p class="tt-dest-card-desc">{{ Str::limit($stay->description, 110) }}</p>
                            @if ($stay->price_from)
                                <div class="mb-2"><strong>{{ $stay->price_from }}</strong></div>
                            @endif
                            <a href="{{ route('accommodation.show', $stay) }}" class="tt-dest-card-link">
                                View Accommodation <i class="fas fa-arrow-right"></i>
                            </a>
                        </div>
                    </article>
                @endforeach
            </div>

            <div class="text-center mt-4">
                <a href="{{ route('accommodation.index', ['country' => $destinations->country ?? 'Zimbabwe']) }}"
                   class="btn-tt-outline">
                    See all <i class="fas fa-arrow-right ms-1"></i>
                </a>
            </div>
        @endif
    </div>
</section>

@include('partials.footer')
@endsection

@push('styles')
<style>
    /* Park / conservation fee chip — sits next to the activity price.
       Visually distinct (muted background, small icon) so it's clearly
       NOT part of the headline price but still discoverable. */
    .tt-activity-row-fee {
        display: inline-flex;
        align-items: center;
        gap: .35rem;
        font-size: .6rem;
        font-weight: 600;
        color: #6b4914;
        background: rgba(212, 160, 60, .14);
        border: 1px solid rgba(212, 160, 60, .35);
        padding: 1px 6px;
        border-radius: 999px;
        white-space: nowrap;
    }
    .tt-activity-row-fee i { font-size: .7rem; color: #6b8a3d; }
    html[data-theme="dark"] .tt-activity-row-fee {
        color: #f3c779;
        background: rgba(212, 160, 60, .12);
        border-color: rgba(212, 160, 60, .45);
    }
    html[data-theme="dark"] .tt-activity-row-fee i { color: #a4d27e; }
</style>
@endpush

@push('scripts')
<script>
    (function () {
        // Activity carousels cycle only on hover; otherwise they show the first image.
        document.querySelectorAll('.tt-activity-row').forEach(function (row) {
            var el = row.querySelector('.gp-activity-carousel');
            if (! el || typeof bootstrap === 'undefined') return;

            var instance = bootstrap.Carousel.getOrCreateInstance(el, {
                interval: 2200,
                ride: false,
                pause: false,
                touch: false,
            });

            row.addEventListener('mouseenter', function () {
                instance.cycle();
            });
            row.addEventListener('mouseleave', function () {
                instance.pause();
                instance.to(0);
            });
        });
    })();
</script>
@endpush
