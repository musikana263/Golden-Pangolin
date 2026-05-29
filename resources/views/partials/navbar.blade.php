@php
    // Admins viewing the public site don't need traveller-tier pricing or a
    // visitor wishlist — those are for guests planning a trip. Hide both.
    $isAdminViewing = auth()->check() && auth()->user()->isAdmin();
@endphp
<!-- Navbar -->
<nav class="navbar navbar-expand-lg tt-navbar fixed-top">
	<div class="container-fluid px-3 px-lg-5 flex-nowrap">
		<a class="navbar-brand gp-brand d-flex align-items-center me-auto" href="{{ url('/') }}">
			<span class="gp-brand-mark">
				<img src="{{ asset('images/logo.png') }}" alt="Golden Pangolin">
			</span>
			<span class="gp-brand-text">Golden <span class="brand-accent">Pangolin</span></span>
		</a>

		{{-- Quick-actions capsule (tier · wishlist · theme toggle) — MOBILE ONLY.
		     On laptop we use the classic right-side controls inside the menu
		     (further down). Tier + wishlist hidden for admins. --}}
		<div class="gp-quick-actions d-lg-none">
			@if (! empty($travellerTierOptions) && ! $isAdminViewing)
				<div class="dropdown gp-qa-tier-wrap">
					<button type="button" class="gp-qa-btn gp-qa-btn--tier"
							data-bs-toggle="dropdown" aria-expanded="false"
							aria-label="Change travelling-from preference"
							title="Travelling from: {{ $travellerTierLabel ?? 'International visitor' }}">
						<i class="fas fa-earth-africa"></i>
					</button>
					<ul class="dropdown-menu dropdown-menu-end gp-tier-indicator__menu">
						<li class="gp-tier-indicator__heading">I'm travelling from</li>
						@foreach ($travellerTierOptions as $value => $label)
							<li>
								<button type="button" class="dropdown-item gp-tier-indicator__option {{ ($travellerTier ?? null) === $value ? 'is-active' : '' }}"
										data-gp-set-tier="{{ $value }}">
									@if ($value === 'local') <i class="fas fa-home me-2"></i>
									@elseif ($value === 'african') <i class="fas fa-globe-africa me-2"></i>
									@else <i class="fas fa-plane me-2"></i>
									@endif
									{{ $label }}
									@if (($travellerTier ?? null) === $value)
										<i class="fas fa-check ms-auto" style="color:var(--tt-accent)"></i>
									@endif
								</button>
							</li>
						@endforeach
					</ul>
				</div>
				<span class="gp-qa-divider" aria-hidden="true"></span>
			@endif
			@unless ($isAdminViewing)
				<a href="{{ route('wishlist.index') }}" class="gp-qa-btn gp-qa-btn--heart" aria-label="View wishlist">
					<i class="fas fa-heart"></i>
					@if (($wishlistCount ?? 0) > 0)
						<span class="gp-wishlist-badge">{{ $wishlistCount }}</span>
					@endif
				</a>
				<span class="gp-qa-divider" aria-hidden="true"></span>
			@endunless
			<button type="button" class="gp-qa-btn gp-qa-btn--theme" data-gp-theme-toggle
					aria-label="Toggle dark mode" title="Toggle dark mode">
				<i class="fas fa-moon gp-icon-moon"></i>
				<i class="fas fa-sun gp-icon-sun"></i>
			</button>
			@if ($isAdminViewing)
				<span class="gp-qa-divider" aria-hidden="true"></span>
				<a href="{{ route('home') }}" class="gp-qa-btn gp-qa-btn--admin" aria-label="Go to admin dashboard" title="Admin dashboard">
					<i class="fas fa-shield-halved"></i>
				</a>
			@endif
		</div>

		<button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#ttNav"
				aria-controls="ttNav" aria-expanded="false" aria-label="Toggle navigation">
			<span class="navbar-toggler-icon"></span>
		</button>

		<div class="collapse navbar-collapse" id="ttNav">
			<ul class="navbar-nav mx-auto">
				<li class="nav-item">
					<a class="nav-link {{ request()->is('/') ? 'active' : '' }}" href="{{ url('/') }}">Home</a>
				</li>
				<li class="nav-item">
					<a class="nav-link {{ request()->is('packages*') ? 'active' : '' }}" href="{{ route('packages') }}">Destinations</a>
				</li>
				<li class="nav-item">
					<a class="nav-link {{ request()->is('tours*') ? 'active' : '' }}" href="{{ route('tours.index') }}">Packages</a>
				</li>
				<li class="nav-item">
					<a class="nav-link {{ request()->is('accommodation*') || request()->is('stay*') ? 'active' : '' }}" href="{{ route('accommodation.index') }}">Accommodation</a>
				</li>
				<li class="nav-item">
					<a class="nav-link {{ request()->is('blog*') || request()->is('news*') ? 'active' : '' }}" href="{{ route('blog') }}">Blog</a>
				</li>
				<li class="nav-item">
					<a class="nav-link {{ request()->is('contact*') ? 'active' : '' }}" href="{{ route('contact') }}">Contact</a>
				</li>
				<li class="nav-item">
					<a class="nav-link {{ request()->is('about*') ? 'active' : '' }}" href="{{ route('about') }}">About</a>
				</li>
			</ul>

			<div class="d-flex align-items-center gap-2 flex-wrap">
				{{-- Tier picker (desktop only — on mobile it lives in the capsule).
				     Hidden for admins viewing the site. --}}
				@if (! empty($travellerTierOptions) && ! $isAdminViewing)
					<div class="dropdown gp-tier-indicator d-none d-lg-block">
						<button class="gp-tier-indicator__btn" type="button"
								data-bs-toggle="dropdown" aria-expanded="false"
								aria-label="Change travelling-from preference">
							<i class="fas fa-earth-africa"></i>
							<span class="gp-tier-indicator__label">{{ $travellerTierLabel ?? 'International visitor' }}</span>
						</button>
						<ul class="dropdown-menu dropdown-menu-end gp-tier-indicator__menu">
							<li class="gp-tier-indicator__heading">I'm travelling from</li>
							@foreach ($travellerTierOptions as $value => $label)
								<li>
									<button type="button" class="dropdown-item gp-tier-indicator__option {{ ($travellerTier ?? null) === $value ? 'is-active' : '' }}"
											data-gp-set-tier="{{ $value }}">
										@if ($value === 'local') <i class="fas fa-home me-2"></i>
										@elseif ($value === 'african') <i class="fas fa-globe-africa me-2"></i>
										@else <i class="fas fa-plane me-2"></i>
										@endif
										{{ $label }}
										@if (($travellerTier ?? null) === $value)
											<i class="fas fa-check ms-auto" style="color:var(--tt-accent)"></i>
										@endif
									</button>
								</li>
							@endforeach
						</ul>
					</div>
				@endif

				{{-- Desktop-only: classic individual wishlist + theme toggle.
				     On mobile these live in the capsule outside the collapse.
				     Wishlist hidden for admins. --}}
				@unless ($isAdminViewing)
					<a href="{{ route('wishlist.index') }}" class="gp-wishlist-link d-none d-lg-inline-flex" aria-label="View wishlist">
						<i class="fas fa-heart"></i>
						@if (($wishlistCount ?? 0) > 0)
							<span class="gp-wishlist-badge gp-wishlist-badge--classic">{{ $wishlistCount }}</span>
						@endif
					</a>
				@endunless

				<button type="button" class="gp-theme-toggle d-none d-lg-inline-flex" data-gp-theme-toggle aria-label="Toggle dark mode">
					<i class="fas fa-moon gp-icon-moon"></i>
					<i class="fas fa-sun gp-icon-sun"></i>
				</button>

				@if ($isAdminViewing)
					<a href="{{ route('home') }}" class="gp-theme-toggle gp-admin-link d-none d-lg-inline-flex"
					   aria-label="Go to admin dashboard" title="Admin dashboard">
						<i class="fas fa-shield-halved"></i>
					</a>
					<form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">@csrf</form>
				@endif
			</div>
		</div>
	</div>

	<style>
		/* ---------- Quick-actions capsule (wishlist + theme toggle) ---------- */
		/* A small rounded pill that holds the heart and the theme toggle. Lives
		   between the brand and the hamburger so it never disappears behind a
		   collapsed menu. */
		.gp-quick-actions {
			display: inline-flex;
			align-items: center;
			gap: 4px;
			padding: 4px 6px;
			margin-right: 8px;
			border-radius: 999px;
			background:
				linear-gradient(135deg, rgba(212, 160, 60, .14), rgba(212, 160, 60, .04));
			border: 1px solid rgba(212, 160, 60, .35);
			box-shadow: 0 4px 14px rgba(122, 82, 27, .08);
			backdrop-filter: blur(8px);
			-webkit-backdrop-filter: blur(8px);
			transition: border-color .2s ease, box-shadow .2s ease, transform .15s ease;
		}
		.gp-quick-actions:hover {
			border-color: rgba(212, 160, 60, .55);
			box-shadow: 0 6px 18px rgba(184, 135, 46, .15);
		}
		html[data-theme="dark"] .gp-quick-actions {
			background: linear-gradient(135deg, rgba(212, 160, 60, .14), rgba(0, 0, 0, .25));
			border-color: rgba(212, 160, 60, .35);
			box-shadow: 0 4px 14px rgba(0, 0, 0, .35);
		}

		.gp-qa-btn {
			position: relative;
			width: 36px;
			height: 36px;
			border-radius: 50%;
			border: 0;
			background: transparent;
			color: var(--tt-text);
			display: inline-flex;
			align-items: center;
			justify-content: center;
			text-decoration: none;
			cursor: pointer;
			transition: background .15s ease, color .15s ease, transform .15s ease;
		}
		.gp-qa-btn:hover { transform: translateY(-1px); color: var(--tt-accent); }
		.gp-qa-btn--heart:hover { color: #d9534f; }
		.gp-qa-btn--heart:hover i { animation: gpQaHeartBeat .55s ease; }
		@keyframes gpQaHeartBeat {
			0%, 100% { transform: scale(1); }
			30%      { transform: scale(1.25); }
			60%      { transform: scale(1); }
		}
		.gp-qa-btn i { font-size: 1rem; }

		.gp-qa-divider {
			width: 1px;
			height: 22px;
			background: rgba(212, 160, 60, .35);
		}

		.gp-wishlist-badge {
			position: absolute;
			top: -3px;
			right: -3px;
			background: var(--tt-accent);
			color: #1a1208;
			font-size: .68rem;
			font-weight: 700;
			min-width: 18px;
			height: 18px;
			padding: 0 5px;
			border-radius: 999px;
			display: inline-flex;
			align-items: center;
			justify-content: center;
			border: 2px solid #fff;
		}
		html[data-theme="dark"] .gp-wishlist-badge { border-color: #0c0c10; }

		/* Theme toggle icons — moon visible in light, sun visible in dark. */
		.gp-qa-btn--theme .gp-icon-sun { display: none; }
		html[data-theme="dark"] .gp-qa-btn--theme .gp-icon-sun { display: inline; }
		html[data-theme="dark"] .gp-qa-btn--theme .gp-icon-moon { display: none; }

		/* Tier picker inside the capsule — wrap the dropdown so it sits flush. */
		.gp-qa-tier-wrap { display: inline-flex; }
		.gp-qa-btn--tier:hover { color: var(--tt-accent); }

		/* Admin shortcut button in the capsule — gold-tinted so it pops as the
		   privileged shortcut without shouting. Also covers the desktop link
		   styled as a theme-toggle next to the moon. */
		.gp-qa-btn--admin,
		.gp-admin-link {
			color: var(--tt-accent);
			text-decoration: none;
		}
		.gp-qa-btn--admin:hover,
		.gp-admin-link:hover {
			background: rgba(212, 160, 60, .15);
			color: var(--tt-accent-dark);
		}

		/* ---------- Desktop-only individual controls ---------- */
		/* The classic standalone wishlist + theme toggle, restored so laptops
		   look like they did before the mobile capsule existed. */
		.gp-wishlist-link {
			position: relative;
			width: 38px;
			height: 38px;
			border-radius: 999px;
			border: 1px solid var(--tt-border);
			color: var(--tt-text);
			display: inline-flex;
			align-items: center;
			justify-content: center;
			text-decoration: none;
			transition: background .2s ease, color .2s ease, border-color .2s ease;
		}
		.gp-wishlist-link:hover {
			background: rgba(212, 160, 60, .1);
			border-color: var(--tt-accent);
			color: var(--tt-accent);
		}
		.gp-wishlist-badge--classic {
			position: absolute;
			top: -4px;
			right: -4px;
			background: var(--tt-accent);
			color: #fff;
			font-size: .7rem;
			font-weight: 700;
			min-width: 18px;
			height: 18px;
			padding: 0 5px;
			border-radius: 999px;
			display: inline-flex;
			align-items: center;
			justify-content: center;
			border: 0;
		}
		.gp-theme-toggle {
			width: 38px;
			height: 38px;
			border-radius: 999px;
			border: 1px solid var(--tt-border);
			background: transparent;
			color: var(--tt-text);
			display: inline-flex;
			align-items: center;
			justify-content: center;
			cursor: pointer;
			transition: background .2s ease, color .2s ease, border-color .2s ease;
		}
		.gp-theme-toggle:hover {
			background: rgba(212, 160, 60, .1);
			border-color: var(--tt-accent);
			color: var(--tt-accent);
		}
		.gp-theme-toggle .gp-icon-sun { display: none; }
		html[data-theme="dark"] .gp-theme-toggle .gp-icon-sun { display: inline; }
		html[data-theme="dark"] .gp-theme-toggle .gp-icon-moon { display: none; }

		/* ---------- Tier indicator (kept inside hamburger menu) ---------- */
		.gp-tier-indicator__btn {
			display: inline-flex;
			align-items: center;
			gap: .4rem;
			height: 38px;
			padding: 0 12px;
			border-radius: 999px;
			border: 1px solid var(--tt-border);
			background: transparent;
			color: var(--tt-text);
			font-size: .82rem;
			font-weight: 600;
			cursor: pointer;
			transition: background .15s ease, color .15s ease, border-color .15s ease;
		}
		.gp-tier-indicator__btn:hover {
			background: rgba(212, 160, 60, .10);
			border-color: var(--tt-accent);
			color: var(--tt-accent);
		}
		.gp-tier-indicator__btn i { font-size: .85rem; }
		.gp-tier-indicator__menu {
			min-width: 220px;
			padding: .35rem;
			border: 1px solid rgba(212, 160, 60, .35);
			box-shadow: 0 14px 36px rgba(0, 0, 0, .15);
		}
		.gp-tier-indicator__heading {
			padding: .3rem .6rem;
			font-size: .7rem;
			font-weight: 700;
			text-transform: uppercase;
			letter-spacing: .08em;
			color: var(--tt-text-muted);
		}
		.gp-tier-indicator__option {
			display: flex;
			align-items: center;
			gap: .3rem;
			font-size: .9rem;
			font-weight: 600;
			border-radius: 8px;
		}
		.gp-tier-indicator__option:hover {
			background: rgba(212, 160, 60, .12);
			color: var(--tt-accent);
		}
		.gp-tier-indicator__option.is-active {
			background: rgba(212, 160, 60, .18);
			color: var(--tt-accent);
		}

		@media (max-width: 991.98px) {
			.gp-tier-indicator__label { display: none; }
			.gp-tier-indicator__btn { padding: 0 10px; }
			.gp-quick-actions { padding: 3px 5px; margin-right: 6px; gap: 2px; }
			.gp-qa-btn { width: 34px; height: 34px; }
		}

		/* Phones — keep the wordmark visible but shrink everything so hamburger ·
		   brand · capsule still fit on one row. Hamburger gets order:-1 so it
		   sits at the far left, with the brand next to it. */
		@media (max-width: 991.98px) {
			.tt-navbar .navbar-toggler {
				order: -1;
				margin-right: 10px;
			}
			/* Drop the open menu OUT of the navbar's flex flow so the navbar
			   itself never grows in height. The menu becomes a floating panel
			   that slides down under the navbar. */
			.tt-navbar > .container-fluid { position: relative; }
			.tt-navbar .navbar-collapse {
				position: absolute;
				top: 100%;
				left: 0;
				right: 0;
				margin-top: 8px;
				padding: 14px 18px 18px;
				background: rgba(255, 255, 255, .96);
				border: 1px solid rgba(212, 160, 60, .25);
				border-radius: 14px;
				box-shadow: 0 14px 36px rgba(122, 82, 27, .18);
				backdrop-filter: blur(14px);
				-webkit-backdrop-filter: blur(14px);
			}
			html[data-theme="dark"] .tt-navbar .navbar-collapse {
				background: rgba(15, 15, 18, .96);
				border-color: rgba(212, 160, 60, .3);
				box-shadow: 0 14px 36px rgba(0, 0, 0, .55);
			}
			.tt-navbar .navbar-collapse .navbar-nav {
				margin-left: 0 !important;
				margin-right: 0 !important;
				gap: 2px;
			}
			.tt-navbar .navbar-collapse .nav-link {
				padding: .55rem .25rem;
			}
			.tt-navbar .navbar-collapse > .d-flex {
				margin-top: 10px;
				padding-top: 10px;
				border-top: 1px solid rgba(212, 160, 60, .15);
			}
		}
		@media (max-width: 767.98px) {
			.gp-brand { min-width: 0; gap: .5rem; }
			.gp-brand-text {
				font-size: 1rem;
				white-space: nowrap;
				overflow: hidden;
				text-overflow: ellipsis;
				min-width: 0;
			}
			.gp-brand-mark img { height: 40px; }
			.gp-quick-actions { padding: 2px 4px; margin-right: 4px; }
			.gp-qa-btn { width: 32px; height: 32px; }
			.gp-qa-btn i { font-size: .92rem; }
			.gp-qa-divider { height: 18px; }
			.tt-navbar .navbar-toggler { padding: 4px 8px; margin-right: 6px; }
		}
		@media (max-width: 420px) {
			.gp-brand-text { font-size: .92rem; }
			.gp-brand-mark img { height: 36px; }
			.gp-qa-btn { width: 30px; height: 30px; }
			.gp-qa-btn i { font-size: .85rem; }
		}
		/* Tiny phones (e.g. iPhone SE) — let the wordmark truncate gracefully
		   so the capsule + hamburger always have room. */
		@media (max-width: 340px) {
			.gp-brand-text { font-size: .85rem; }
			.gp-brand-mark img { height: 32px; }
		}
	</style>

	<script>
		// One-click tier change from the navbar dropdown — posts to the
		// same endpoint as the consent banner, then refreshes so prices
		// + filtered activity lists reflect the new tier.
		document.addEventListener('click', function (e) {
			var btn = e.target.closest('[data-gp-set-tier]');
			if (!btn) return;
			e.preventDefault();
			var tier = btn.getAttribute('data-gp-set-tier');
			var token = document.querySelector('meta[name="csrf-token"]');
			fetch('{{ route('traveller-tier.update') }}', {
				method: 'POST',
				credentials: 'same-origin',
				headers: {
					'Accept': 'application/json',
					'Content-Type': 'application/json',
					'X-CSRF-TOKEN': token ? token.content : '',
					'X-Requested-With': 'XMLHttpRequest',
				},
				body: JSON.stringify({ tier: tier }),
			}).then(function () { window.location.reload(); });
		});
	</script>
</nav>
