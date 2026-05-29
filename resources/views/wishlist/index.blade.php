@extends('layouts.front')

@section('seo')
    <x-seo title="My Wishlist"
           description="Trips, activities, and stays you've saved for later. Send the list straight to our team on WhatsApp or by email." />
@endsection

@section('page')
@include('partials.navbar')

<section class="tt-page-hero gp-pangolin-bg">
    <div class="tt-page-hero-bg" style="background-image: url('{{ asset('images/about.jpg') }}');"></div>
    <div class="container" data-aos="fade-up">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb justify-content-center">
                <li class="breadcrumb-item"><a href="{{ url('/') }}"><i class="fas fa-home me-1"></i>Home</a></li>
                <li class="breadcrumb-item active">Wishlist</li>
            </ol>
        </nav>
        <h1 class="tt-page-title">Your <span class="accent">Wishlist</span></h1>
        <p class="tt-page-subtitle">
            Save packages and activities, then send the list straight to us on WhatsApp.
        </p>
    </div>
</section>

<section class="tt-section">
    <div class="container">
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @php $total = $packages->count() + $activities->count() + $accommodations->count(); @endphp

        @if ($total === 0)
            <div class="text-center py-5">
                <i class="far fa-heart fa-4x text-muted mb-4"></i>
                <h3>Your wishlist is empty</h3>
                <p class="text-muted">Tap the heart on any package or activity to save it for later.</p>
                <a href="{{ route('tours.index') }}" class="btn-tt-primary mt-3">
                    Browse Packages <i class="fas fa-arrow-right ms-1"></i>
                </a>
            </div>
        @else
            <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-2">
                <h4 class="mb-0">{{ $total }} {{ Str::plural('item', $total) }} saved</h4>

                <div class="d-flex gap-2 flex-wrap">
                    <button type="button" class="btn-tt-primary"
                            data-bs-toggle="modal" data-bs-target="#gpSendWishlist">
                        <i class="fas fa-paper-plane me-1"></i> Send wishlist
                    </button>
                    <form action="{{ route('wishlist.clear') }}" method="POST" class="d-inline">
                        @csrf
                        <button type="submit" class="btn-tt-outline-white"
                                style="border-color:var(--tt-border); color:var(--tt-text);"
                                onclick="return confirm('Clear your whole wishlist?');">
                            <i class="fas fa-trash-alt me-1"></i> Clear All
                        </button>
                    </form>
                </div>
            </div>

            {{-- Send-wishlist chooser modal --}}
            <div class="modal fade gp-send-wishlist-modal" id="gpSendWishlist" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content gp-swm__content">
                        <div class="modal-header border-0">
                            <h5 class="modal-title"><i class="fas fa-paper-plane me-2" style="color:var(--tt-accent)"></i> Send your wishlist</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body pt-0">
                            <p class="text-muted mb-3">
                                Pick how you'd like to share your saved items with our team — we'll get back to you with options.
                            </p>

                            <a href="{{ $whatsappUrl }}" target="_blank" rel="noopener" class="gp-swm__option gp-swm__option--whatsapp">
                                <span class="gp-swm__icon" style="background:#25D366;"><i class="fab fa-whatsapp"></i></span>
                                <span class="gp-swm__body">
                                    <strong>WhatsApp</strong>
                                    <small>Opens WhatsApp with your wishlist ready to send.</small>
                                </span>
                                <i class="fas fa-arrow-right gp-swm__arrow"></i>
                            </a>

                            <a href="{{ $mailtoUrl }}" class="gp-swm__option gp-swm__option--email">
                                <span class="gp-swm__icon" style="background:linear-gradient(135deg,#d4a03c,#b8872e);"><i class="fas fa-envelope"></i></span>
                                <span class="gp-swm__body">
                                    <strong>Email</strong>
                                    <small>Opens your email client with the wishlist pre-filled.</small>
                                </span>
                                <i class="fas fa-arrow-right gp-swm__arrow"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <style>
                .gp-send-wishlist-modal .gp-swm__content {
                    border-radius: 16px;
                    border: 1px solid rgba(212, 160, 60, .35);
                }
                html[data-theme="dark"] .gp-send-wishlist-modal .gp-swm__content {
                    background: #1a1208;
                    color: #fbf3e1;
                }
                .gp-swm__option {
                    display: flex;
                    align-items: center;
                    gap: 14px;
                    padding: 14px 16px;
                    border: 1px solid var(--tt-border);
                    border-radius: 12px;
                    margin-bottom: 10px;
                    text-decoration: none;
                    color: var(--tt-text);
                    transition: border-color .15s ease, transform .12s ease, box-shadow .15s ease;
                }
                .gp-swm__option:last-child { margin-bottom: 0; }
                .gp-swm__option:hover {
                    border-color: var(--tt-accent);
                    color: var(--tt-text);
                    transform: translateY(-1px);
                    box-shadow: 0 6px 18px rgba(122, 82, 27, .12);
                }
                html[data-theme="dark"] .gp-swm__option {
                    border-color: rgba(212, 160, 60, .25);
                    color: #f3eadb;
                }
                html[data-theme="dark"] .gp-swm__option:hover { color: #fbf3e1; }
                .gp-swm__icon {
                    width: 46px; height: 46px;
                    border-radius: 12px;
                    display: inline-flex;
                    align-items: center;
                    justify-content: center;
                    color: #fff;
                    font-size: 1.25rem;
                    flex-shrink: 0;
                }
                .gp-swm__body { display: flex; flex-direction: column; flex: 1 1 auto; }
                .gp-swm__body strong { font-size: 1rem; }
                .gp-swm__body small { font-size: .82rem; color: var(--tt-text-muted); }
                html[data-theme="dark"] .gp-swm__body small { color: rgba(251, 243, 225, .65); }
                .gp-swm__arrow { color: var(--tt-text-muted); flex-shrink: 0; }
                .gp-swm__option:hover .gp-swm__arrow { color: var(--tt-accent); }
            </style>

            @if ($packages->isNotEmpty())
                <h3 class="mt-4 mb-3" style="font-family:var(--tt-font-display);">
                    <i class="fas fa-suitcase-rolling me-2" style="color:var(--tt-accent)"></i>
                    Packages
                </h3>
                <div class="tt-dest-grid mb-5">
                    @foreach ($packages as $package)
                        <article class="tt-dest-card" data-aos="fade-up">
                            <div class="tt-dest-card-img">
                                @if ($package->cover_url)
                                    <img src="{{ $package->cover_url }}" alt="{{ $package->name }}" loading="lazy">
                                @endif
                            </div>
                            <div class="tt-dest-card-body">
                                <div class="tt-dest-card-meta">
                                    <span><i class="fas fa-globe-africa"></i> {{ $package->country }}</span>
                                    @if ($package->duration)<span><i class="fas fa-clock"></i> {{ $package->duration }}</span>@endif
                                </div>
                                <h3 class="tt-dest-card-title">
                                    <a href="{{ route('tours.show', $package) }}">{{ $package->name }}</a>
                                </h3>
                                <p class="tt-dest-card-desc">{{ Str::limit($package->summary, 110) }}</p>
                                @if ($package->price_from)
                                    <div class="mb-2"><strong>{{ $package->price_from }}</strong></div>
                                @endif
                                <div class="tt-dest-card-footer">
                                    <a href="{{ route('tours.show', $package) }}" class="tt-dest-card-link">
                                        View Package <i class="fas fa-arrow-right"></i>
                                    </a>
                                    <form action="{{ route('wishlist.packages.destroy', $package) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm"
                                                style="background:transparent; color:#d9534f; border:1px solid #d9534f33;"
                                                title="Remove from wishlist">
                                            <i class="fas fa-heart-broken"></i>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </article>
                    @endforeach
                </div>
            @endif

            @if ($activities->isNotEmpty())
                <h3 class="mt-4 mb-3" style="font-family:var(--tt-font-display);">
                    <i class="fas fa-hiking me-2" style="color:var(--tt-accent)"></i>
                    Activities
                </h3>
                <div class="tt-dest-grid mb-5">
                    @foreach ($activities as $activity)
                        <article class="tt-dest-card" data-aos="fade-up">
                            <div class="tt-dest-card-img">
                                @if ($activity->cover_url)
                                    <img src="{{ $activity->cover_url }}" alt="{{ $activity->name }}" loading="lazy">
                                @endif
                            </div>
                            <div class="tt-dest-card-body">
                                <div class="tt-dest-card-meta">
                                    @if ($activity->destination)
                                        <span><i class="fas fa-map-marker-alt"></i> {{ $activity->destination->title }}</span>
                                    @endif
                                    @if ($activity->duration)<span><i class="fas fa-clock"></i> {{ $activity->duration }}</span>@endif
                                </div>
                                <h3 class="tt-dest-card-title">{{ $activity->name }}</h3>
                                <p class="tt-dest-card-desc">{{ Str::limit($activity->description, 110) }}</p>
                                <div class="mb-2"><strong>{{ $activity->applicable_price_formatted }}</strong></div>
                                <div class="tt-dest-card-footer">
                                    @if ($activity->destination)
                                        <a href="{{ route('desti.show', $activity->destination->id) }}" class="tt-dest-card-link">
                                            View Destination <i class="fas fa-arrow-right"></i>
                                        </a>
                                    @endif
                                    <form action="{{ route('wishlist.activities.destroy', $activity) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm"
                                                style="background:transparent; color:#d9534f; border:1px solid #d9534f33;"
                                                title="Remove from wishlist">
                                            <i class="fas fa-heart-broken"></i>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </article>
                    @endforeach
                </div>
            @endif

            @if ($accommodations->isNotEmpty())
                <h3 class="mt-4 mb-3" style="font-family:var(--tt-font-display);">
                    <i class="fas fa-bed me-2" style="color:var(--tt-accent)"></i>
                    Accommodation
                </h3>
                <div class="tt-dest-grid mb-5">
                    @foreach ($accommodations as $stay)
                        <article class="tt-dest-card" data-aos="fade-up">
                            <div class="tt-dest-card-img">
                                @if ($stay->cover_url)
                                    <img src="{{ $stay->cover_url }}" alt="{{ $stay->name }}" loading="lazy">
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
                                <div class="tt-dest-card-footer">
                                    <a href="{{ route('accommodation.show', $stay) }}" class="tt-dest-card-link">
                                        View Accommodation <i class="fas fa-arrow-right"></i>
                                    </a>
                                    <form action="{{ route('wishlist.accommodations.destroy', $stay) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm"
                                                style="background:transparent; color:#d9534f; border:1px solid #d9534f33;"
                                                title="Remove from wishlist">
                                            <i class="fas fa-heart-broken"></i>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </article>
                    @endforeach
                </div>
            @endif

        @endif
    </div>
</section>

@include('partials.footer')
@endsection
