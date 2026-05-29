@extends('layouts.front')

@section('seo')
    <x-seo title="Tour Packages"
           description="Curated multi-day packages across Zimbabwe, Botswana, and Zambia — wildlife safaris, waterfall adventures, and cultural journeys." />
@endsection

@section('page')
@include('partials.navbar')

<section class="tt-page-hero gp-pangolin-bg">
    <div class="tt-page-hero-bg" style="background-image: url('{{ asset('images/about.jpg') }}');"></div>
    <div class="container" data-aos="fade-up">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb justify-content-center">
                <li class="breadcrumb-item"><a href="{{ url('/') }}"><i class="fas fa-home me-1"></i>Home</a></li>
                <li class="breadcrumb-item active">Packages</li>
            </ol>
        </nav>
        <h1 class="tt-page-title">Curated <span class="accent">Packages</span></h1>
        <p class="tt-page-subtitle">
            Multi-day journeys through Zimbabwe, Botswana, and Zambia — each one crafted by our local experts.
        </p>
    </div>
</section>

<section class="tt-section-sm">
    <div class="container">
        <div class="tt-search-bar" data-aos="fade-up">
            <form action="{{ route('tours.index') }}" method="GET">
                <div class="row g-3 align-items-end">
                    <div class="col-lg-4">
                        <div class="tt-form-group">
                            <label><i class="fas fa-search"></i> Search</label>
                            <input type="text" class="tt-input" name="search"
                                   placeholder="Package name or keyword" value="{{ $activeSearch }}">
                        </div>
                    </div>
                    <div class="col-lg-3">
                        <div class="tt-form-group">
                            <label><i class="fas fa-globe-africa"></i> Country</label>
                            <select name="country" class="tt-input">
                                <option value="">All countries</option>
                                @foreach ($countries as $country)
                                    <option value="{{ $country }}" @selected($activeCountry === $country)>{{ $country }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-lg-3">
                        <div class="tt-form-group">
                            <label><i class="fas fa-map-marker-alt"></i> Destination</label>
                            <select name="destination" class="tt-input">
                                <option value="">All destinations</option>
                                @foreach ($destinations as $d)
                                    <option value="{{ $d->id }}" @selected($activeDestination === $d->id)>{{ $d->title }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-lg-2">
                        <button type="submit" class="btn-tt-primary w-100">
                            Filter <i class="fas fa-arrow-right"></i>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</section>

<section class="tt-section">
    <div class="container">
        @if ($packages->isEmpty())
            <div class="tt-empty-state" data-aos="fade-up">
                <div class="icon"><i class="fas fa-suitcase-rolling"></i></div>
                <h3>No packages match your filters</h3>
                <p>Try a broader country or destination, or browse all packages.</p>
                <a href="{{ route('tours.index') }}" class="btn-tt-primary">View All Packages</a>
            </div>
        @else
            <div class="tt-dest-grid">
                @foreach ($packages as $package)
                    <article class="tt-dest-card" data-aos="fade-up">
                        <div class="tt-dest-card-img" style="position:relative;">
                            @if ($package->cover_url)
                                <img src="{{ $package->cover_url }}" alt="{{ $package->name }}" loading="lazy">
                            @endif
                            @if ($package->is_featured)
                                <span class="card-badge" style="position:absolute; top:12px; left:12px;">
                                    <i class="fas fa-star me-1"></i> Featured
                                </span>
                            @endif
                            <div class="gp-wishlist-overlay">
                                @include('partials.wishlist-button', ['item' => $package, 'type' => 'package'])
                            </div>
                        </div>
                        <div class="tt-dest-card-body">
                            <div class="tt-dest-card-meta">
                                @if ($package->duration) <span><i class="fas fa-clock"></i> {{ $package->duration }}</span> @endif
                            </div>
                            <h3 class="tt-dest-card-title">
                                <a href="{{ route('tours.show', $package) }}">{{ $package->name }}</a>
                            </h3>
                            <p class="tt-dest-card-desc">{{ Str::limit($package->summary, 110) }}</p>

                            @if ($package->destinations->isNotEmpty())
                                <div class="mb-2">
                                    @foreach ($package->destinations->take(3) as $d)
                                        <span class="badge bg-light text-dark border me-1">{{ $d->title }}</span>
                                    @endforeach
                                    @if ($package->destinations->count() > 3)
                                        <small class="text-muted">+{{ $package->destinations->count() - 3 }}</small>
                                    @endif
                                </div>
                            @endif

                            @if ($package->price_from)
                                <div class="mb-2"><strong>{{ $package->price_from }}</strong></div>
                            @endif
                            <a href="{{ route('tours.show', $package) }}" class="tt-dest-card-link">
                                View Package <i class="fas fa-arrow-right"></i>
                            </a>
                        </div>
                    </article>
                @endforeach
            </div>

            <div class="tt-pagination mt-5 d-flex justify-content-center" data-aos="fade-up">
                {{ $packages->links('pagination::bootstrap-4') }}
            </div>
        @endif
    </div>
</section>

@include('partials.footer')
@endsection
