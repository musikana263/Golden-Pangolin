@extends('layouts.front')

@section('seo')
    <x-seo :title="$accommodation->name"
           :description="Str::limit($accommodation->description, 160)"
           :image="$accommodation->cover_url"
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
                <li class="breadcrumb-item"><a href="{{ route('accommodation.index') }}">Accommodation</a></li>
                <li class="breadcrumb-item active">{{ $accommodation->name }}</li>
            </ol>
        </nav>
        <h1 class="tt-page-title">{{ $accommodation->name }}</h1>
        <p class="tt-page-subtitle">
            <i class="fas fa-map-marker-alt me-1"></i> {{ $accommodation->location ?: $accommodation->country }}
            @if ($accommodation->type)
                · <i class="{{ $accommodation->type->icon ?? 'fas fa-bed' }} me-1"></i> {{ $accommodation->type->name }}
            @endif
        </p>
    </div>
</section>

<section class="tt-section">
    <div class="container">
        @if ($accommodation->media->isNotEmpty())
            @php
                $images        = $accommodation->media;
                $thumbs        = $images->slice(1);          // every image after the lead
                $thumbCount    = $thumbs->count();
                // If the visible thumbs are an odd number, give the LAST one a
                // full-width col-12 so the grid doesn't leave a lonely empty slot
                // (clean look for 5-thumb / 6-image accommodations).
                $lastIsFull    = ($thumbCount % 2) !== 0;
            @endphp
            <div class="row g-2 mb-5 gp-gallery" data-aos="fade-up" data-gp-gallery>
                <div class="col-md-8">
                    <a href="{{ $images->first()->url }}" data-gp-lightbox data-gp-index="0">
                        <img src="{{ $images->first()->url }}" alt="{{ $accommodation->name }}"
                             style="width:100%; height:420px; object-fit:cover; border-radius:14px;">
                    </a>
                </div>
                <div class="col-md-4">
                    <div class="row g-2">
                        @foreach ($thumbs as $i => $media)
                            @php
                                $isLast    = ($i === $thumbCount - 1);
                                $colClass  = ($isLast && $lastIsFull) ? 'col-12' : 'col-6';
                                // Heights: smaller in 2-col rows, taller when the
                                // odd one out goes full-width on its own row.
                                $imgHeight = ($colClass === 'col-12') ? '150px' : '130px';
                            @endphp
                            <div class="{{ $colClass }}">
                                <a href="{{ $media->url }}" data-gp-lightbox data-gp-index="{{ $i + 1 }}">
                                    <img src="{{ $media->url }}" alt=""
                                         style="width:100%; height:{{ $imgHeight }}; object-fit:cover; border-radius:10px;">
                                </a>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- Lightbox overlay (one per page, hidden until a thumbnail is tapped). --}}
            <div class="gp-lightbox" id="gp-lightbox" hidden aria-hidden="true" role="dialog" aria-modal="true" aria-label="Photo gallery">
                <button type="button" class="gp-lightbox__close" aria-label="Close gallery">&times;</button>
                <button type="button" class="gp-lightbox__nav gp-lightbox__nav--prev" aria-label="Previous photo">
                    <i class="fas fa-chevron-left"></i>
                </button>
                <div class="gp-lightbox__stage">
                    <img class="gp-lightbox__img" alt="" />
                </div>
                <button type="button" class="gp-lightbox__nav gp-lightbox__nav--next" aria-label="Next photo">
                    <i class="fas fa-chevron-right"></i>
                </button>
                <div class="gp-lightbox__counter" aria-live="polite"></div>
            </div>

            <style>
                .gp-lightbox {
                    position: fixed;
                    inset: 0;
                    z-index: 2000;
                    background: rgba(0, 0, 0, 0.92);
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    opacity: 0;
                    transition: opacity .2s ease;
                    touch-action: pan-y;
                }
                .gp-lightbox[data-shown="1"] { opacity: 1; }
                .gp-lightbox__stage {
                    flex: 1;
                    max-width: 100%;
                    max-height: 100%;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    padding: 64px 16px;
                    overflow: hidden;
                }
                .gp-lightbox__img {
                    max-width: 100%;
                    max-height: 100%;
                    object-fit: contain;
                    border-radius: 8px;
                    box-shadow: 0 12px 40px rgba(0, 0, 0, 0.5);
                    transition: transform .25s ease, opacity .15s ease;
                    user-select: none;
                    -webkit-user-drag: none;
                }
                .gp-lightbox__img.gp-lightbox__img--switching { opacity: 0; }

                .gp-lightbox__close,
                .gp-lightbox__nav {
                    position: absolute;
                    background: rgba(255, 255, 255, 0.12);
                    border: 1px solid rgba(255, 255, 255, 0.2);
                    color: #fff;
                    cursor: pointer;
                    transition: background .15s ease, transform .15s ease;
                    backdrop-filter: blur(8px);
                }
                .gp-lightbox__close:hover,
                .gp-lightbox__nav:hover { background: rgba(255, 255, 255, 0.22); }

                .gp-lightbox__close {
                    top: 16px;
                    right: 16px;
                    width: 44px;
                    height: 44px;
                    border-radius: 50%;
                    font-size: 1.6rem;
                    line-height: 1;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                }

                .gp-lightbox__nav {
                    top: 50%;
                    transform: translateY(-50%);
                    width: 52px;
                    height: 52px;
                    border-radius: 50%;
                    font-size: 1.1rem;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                }
                .gp-lightbox__nav--prev { left: 16px; }
                .gp-lightbox__nav--next { right: 16px; }

                .gp-lightbox__counter {
                    position: absolute;
                    bottom: 20px;
                    left: 50%;
                    transform: translateX(-50%);
                    color: #fff;
                    background: rgba(0, 0, 0, 0.4);
                    border-radius: 999px;
                    padding: 6px 14px;
                    font-size: .85rem;
                    font-family: 'Inter', system-ui, sans-serif;
                }

                @media (max-width: 575.98px) {
                    .gp-lightbox__stage { padding: 56px 8px; }
                    .gp-lightbox__nav {
                        width: 44px;
                        height: 44px;
                    }
                    .gp-lightbox__nav--prev { left: 8px; }
                    .gp-lightbox__nav--next { right: 8px; }
                }

                body.gp-lightbox-open { overflow: hidden; }
            </style>

            <script>
                (function () {
                    var gallery = document.querySelector('[data-gp-gallery]');
                    var box = document.getElementById('gp-lightbox');
                    if (!gallery || !box) return;

                    var links = Array.prototype.slice.call(gallery.querySelectorAll('[data-gp-lightbox]'));
                    var urls = links.map(function (a) { return a.getAttribute('href'); });
                    if (urls.length === 0) return;

                    var img       = box.querySelector('.gp-lightbox__img');
                    var counter   = box.querySelector('.gp-lightbox__counter');
                    var prevBtn   = box.querySelector('.gp-lightbox__nav--prev');
                    var nextBtn   = box.querySelector('.gp-lightbox__nav--next');
                    var closeBtn  = box.querySelector('.gp-lightbox__close');
                    var current   = 0;

                    function preload(i) {
                        if (i < 0 || i >= urls.length) return;
                        var im = new Image();
                        im.src = urls[i];
                    }

                    function show(i) {
                        if (i < 0) i = urls.length - 1;
                        if (i >= urls.length) i = 0;
                        current = i;
                        img.classList.add('gp-lightbox__img--switching');
                        // Tiny delay so the fade-out is visible.
                        setTimeout(function () {
                            img.src = urls[i];
                            img.onload = function () {
                                img.classList.remove('gp-lightbox__img--switching');
                            };
                        }, 80);
                        counter.textContent = (i + 1) + ' / ' + urls.length;
                        // Preload neighbours for snappy swipes.
                        preload(i + 1);
                        preload(i - 1);

                        var solo = urls.length <= 1;
                        prevBtn.style.display = solo ? 'none' : '';
                        nextBtn.style.display = solo ? 'none' : '';
                        counter.style.display = solo ? 'none' : '';
                    }

                    function open(i) {
                        box.hidden = false;
                        document.body.classList.add('gp-lightbox-open');
                        show(i);
                        requestAnimationFrame(function () {
                            box.setAttribute('data-shown', '1');
                            box.setAttribute('aria-hidden', 'false');
                        });
                    }

                    function close() {
                        box.removeAttribute('data-shown');
                        box.setAttribute('aria-hidden', 'true');
                        setTimeout(function () {
                            box.hidden = true;
                            document.body.classList.remove('gp-lightbox-open');
                        }, 200);
                    }

                    // Wire up thumbnail clicks. Each link has data-gp-index so
                    // clicking the big lead image opens at #0, fourth thumb at
                    // #4, hidden "more" link at #5+, etc.
                    links.forEach(function (a, fallbackIndex) {
                        a.addEventListener('click', function (e) {
                            e.preventDefault();
                            var idx = parseInt(a.getAttribute('data-gp-index'), 10);
                            if (isNaN(idx)) idx = fallbackIndex;
                            open(idx);
                        });
                    });

                    prevBtn.addEventListener('click', function () { show(current - 1); });
                    nextBtn.addEventListener('click', function () { show(current + 1); });
                    closeBtn.addEventListener('click', close);

                    // Click outside the image (on the dimmed backdrop) closes.
                    box.addEventListener('click', function (e) {
                        if (e.target === box || e.target.classList.contains('gp-lightbox__stage')) close();
                    });

                    // Keyboard nav.
                    document.addEventListener('keydown', function (e) {
                        if (box.hidden) return;
                        if (e.key === 'Escape')      close();
                        else if (e.key === 'ArrowRight') show(current + 1);
                        else if (e.key === 'ArrowLeft')  show(current - 1);
                    });

                    // Touch / swipe.
                    var startX = 0, startY = 0, tracking = false;
                    box.addEventListener('touchstart', function (e) {
                        if (e.touches.length !== 1) return;
                        startX = e.touches[0].clientX;
                        startY = e.touches[0].clientY;
                        tracking = true;
                    }, { passive: true });
                    box.addEventListener('touchend', function (e) {
                        if (!tracking) return;
                        tracking = false;
                        var t = e.changedTouches[0];
                        var dx = t.clientX - startX;
                        var dy = t.clientY - startY;
                        // Horizontal swipe only — vertical should pass through.
                        if (Math.abs(dx) > 50 && Math.abs(dx) > Math.abs(dy)) {
                            show(dx < 0 ? current + 1 : current - 1);
                        }
                    }, { passive: true });
                })();
            </script>
        @endif

        <div class="row g-4">
            <div class="col-lg-8">
                <h3 class="mb-3">About this stay</h3>
                <p class="lead">{{ $accommodation->description }}</p>

                @if (! empty($accommodation->amenities))
                    @php $amenitiesConfig = config('site.amenities'); @endphp
                    <h4 class="mt-5 mb-4">Amenities</h4>
                    <div class="gp-amenity-display">
                        @foreach ($accommodation->amenities as $key)
                            @if (isset($amenitiesConfig[$key]))
                                <div class="gp-amenity-tile">
                                    <i class="{{ $amenitiesConfig[$key]['icon'] }}"></i>
                                    <span>{{ $amenitiesConfig[$key]['label'] }}</span>
                                </div>
                            @endif
                        @endforeach
                    </div>
                @endif
            </div>

            <div class="col-lg-4">
                <div class="tt-sidebar-card" data-aos="fade-left">
                    @if ($accommodation->price_from)
                        <h4 style="font-weight:700;">{{ $accommodation->price_from }}</h4>
                        <p class="text-muted mb-3">Rates depend on season and occupancy.</p>
                    @endif

                    @php
                        $waNumber = config('site.whatsapp.number');
                        $msg = rawurlencode("Hello Golden Pangolin! I'd love to enquire about {$accommodation->name} ({$accommodation->location}).");
                    @endphp
                    <a href="https://wa.me/{{ $waNumber }}?text={{ $msg }}" target="_blank" rel="noopener"
                       class="btn-tt-primary w-100 mb-2"
                       style="background:#25D366; border-color:#25D366;">
                        <i class="fab fa-whatsapp me-1"></i> Enquire on WhatsApp
                    </a>
                    <a href="{{ route('contact') }}" class="btn-tt-outline w-100">
                        <i class="fas fa-envelope me-1"></i> Contact Us
                    </a>
                </div>
            </div>
        </div>

        @if ($related->isNotEmpty())
            <h3 class="mt-5 mb-4">You might also like</h3>
            <div class="tt-dest-grid">
                @foreach ($related as $rel)
                    <article class="tt-dest-card" data-aos="fade-up">
                        <div class="tt-dest-card-img">
                            @if ($rel->cover_url)
                                <img src="{{ $rel->cover_url }}" alt="{{ $rel->name }}" loading="lazy">
                            @endif
                        </div>
                        <div class="tt-dest-card-body">
                            <h3 class="tt-dest-card-title">
                                <a href="{{ route('accommodation.show', $rel) }}">{{ $rel->name }}</a>
                            </h3>
                            <p class="tt-dest-card-desc">{{ Str::limit($rel->description, 100) }}</p>
                            <a href="{{ route('accommodation.show', $rel) }}" class="tt-dest-card-link">
                                View Accommodation <i class="fas fa-arrow-right"></i>
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
