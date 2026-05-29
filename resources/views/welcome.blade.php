@extends('layouts.front')

@section('seo')
    <x-seo />
@endsection

@section('page')
@include('partials.navbar')

<!-- Hero -->
<section class="tt-hero gp-pangolin-bg">
	<div class="tt-hero-bg"></div>
	<div class="container tt-hero-content">
		<div class="row align-items-center">
			<div class="col-lg-7" data-aos="fade-right">
				<div class="tt-badge">
					<i class="fas fa-leaf"></i>
					<span>Authentic Southern African Experiences</span>
				</div>

				<h1 class="tt-hero-title">
					Discover the Soul of <span class="accent">Southern Africa</span>
				</h1>

				<p class="tt-hero-text">
					Embark on extraordinary journeys through Zimbabwe's untamed wilderness and vibrant cultures. Where every moment becomes a treasured memory.
				</p>

				<div class="tt-hero-actions">
					<a href="{{ route('tours.index') }}" class="btn-tt-accent">
						Browse Packages <i class="fas fa-arrow-right"></i>
					</a>
					<a href="{{ route('contact') }}" class="btn-tt-outline-white">
						Plan With Us <i class="fas fa-compass"></i>
					</a>
				</div>
			</div>

			@if ($heroPackage)
			<div class="col-lg-5 d-none d-lg-block" data-aos="fade-left" data-aos-delay="200">
				<div class="tt-hero-card" style="position:relative;">
					<div class="gp-wishlist-overlay" style="position:absolute; top:12px; right:12px; z-index:5;">
						@include('partials.wishlist-button', ['item' => $heroPackage, 'type' => 'package'])
					</div>
					<img src="{{ $heroPackage->cover_url ?? asset('images/about.jpg') }}"
					     alt="{{ $heroPackage->name }}" loading="eager"
					     fetchpriority="high" width="520" height="360" decoding="async">
					<div class="tt-hero-card-body">
						<span class="card-badge">
							{{ $heroPackage->is_hero ? 'Popular Choice' : 'Featured' }}
						</span>
						<h4>{{ $heroPackage->name }}</h4>
						<div class="tt-hero-card-meta">
							@if ($heroPackage->duration)
								<span><i class="fas fa-clock"></i> {{ $heroPackage->duration }}</span>
							@endif
							@if ($heroPackage->group_size)
								<span><i class="fas fa-users"></i> {{ $heroPackage->group_size }}</span>
							@endif
						</div>
						<div class="tt-hero-card-footer">
							<div>
								@if ($heroPackage->price_from)
									<div class="price-value">{{ $heroPackage->price_from }}</div>
								@endif
							</div>
							<a href="{{ route('tours.show', $heroPackage) }}" class="tt-hero-card-link">
								View Details <i class="fas fa-arrow-right"></i>
							</a>
						</div>
					</div>
				</div>
			</div>
			@endif
		</div>
	</div>

	<div class="tt-hero-scroll">
		<span>Scroll to Explore</span>
		<i class="fas fa-chevron-down"></i>
	</div>
</section>

{{-- Mobile-only "Popular Choice" section. On desktop the hero card lives
     inside the hero on the right; on mobile that column is hidden, so we
     surface the same package here as its own dedicated section. --}}
@if ($heroPackage)
<section class="tt-section-sm gp-popular-mobile d-lg-none">
	<div class="container">
		<div class="tt-section-header text-center mb-3" data-aos="fade-up">
			<div class="tt-pretitle"><i class="fas fa-star me-1"></i> Popular Choice</div>
			<h2 class="tt-title mb-0">{{ $heroPackage->name }}</h2>
		</div>

		<article class="tt-dest-card gp-popular-mobile__card" data-aos="fade-up">
			<div class="tt-dest-card-img" style="position:relative;">
				<img src="{{ $heroPackage->cover_url ?? asset('images/about.jpg') }}"
				     alt="{{ $heroPackage->name }}" loading="eager"
				     fetchpriority="high" width="480" height="280" decoding="async">
				<span class="card-badge gp-popular-mobile__badge">
					{{ $heroPackage->is_hero ? 'Popular Choice' : 'Featured' }}
				</span>
				<div class="gp-wishlist-overlay">
					@include('partials.wishlist-button', ['item' => $heroPackage, 'type' => 'package'])
				</div>
			</div>
			<div class="tt-dest-card-body">
				@if ($heroPackage->summary)
					<p class="tt-dest-card-desc">{{ Str::limit($heroPackage->summary, 140) }}</p>
				@endif
				<div class="tt-dest-card-meta mb-2">
					@if ($heroPackage->duration)
						<span><i class="fas fa-clock"></i> {{ $heroPackage->duration }}</span>
					@endif
					@if ($heroPackage->group_size)
						<span><i class="fas fa-users"></i> {{ $heroPackage->group_size }}</span>
					@endif
				</div>
				@if ($heroPackage->price_from)
					<div class="mb-2"><strong>{{ $heroPackage->price_from }}</strong></div>
				@endif
				<a href="{{ route('tours.show', $heroPackage) }}" class="tt-dest-card-link">
					View Details <i class="fas fa-arrow-right"></i>
				</a>
			</div>
		</article>
	</div>
