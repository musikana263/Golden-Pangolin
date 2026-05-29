@extends('layouts.front')

@section('seo')
    <x-seo title="Destinations"
           description="Explore every destination Golden Pangolin curates across Zimbabwe, Botswana, and Zambia — from Victoria Falls and Hwange to Chobe and the Zambezi." />
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
				<li class="breadcrumb-item active">Destinations</li>
			</ol>
		</nav>
		<h1 class="tt-page-title">Discover Zimbabwe's <span class="accent">Wonders</span></h1>
		<p class="tt-page-subtitle">
			From the majestic plains of Hwange National Park to the breathtaking waters of Lake Kariba and the iconic Victoria Falls, 
explore our handpicked destinations that showcase Zimbabwe's natural beauty.

		</p>
	</div>
</section>

<!-- Destinations Listing -->
<section class="tt-section">
	<div class="container">
		<div class="tt-section-header text-center" data-aos="fade-up">
			<div class="tt-pretitle">Southern Africa</div>
			<h2 class="tt-title">
				@if(request('search'))
					Results for "<span class="accent">{{ request('search') }}</span>"
				@else
					All <span class="accent">Destinations</span>
				@endif
			</h2>
			<p class="tt-subtitle">
				Iconic places across Zimbabwe, Botswana, and Zambia — pick one to see the tours and stays we've built around it.
			</p>

			<form action="{{ route('packages') }}" method="GET" class="d-flex justify-content-center mt-4">
				<div class="input-group" style="max-width:520px;">
					<input type="text" class="tt-input form-control" name="search"
						   placeholder="Search a place — e.g. Victoria Falls" value="{{ request('search') }}">
					<button type="submit" class="btn-tt-primary">
						<i class="fas fa-search"></i>
					</button>
				</div>
			</form>
		</div>

		@if($destinations->count() > 0)
		<div class="tt-dest-grid">
			@foreach ($destinations as $destination)
			<article class="tt-dest-card" data-aos="fade-up" data-aos-delay="{{ ($loop->iteration % 3) * 100 }}">
				<div class="tt-dest-card-img">
					<img src="{{ $destination->image_url }}"
						 alt="{{ $destination->title }}" loading="lazy">
				</div>
				<div class="tt-dest-card-body">
					<div class="tt-dest-card-meta">
						<span><i class="fas fa-map-marker-alt"></i> {{ $destination->country ?? 'Zimbabwe' }}</span>
					</div>
					<h3 class="tt-dest-card-title">
						<a href="{{ route('desti.show', $destination->id) }}">{{ $destination->title }}</a>
					</h3>
					<p class="tt-dest-card-desc">{{ Str::limit($destination->description, 120) }}</p>
					<div class="tt-dest-card-footer">
						<a href="{{ route('desti.show', $destination->id) }}" class="tt-dest-card-link">
							Explore <i class="fas fa-arrow-right"></i>
						</a>
					</div>
				</div>
			</article>
			@endforeach
		</div>

		<div class="tt-pagination mt-5 d-flex justify-content-center" data-aos="fade-up">
			{{ $destinations->appends(request()->query())->links('pagination::bootstrap-4') }}
		</div>
		@else
		<div class="tt-empty-state" data-aos="fade-up">
			<div class="icon"><i class="fas fa-search"></i></div>
			<h3>No Destinations Found</h3>
			<p>We couldn't find any destinations matching your search criteria. Try adjusting your filters.</p>
			<a href="{{ route('packages') }}" class="btn-tt-primary">View All Destinations</a>
		</div>
		@endif
	</div>
</section>

@include('partials.footer')
@endsection
