@extends('layouts.front')

@section('seo')
    <x-seo title="Blog"
           description="Travel stories, planning tips, and on-the-ground insights from our team across Zimbabwe, Botswana, and Zambia." />
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
				<li class="breadcrumb-item active">Blog</li>
			</ol>
		</nav>
		<h1 class="tt-page-title">Zimbabwe Travel <span class="accent">Stories</span></h1>
		<p class="tt-page-subtitle">
			Discover insider tips, travel stories, and hidden gems across Zimbabwe, Botswana, and Zambia.
		</p>
	</div>
</section>

<!-- Blog Listing -->
<section class="tt-section">
	<div class="container">
		<div class="tt-section-header text-center" data-aos="fade-up">
			<div class="tt-pretitle">Latest from Our Blog</div>
			<h2 class="tt-title">Stories & <span class="accent">Insights</span></h2>
			<p class="tt-subtitle">Get inspired by authentic travel experiences and expert tips from our Zimbabwe adventures.</p>
		</div>

		<div class="tt-blog-grid">
			@foreach ($blogs as $blog)
			<article class="tt-blog-card" data-aos="fade-up" data-aos-delay="{{ ($loop->iteration % 3) * 100 }}">
				<div class="tt-blog-card-img">
					<img src="{{ $blog->image_url }}" alt="{{ $blog->title }}" loading="lazy"
					     onerror="this.onerror=null; this.src='{{ asset('images/about.jpg') }}';">
					<span class="badge-cat">{{ $blog->category->name ?? 'Travel' }}</span>
					<span class="badge-read"><i class="fas fa-clock me-1"></i>5 min</span>
				</div>
				<div class="tt-blog-card-body">
					<div class="tt-blog-card-meta">
						<span><i class="fas fa-calendar-alt"></i> {{ optional($blog->published_at ? \Carbon\Carbon::parse($blog->published_at) : $blog->created_at)->format('M d, Y \a\t H:i') ?? 'Recent' }}</span>
						<span><i class="fas fa-user"></i> Golden Pangolin Team</span>
					</div>
					<h3 class="tt-blog-card-title">
						<a href="#">{{ $blog->title }}</a>
					</h3>
					<p class="tt-blog-card-desc">
						{{ Str::limit($blog->description ?? 'Discover amazing travel experiences and insights that will inspire your next Zimbabwe adventure.', 150) }}
					</p>
					<a href="#" class="tt-blog-card-link">Read More <i class="fas fa-arrow-right"></i></a>
				</div>
			</article>
			@endforeach
		</div>

		<div class="tt-pagination mt-5 d-flex justify-content-center" data-aos="fade-up">
			{{ $blogs->links('pagination::bootstrap-4') }}
		</div>
	</div>
</section>

<!-- Newsletter -->
<section class="tt-newsletter" data-aos="zoom-in">
	<div class="container">
		<div class="tt-newsletter-inner">
			<div class="tt-newsletter-icon"><i class="fas fa-envelope-open-text"></i></div>
			<h2>Stay Updated with Zimbabwean Travel Tips</h2>
			<p>Get the latest travel stories, tips, and exclusive offers delivered to your inbox.</p>
			<form class="tt-newsletter-form" method="POST" action="#">
				@csrf
				<div class="tt-newsletter-input-group">
					<input type="email" placeholder="Enter your email address" required>
					<button type="submit" class="btn-tt-primary">Subscribe <i class="fas fa-paper-plane ms-1"></i></button>
				</div>
			</form>
			<small class="text-muted mt-2 d-block">We respect your privacy. Unsubscribe at any time.</small>
		</div>
	</div>
</section>

@include('partials.footer')
@endsection