</section>

<style>
	.gp-popular-mobile__card { max-width: 480px; margin: 0 auto; }
	.gp-popular-mobile__badge {
		position: absolute;
		top: .75rem;
		left: .75rem;
		background: linear-gradient(135deg, #d4a03c, #b8872e);
		color: #1a1208;
		font-size: .7rem;
		font-weight: 700;
		text-transform: uppercase;
		letter-spacing: .05em;
		padding: .35rem .85rem;
		border-radius: 50rem;
		box-shadow: 0 4px 12px rgba(184, 135, 46, .35);
	}
	.gp-popular-mobile .tt-pretitle i { color: var(--tt-accent); }
</style>
@endif

<!-- Featured Destinations -->
<section class="tt-section">
	<div class="container">
		<div class="tt-section-header text-center" data-aos="fade-up">
			<div class="tt-pretitle">Handpicked Experiences</div>
			<h2 class="tt-title">Featured <span class="accent">Destinations</span></h2>
			<p class="tt-subtitle">
				Explore Africa's most captivating locations, curated by our local experts
				who know every hidden gem and breathtaking vista.
			</p>
		</div>

		<div class="tt-dest-grid">
			@foreach ($destinations as $destination)
			<article class="tt-dest-card" data-aos="fade-up" data-aos-delay="{{ $loop->iteration * 100 }}">
				<div class="tt-dest-card-img">
					<img src="{{ $destination->image_url }}"
						 alt="{{ $destination->title }}" loading="lazy"
						 width="600" height="220" decoding="async">
				</div>
				<div class="tt-dest-card-body">
					<div class="tt-dest-card-meta">
						<span><i class="fas fa-map-marker-alt"></i> {{ $destination->country ?? 'Zimbabwe' }}</span>
					</div>
					<h3 class="tt-dest-card-title">
						<a href="{{ route('desti.show', $destination->id) }}">{{ $destination->title }}</a>
					</h3>
					<p class="tt-dest-card-desc">{{ Str::limit($destination->description, 110) }}</p>
					<div class="tt-dest-card-footer">
						<a href="{{ route('desti.show', $destination->id) }}" class="tt-dest-card-link">
							Explore <i class="fas fa-arrow-right"></i>
						</a>
					</div>
				</div>
			</article>
			@endforeach
		</div>

		<div class="text-center mt-5" data-aos="fade-up">
			<a href="{{ route('packages') }}" class="btn-tt-primary">
				View All Destinations <i class="fas fa-globe-africa"></i>
			</a>
		</div>
	</div>
</section>

@if ($featuredTours->isNotEmpty())
<!-- Curated Packages -->
<section class="tt-section tt-section-light">
	<div class="container">
		<div class="tt-section-header text-center" data-aos="fade-up">
			<div class="tt-pretitle">Multi-day Itineraries</div>
			<h2 class="tt-title">Curated <span class="accent">Packages</span></h2>
			<p class="tt-subtitle">
				Hand-built journeys across Zimbabwe, Botswana, and Zambia — itineraries, transfers, and accommodation already sorted.
			</p>
		</div>

		<div class="tt-dest-grid">
			@foreach ($featuredTours as $tour)
				<article class="tt-dest-card" data-aos="fade-up" data-aos-delay="{{ $loop->iteration * 80 }}">
					<div class="tt-dest-card-img" style="position:relative;">
						@if ($tour->cover_url)
							<img src="{{ $tour->cover_url }}" alt="{{ $tour->name }}" loading="lazy" width="600" height="220" decoding="async">
						@endif
						@if ($tour->is_featured)
							<span class="card-badge" style="position:absolute; top:12px; left:12px;">
								<i class="fas fa-star me-1"></i> Featured
							</span>
						@endif
						<div class="gp-wishlist-overlay">
							@include('partials.wishlist-button', ['item' => $tour, 'type' => 'package'])
						</div>
					</div>
					<div class="tt-dest-card-body">
						<div class="tt-dest-card-meta">
							<span><i class="fas fa-globe-africa"></i> {{ $tour->country }}</span>
							@if ($tour->duration)<span><i class="fas fa-clock"></i> {{ $tour->duration }}</span>@endif
						</div>
						<h3 class="tt-dest-card-title">
							<a href="{{ route('tours.show', $tour) }}">{{ $tour->name }}</a>
						</h3>
						<p class="tt-dest-card-desc">{{ Str::limit($tour->summary, 100) }}</p>
						@if ($tour->price_from)
							<div class="mb-2"><strong>{{ $tour->price_from }}</strong></div>
						@endif
						<a href="{{ route('tours.show', $tour) }}" class="tt-dest-card-link">
							View Package <i class="fas fa-arrow-right"></i>
						</a>
					</div>
				</article>
			@endforeach
		</div>

		<div class="text-center mt-5" data-aos="fade-up">
			<a href="{{ route('tours.index') }}" class="btn-tt-primary">
				Browse All Packages <i class="fas fa-suitcase-rolling"></i>
			</a>
		</div>
	</div>
</section>
@endif

@if ($featuredStays->isNotEmpty())
<!-- Our Accommodation -->
<section class="tt-section">
	<div class="container">
		<div class="tt-section-header text-center" data-aos="fade-up">
			<div class="tt-pretitle">Where You'll Sleep</div>
			<h2 class="tt-title">Hand-Picked <span class="accent">Accommodation</span></h2>
			<p class="tt-subtitle">
				From luxury safari lodges to riverside tented camps — accommodation we've stayed at ourselves and trust.
			</p>
		</div>

		<div class="tt-dest-grid">
			@foreach ($featuredStays as $stay)
				<article class="tt-dest-card" data-aos="fade-up" data-aos-delay="{{ $loop->iteration * 80 }}">
					<div class="tt-dest-card-img" style="position:relative;">
						@if ($stay->cover_url)
							<img src="{{ $stay->cover_url }}" alt="{{ $stay->name }}" loading="lazy" width="600" height="220" decoding="async">
						@endif
						@if ($stay->type)
							<span class="card-badge" style="position:absolute; top:12px; left:12px;">
								@if ($stay->type->icon)<i class="{{ $stay->type->icon }} me-1"></i>@endif
								{{ $stay->type->name }}
							</span>
						@endif
						<div class="gp-wishlist-overlay">
							@include("partials.wishlist-button", ["item" => $stay, "type" => "accommodation"])
						</div>
					</div>
					<div class="tt-dest-card-body">
						<div class="tt-dest-card-meta">
							<span><i class="fas fa-map-marker-alt"></i> {{ $stay->location ?: $stay->country }}</span>
						</div>
						<h3 class="tt-dest-card-title">
							<a href="{{ route('accommodation.show', $stay) }}">{{ $stay->name }}</a>
						</h3>
						<p class="tt-dest-card-desc">{{ Str::limit($stay->description, 100) }}</p>
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

		<div class="text-center mt-5" data-aos="fade-up">
			<a href="{{ route('accommodation.index') }}" class="btn-tt-primary">
				Browse All Accommodation <i class="fas fa-bed"></i>
			</a>
		</div>
	</div>
</section>
@endif

<!-- Why Choose Us -->
<section class="tt-section tt-section-light">
	<div class="container">
		<div class="row align-items-center g-5">
			<div class="col-lg-6" data-aos="fade-right">
				<div class="tt-features-img">
					<img src="{{ asset('images/about.jpg') }}" alt="Zimbabwean Wildlife">
					
				</div>
			</div>

			<div class="col-lg-6" data-aos="fade-left">
				<div class="tt-section-header">
					<div class="tt-pretitle">Why Choose Golden Pangolin</div>
					<h2 class="tt-title">Experience Zimbabwe with <span class="accent">Local Experts</span></h2>
					<p class="tt-subtitle">
						As proud Zimbabweans, we don't just show you destinations — we share our homeland with you.
					</p>
				</div>

				<div class="tt-feature-item">
					<div class="tt-feature-icon"><i class="fas fa-map-marked-alt"></i></div>
					<div>
						<h4>Local Expertise</h4>
						<p>Born and raised in Zimbabwe, we know every hidden gem, cultural nuance, and breathtaking vista.</p>
					</div>
				</div>
				
				<div class="tt-feature-item">
					<div class="tt-feature-icon"><i class="fas fa-leaf"></i></div>
					<div>
						<h4>Sustainable Tourism</h4>
						<p>We partner with local communities and support conservation efforts across Zimbabwe.</p>
					</div>
				</div>
				<div class="tt-feature-item">
					<div class="tt-feature-icon"><i class="fas fa-star"></i></div>
					<div>
						<h4>Personalized Service</h4>
						<p>Every journey is tailored to your preferences, creating memories that last a lifetime.</p>
					</div>
				</div>
			</div>
		</div>
	</div>
</section>


<!-- CTA -->
<section class="tt-cta gp-pangolin-bg">
	<div class="container" data-aos="zoom-in">
		<div class="icon-lg"><i class="fas fa-paper-plane"></i></div>
		<h2>Ready to Begin Your Southern African Adventure?</h2>
		<p>
			Let our local experts craft your perfect journey. From wildlife safaris in Hwange and Chobe
			to the thunder of Victoria Falls — your dream Southern African experience awaits.
		</p>
		<div class="tt-cta-actions">
			<a href="{{ route('packages') }}" class="btn-tt-white">
				Explore Destinations <i class="fas fa-compass"></i>
			</a>
			<a href="{{ route('contact') }}" class="btn-tt-outline-white">
				Contact Us <i class="fas fa-phone"></i>
			</a>
		</div>
	</div>
</section>

@include('partials.footer')
@endsection
