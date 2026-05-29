@extends('layouts.front')

@section('seo')
    <x-seo title="Accommodation"
           description="Browse lodges, tented camps, and boutique hotels across Zimbabwe, Botswana, and Zambia." />
@endsection

@section('page')
@include('partials.navbar')

<section class="tt-page-hero gp-pangolin-bg">
    <div class="tt-page-hero-bg" style="background-image: url('{{ asset('images/about.jpg') }}');"></div>
    <div class="container" data-aos="fade-up">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb justify-content-center">
                <li class="breadcrumb-item"><a href="{{ url('/') }}"><i class="fas fa-home me-1"></i>Home</a></li>
                <li class="breadcrumb-item active">Accommodation</li>
            </ol>
        </nav>
        <h1 class="tt-page-title">Our <span class="accent">Accommodation</span></h1>
        <p class="tt-page-subtitle">
            From luxury safari lodges to riverside tented camps — handpicked stays across Zimbabwe, Botswana, and Zambia.
        </p>
    </div>
</section>

<section class="tt-section-sm">
    <div class="container">
        <div class="tt-search-bar" data-aos="fade-up">
            <form action="{{ route('accommodation.index') }}" method="GET">
                <div class="row g-3 align-items-end">
                    <div class="col-lg-4">
                        <div class="tt-form-group">
                            <label><i class="fas fa-search"></i> Search</label>
                            <input type="text" class="tt-input" name="search"
                                   placeholder="Name, location, or keyword" value="{{ $activeSearch }}">
                        </div>
                    </div>
                    <div class="col-lg-3">
                        <div class="tt-form-group">
                            <label><i class="fas fa-bed"></i> Type</label>
                            <select name="type" class="tt-input">
                                <option value="">All types</option>
                                @foreach ($types as $type)
                                    <option value="{{ $type->id }}" @selected($activeType === $type->id)>{{ $type->name }}</option>
                                @endforeach
                            </select>
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
        @if ($accommodations->isEmpty())
            <div class="tt-empty-state" data-aos="fade-up">
                <div class="icon"><i class="fas fa-bed"></i></div>
                <h3>No stays match your filters</h3>
                <p>Try clearing a filter or browsing all accommodation.</p>
                <a href="{{ route('accommodation.index') }}" class="btn-tt-primary">View All</a>
            </div>
        @else
            <div class="tt-dest-grid">
                @foreach ($accommodations as $accommodation)
                    <article class="tt-dest-card" data-aos="fade-up" data-aos-delay="{{ ($loop->iteration % 3) * 100 }}">
                        <div class="tt-dest-card-img" style="position:relative;">
                            @if ($accommodation->cover_url)
                                <img src="{{ $accommodation->cover_url }}" alt="{{ $accommodation->name }}" loading="lazy">
                            @else
                                <div style="height:240px; background:var(--tt-border); display:flex; align-items:center; justify-content:center;">
                                    <i class="fas fa-image fa-3x text-muted"></i>
                                </div>
                            @endif
                            @if ($accommodation->type)
                                <span class="card-badge" style="position:absolute; top:12px; left:12px;">
                                    @if ($accommodation->type->icon)<i class="{{ $accommodation->type->icon }} me-1"></i>@endif
                                    {{ $accommodation->type->name }}
                                </span>
                            @endif
                        </div>
                        <div class="tt-dest-card-body">
                            <div class="tt-dest-card-meta">
                                <span><i class="fas fa-map-marker-alt"></i> {{ $accommodation->location ?: $accommodation->country }}</span>
                                <span><i class="fas fa-globe-africa"></i> {{ $accommodation->country }}</span>
                            </div>
                            <h3 class="tt-dest-card-title">
                                <a href="{{ route('accommodation.show', $accommodation) }}">{{ $accommodation->name }}</a>
                            </h3>
                            <p class="tt-dest-card-desc">{{ Str::limit($accommodation->description, 110) }}</p>
                            @if ($accommodation->price_from)
                                <div class="mb-2"><strong>{{ $accommodation->price_from }}</strong></div>
                            @endif
                            <div class="tt-dest-card-footer">
                                <a href="{{ route('accommodation.show', $accommodation) }}" class="tt-dest-card-link">
                                    View Accommodation <i class="fas fa-arrow-right"></i>
                                </a>
                            </div>
                        </div>
                    </article>
                @endforeach
            </div>

            <div class="tt-pagination mt-5 d-flex justify-content-center" data-aos="fade-up">
                {{ $accommodations->links('pagination::bootstrap-4') }}
            </div>
        @endif
    </div>
</section>

@include('partials.footer')
@endsection
