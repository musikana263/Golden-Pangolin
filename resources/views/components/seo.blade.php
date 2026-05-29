@props([
    // Page name only — the component prepends "Golden Pangolin — " automatically.
    // Pass `false` to use the plain brand title (home page only).
    'title' => null,
    'description' => 'Curated travel packages across Zimbabwe, Botswana, and Zambia. Wildlife safaris, waterfall adventures, and cultural journeys with local Zimbabwean experts.',
    'image' => null,
    'type' => 'website',
    'url' => null,
])

@php
    $defaultTitle = 'Golden Pangolin — Tours, Safaris & Accommodation in Southern Africa';
    $pageTitle = $title === null || $title === false
        ? $defaultTitle
        : 'Golden Pangolin — '.$title;
    $shareImage = $image ?: asset('images/about.jpg');
    $canonical = $url ?: url()->current();
@endphp

<!-- Primary Meta Tags -->
<title>{{ $pageTitle }}</title>
<meta name="title" content="{{ $pageTitle }}">
<meta name="description" content="{{ $description }}">
<link rel="canonical" href="{{ $canonical }}">

<!-- Open Graph / Facebook -->
<meta property="og:type" content="{{ $type }}">
<meta property="og:url" content="{{ $canonical }}">
<meta property="og:title" content="{{ $pageTitle }}">
<meta property="og:description" content="{{ $description }}">
<meta property="og:image" content="{{ $shareImage }}">
<meta property="og:site_name" content="Golden Pangolin">
<meta property="og:locale" content="en_US">

<!-- Twitter -->
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:url" content="{{ $canonical }}">
<meta name="twitter:title" content="{{ $pageTitle }}">
<meta name="twitter:description" content="{{ $description }}">
<meta name="twitter:image" content="{{ $shareImage }}">

<!-- Additional SEO -->
<meta name="robots" content="index, follow">
<meta name="author" content="Golden Pangolin">
<meta name="theme-color" content="#1a1208">

<!-- JSON-LD Structured Data (TravelAgency on the home page only) -->
@if ($type === 'website')
<script type="application/ld+json">
{
    "@context": "https://schema.org",
    "@type": "TravelAgency",
    "name": "Golden Pangolin",
    "description": "{{ $description }}",
    "url": "{{ url('/') }}",
    "logo": "{{ asset('images/logo.png') }}",
    "areaServed": [
        { "@type": "Country", "name": "Zimbabwe" },
        { "@type": "Country", "name": "Botswana" },
        { "@type": "Country", "name": "Zambia" }
    ],
    "contactPoint": {
        "@type": "ContactPoint",
        "telephone": "+{{ site_whatsapp_number() }}",
        "contactType": "customer service",
        "availableLanguage": ["English"]
    },
    "sameAs": [
        "https://www.facebook.com/share/1Xpkia2ZNN/",
        "https://www.instagram.com/goldenpangolin2026",
        "https://www.tiktok.com/@golden.pangolin"
    ]
}
</script>
@endif
