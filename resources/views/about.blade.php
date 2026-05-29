@extends('layouts.front')

@section('seo')
    <x-seo title="About"
           description="Born and raised in Zimbabwe, Golden Pangolin shares our homeland's landscapes, culture, and hidden treasures with travellers from across the globe." />
@endsection

@section('page')
@include('partials.navbar')

<!-- Page Hero -->
<section class="tt-page-hero gp-pangolin-bg">
	<div class="tt-page-hero-bg" style="background-image: url('{{ asset('images/about.jpg') }}');"></div>
	<div class="container" data-aos="fade-up">
		<nav aria-label="breadcrumb">
			<ol class="breadcrumb justify-content-center">
				<li class="breadcrumb-item"><a href="{{ url('/') }}"><i class="fas fa-home me-1"></i>Home</a></li>
				<li class="breadcrumb-item active">About</li>
			</ol>
		</nav>
		<h1 class="tt-page-title">Discover Zimbabwe with <span class="accent">Local Experts</span></h1>
		<p class="tt-page-subtitle">
			Born and raised in Zimbabwe, we share our homeland’s breathtaking landscapes, vibrant culture, and hidden treasures with passionate travelers from across the globe.
		</p>
	</div>
</section>

<!-- Our Story -->
<section class="tt-section">
	<div class="container">
		<div class="row align-items-center g-5">
			<div class="col-lg-6" data-aos="fade-right">
				
				<h2 class="tt-title mb-3">Our <span class="accent">Story</span></h2>
				<p>
					At the edge of Victoria Falls, where mist rises to the heavens and the earth trembles with thunder, Golden Pangolin was born.
				</p>
				<p>
					<strong>Golden</strong> is the glow of sunsets over the Zambezi and the promise of something rare and unforgettable. <strong>Pangolin</strong>, one of Africa's most elusive creatures, stands for rarity and protection — just like our journeys: carefully curated, respectful of nature, and designed to reveal what few ever see.
				</p>
				<p>
					We are storytellers, pathfinders, and custodians of authentic African adventure. From the raw power of the Falls to sunset cruises on the Zambezi and hidden gems across Zimbabwe and beyond, every journey is crafted with care and precision.
				</p>
				<p>
					Golden Pangolin is for those who seek meaning, wonder, and the extraordinary.
				</p>
				
			</div>
			<div class="col-lg-6" data-aos="fade-left">
				<div class="tt-about-images">
					<img src="{{ asset('images/about.jpg') }}" alt="Zimbabwe Safari" class="img-fluid rounded-3 shadow">
				</div>
			</div>
		</div>
	</div>
</section>

<!-- Mission & Vision -->
<section class="tt-section tt-section-light">
	<div class="container">
		<div class="row g-4">
			<div class="col-lg-6" data-aos="fade-up">
				<div class="tt-mission-card">
					<div class="icon"><i class="fas fa-compass"></i></div>
					<h3>Our Mission</h3>
					<p>
						To showcase Zimbabwe’s natural wonders and rich cultural heritage through authentic, sustainable tourism experiences that uplift local communities while creating unforgettable memories for our guests.
					</p>
				</div>
			</div>
			<div class="col-lg-6" data-aos="fade-up" data-aos-delay="100">
				<div class="tt-mission-card">
					<div class="icon"><i class="fas fa-eye"></i></div>
					<h3>Our Vision</h3>
					<p>
						To be Zimbabwe’s most trusted and respected travel agency, celebrated for delivering exceptional experiences that inspire conservation, cultural appreciation, and meaningful connections between visitors and our homeland.
					</p>
				</div>
			</div>
		</div>
	</div>
</section>

<!-- Why Choose Us -->
<section class="tt-section">
	<div class="container">
		<div class="tt-section-header text-center" data-aos="fade-up">
			<div class="tt-pretitle">What Makes Us Different</div>
			<h2 class="tt-title">Why Choose <span class="accent">Golden Pangolin</span></h2>
			<p class="tt-subtitle">As local Zimbabweans, we offer authentic experiences you won't find anywhere else.</p>
		</div>
		<div class="row g-4">
			<div class="col-md-6 col-lg-4" data-aos="fade-up">
				<div class="tt-feature-card">
					<div class="icon"><i class="fas fa-hiking"></i></div>
					<h4>Authentic Adventures</h4>
					<p>Experience Zimbabwe through local eyes with authentic cultural immersion and off-the-beaten-path adventures.</p>
				</div>
			</div>
			<div class="col-md-6 col-lg-4" data-aos="fade-up" data-aos-delay="100">
				<div class="tt-feature-card">
					<div class="icon"><i class="fas fa-route"></i></div>
					<h4>Local Expertise</h4>
					<p>Benefit from our deep local knowledge and connections to access exclusive locations and experiences.</p>
				</div>
			</div>
				
		
			<div class="col-md-6 col-lg-4" data-aos="fade-up" data-aos-delay="300">
				<div class="tt-feature-card">
					<div class="icon"><i class="fas fa-star"></i></div>
					<h4>Personalized Service</h4>
					<p>Every journey is tailored to your preferences and interests, creating memories that last a lifetime.</p>
				</div>
			</div>
		</div>
	</div>
</section>

<!-- Values -->
<section class="tt-section tt-section-light">
	<div class="container">
		<div class="tt-section-header text-center" data-aos="fade-up">
			<div class="tt-pretitle">What We Stand For</div>
			<h2 class="tt-title">Our Core <span class="accent">Values</span></h2>
		</div>
		<div class="row g-4 justify-content-center">
			<div class="col-md-4" data-aos="zoom-in">
				<div class="tt-value-card text-center">
					<div class="icon"><i class="fas fa-leaf"></i></div>
					<h4>Sustainability</h4>
					<p>We're committed to protecting Zimbabwe's natural beauty and wildlife for future generations through responsible tourism.</p>
				</div>
			</div>
			<div class="col-md-4" data-aos="zoom-in" data-aos-delay="100">
				<div class="tt-value-card text-center">
					<div class="icon"><i class="fas fa-handshake"></i></div>
					<h4>Authenticity</h4>
					<p>Every experience we offer is genuine, connecting you with real Zimbabwean culture, traditions, and stories.</p>
				</div>
			</div>
			<div class="col-md-4" data-aos="zoom-in" data-aos-delay="200">
				<div class="tt-value-card text-center">
					<div class="icon"><i class="fas fa-users"></i></div>
					<h4>Community</h4>
					<p>We partner with local communities, ensuring tourism benefits everyone while preserving cultural heritage.</p>
				</div>
			</div>
		</div>
	</div>
</section>



<!-- CTA -->
<section class="tt-cta" data-aos="zoom-in">
	<div class="container text-center">
		<i class="fas fa-compass fa-3x mb-3" style="color: #fff;"></i>
		<h2>Ready to Explore Zimbabwe?</h2>
		<p>Let our local expertise guide you through an authentic Zimbabwean adventure.</p>
		<div class="d-flex gap-3 justify-content-center flex-wrap">
			<a href="{{ route('packages') }}" class="btn-tt-white"><i class="fas fa-globe-africa me-2"></i> View Destinations</a>
			<a href="{{ route('contact') }}" class="btn-tt-outline" style="border-color:white;color:white;"><i class="fas fa-phone me-2"></i> Plan My Trip</a>
		</div>
	</div>
</section>

@include('partials.footer')
@endsection