@php
    // Suppress the banner entirely on any auth-related page — login,
    // register, password reset/forgot, email verification, and the OTP
    // (one-time code) challenge flow. These screens demand focus and the
    // visitor is already mid-task; a cookie banner would only distract.
    $hideOnAuth = request()->routeIs(
        'login',
        'register',
        'password.*',
        'verification.*',
        'otp.*',
        'two-factor.*',
    ) || str_starts_with((string) request()->path(), 'login')
       || str_starts_with((string) request()->path(), 'register')
       || str_starts_with((string) request()->path(), 'password')
       || str_starts_with((string) request()->path(), 'reset-password')
       || str_starts_with((string) request()->path(), 'forgot-password')
       || str_starts_with((string) request()->path(), 'verify-email')
       || str_starts_with((string) request()->path(), 'email/verify')
       || str_starts_with((string) request()->path(), 'two-factor')
       || str_starts_with((string) request()->path(), 'otp');
    // Has the visitor already explicitly picked a tier?
    $hasTier = app(\App\Services\TravellerTier::class)->current() !== null;
@endphp

@unless ($hideOnAuth)
<div id="gp-cookie-consent" class="gp-cookie-consent" role="dialog"
     aria-modal="true" aria-live="polite" aria-label="Cookie and traveller preferences"
     data-has-tier="{{ $hasTier ? '1' : '0' }}" hidden>
    <div class="gp-cookie-consent-inner">
        <div class="gp-cookie-consent-text">
            <strong>We value your privacy.</strong>
            We use cookies and may collect limited info (such as pages viewed and items
            saved to your wishlist) to tailor our offers. Before you explore, tell us
            where you're travelling from:
        </div>

        <fieldset class="gp-tier-picker" aria-label="I am travelling from">
            <legend class="gp-tier-picker__legend">I'm travelling from:</legend>
            @foreach (App\Services\TravellerTier::TIERS as $value => $label)
                <label class="gp-tier-option">
                    {{-- No default selection — visitor MUST pick. --}}
                    <input type="radio" name="gp-tier" value="{{ $value }}">
                    <span class="gp-tier-option__chip">
                        <span class="gp-tier-option__tick" aria-hidden="true">
                            <i class="fas fa-check"></i>
                        </span>
                        @if ($value === 'local') <i class="fas fa-home gp-tier-option__icon"></i>
                        @elseif ($value === 'african') <i class="fas fa-globe-africa gp-tier-option__icon"></i>
                        @else <i class="fas fa-plane gp-tier-option__icon"></i>
                        @endif
                        {{ $label }}
                    </span>
                </label>
            @endforeach
        </fieldset>

        <div class="gp-cookie-consent-actions">
            <button type="button" class="gp-cookie-btn gp-cookie-btn-primary"
                    data-gp-cookie-action="accept" disabled>
                Accept &amp; continue
            </button>
        </div>

        <p class="gp-cookie-consent-hint">
            <i class="fas fa-info-circle me-1"></i>
            Pick one to continue.
        </p>
    </div>
</div>

<style>
    .gp-cookie-consent {
        position: fixed;
        bottom: 16px;
        left: 16px;
        right: 16px;
        z-index: 1050;
        background: linear-gradient(180deg, rgba(255, 255, 255, .97), rgba(255, 248, 230, .97));
        color: var(--tt-text);
        border: 1px solid rgba(212, 160, 60, .35);
        border-radius: 16px;
        box-shadow: 0 18px 50px rgba(122, 82, 27, .18), 0 4px 12px rgba(122, 82, 27, .08);
        padding: 18px 22px;
        max-width: 980px;
        margin: 0 auto;
        backdrop-filter: blur(12px);
        -webkit-backdrop-filter: blur(12px);
    }
    html[data-theme="dark"] .gp-cookie-consent {
        background: linear-gradient(180deg, rgba(26, 18, 8, .96), rgba(42, 28, 16, .96));
        color: #fbf3e1;
        border-color: rgba(212, 160, 60, .45);
        box-shadow: 0 20px 60px rgba(0, 0, 0, .55);
    }

    .gp-cookie-consent-inner {
        display: grid;
        grid-template-columns: 1fr;
        gap: 14px;
        align-items: center;
    }
    @media (min-width: 900px) {
        .gp-cookie-consent-inner {
            grid-template-columns: minmax(280px, 1.4fr) auto auto;
            gap: 20px;
        }
        .gp-cookie-consent-hint { grid-column: 1 / -1; margin: 0; }
    }

    .gp-cookie-consent-text {
        font-size: .92rem;
        line-height: 1.5;
    }
    .gp-cookie-consent-text strong { font-weight: 700; color: var(--tt-accent); }

    /* Tier picker — options stacked vertically, one per row. */
    .gp-tier-picker {
        border: 0;
        padding: 0;
        margin: 0;
        display: flex;
        flex-direction: column;
        gap: 8px;
    }
    .gp-tier-picker__legend {
        font-size: .75rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: .08em;
        color: var(--tt-text-muted);
        margin-bottom: 4px;
    }
    html[data-theme="dark"] .gp-tier-picker__legend { color: rgba(251, 243, 225, .65); }

    .gp-tier-option { cursor: pointer; display: block; }
    .gp-tier-option input { position: absolute; opacity: 0; pointer-events: none; }
    .gp-tier-option__chip {
        display: flex;
        align-items: center;
        gap: .55rem;
        padding: 10px 16px 10px 14px;
        border-radius: 12px;
        background: rgba(212, 160, 60, .08);
        border: 1.5px solid rgba(212, 160, 60, .30);
        color: var(--tt-text);
        font-size: .9rem;
        font-weight: 600;
        width: 100%;
        transition: background .15s ease, border-color .15s ease, color .15s ease, transform .12s ease;
    }
    .gp-tier-option__icon { color: var(--tt-accent); font-size: .8rem; }
    .gp-tier-option:hover .gp-tier-option__chip {
        background: rgba(212, 160, 60, .16);
        border-color: rgba(212, 160, 60, .55);
    }

    /* The tick — empty circle by default, gold check when selected. */
    .gp-tier-option__tick {
        width: 18px;
        height: 18px;
        border-radius: 50%;
        border: 1.5px solid rgba(212, 160, 60, .55);
        display: inline-flex;
        align-items: center;
        justify-content: center;
        background: transparent;
        transition: background .15s ease, border-color .15s ease, transform .12s ease;
    }
    .gp-tier-option__tick i {
        font-size: .55rem;
        color: transparent;
        transition: color .12s ease;
    }
    .gp-tier-option input:checked + .gp-tier-option__chip {
        background: linear-gradient(135deg, #fff8e6, #fbecc4);
        border-color: #d4a03c;
        color: #1a1208;
        transform: translateY(-1px);
        box-shadow: 0 4px 14px rgba(184, 135, 46, .25);
    }
    .gp-tier-option input:checked + .gp-tier-option__chip .gp-tier-option__icon { color: #1a1208; }
    .gp-tier-option input:checked + .gp-tier-option__chip .gp-tier-option__tick {
        background: linear-gradient(135deg, #d4a03c, #b8872e);
        border-color: #b8872e;
    }
    .gp-tier-option input:checked + .gp-tier-option__chip .gp-tier-option__tick i { color: #fff; }
    .gp-tier-option input:focus-visible + .gp-tier-option__chip {
        outline: 2px solid var(--tt-accent);
        outline-offset: 2px;
    }
    html[data-theme="dark"] .gp-tier-option__chip { color: #fbf3e1; }

    .gp-cookie-consent-actions {
        display: flex;
        gap: 8px;
        flex-wrap: wrap;
    }
    .gp-cookie-btn {
        border: none;
        padding: 10px 20px;
        border-radius: 999px;
        font-weight: 700;
        font-size: .9rem;
        cursor: pointer;
        transition: opacity .2s ease, transform .12s ease, filter .12s ease;
    }
    .gp-cookie-btn-primary {
        background: linear-gradient(135deg, #d4a03c, #b8872e);
        color: #1a1208;
        box-shadow: 0 6px 16px rgba(184, 135, 46, .35);
    }
    .gp-cookie-btn-primary:hover:not(:disabled) { transform: translateY(-1px); filter: brightness(1.05); }
    .gp-cookie-btn-primary:disabled {
        opacity: .45;
        cursor: not-allowed;
        box-shadow: none;
    }

    .gp-cookie-consent-hint {
        margin: 4px 0 0;
        font-size: .8rem;
        color: var(--tt-text-muted);
    }
    html[data-theme="dark"] .gp-cookie-consent-hint { color: rgba(251, 243, 225, .55); }

    @media (max-width: 575.98px) {
        .gp-cookie-consent { left: 8px; right: 8px; bottom: 88px; padding: 14px; }
        .gp-tier-option__chip { padding: 6px 12px 6px 10px; font-size: .8rem; }
    }
</style>

<script>
    (function () {
        // v3: introduces the no-default + force-pick behaviour.
        var STORAGE_KEY = 'gp_cookie_consent_v3';
        // Clear older keys so existing visitors get the refreshed flow.
        try {
            localStorage.removeItem('gp_cookie_consent');
            localStorage.removeItem('gp_cookie_consent_v2');
        } catch (e) {}

        var banner = document.getElementById('gp-cookie-consent');
        if (!banner) return;

        var hasTier = banner.dataset.hasTier === '1';
        var stored = null;
        try { stored = localStorage.getItem(STORAGE_KEY); } catch (e) {}

        // Show the banner if the visitor hasn't both (a) seen this version of
        // the banner AND (b) picked a tier yet.
        if (!stored || !hasTier) banner.hidden = false;

        var acceptBtn = banner.querySelector('[data-gp-cookie-action="accept"]');

        // The Accept button stays disabled until a tier is selected.
        // We no longer pre-save on change — wait for Accept so we only POST once
        // and can reliably reload the page once the cookie is set.
        banner.addEventListener('change', function (e) {
            if (e.target.name !== 'gp-tier') return;
            acceptBtn.disabled = false;
        });

        acceptBtn.addEventListener('click', function () {
            var checked = banner.querySelector('input[name="gp-tier"]:checked');
            if (!checked) return; // safety — button shouldn't be enabled here
            try { localStorage.setItem(STORAGE_KEY, 'accept'); } catch (e) {}
            acceptBtn.disabled = true;
            banner.hidden = true;
            // Reload after the cookie is persisted so the server re-renders the
            // navbar tier label, filtered activity lists, and prices.
            setTier(checked.value).finally(function () { window.location.reload(); });
        });

        function setTier(tier) {
            var token = document.querySelector('meta[name="csrf-token"]');
            return fetch('{{ route('traveller-tier.update') }}', {
                method: 'POST',
                credentials: 'same-origin',
                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': token ? token.content : '',
                    'X-Requested-With': 'XMLHttpRequest',
                },
                body: JSON.stringify({ tier: tier }),
            });
        }
    })();
</script>
@endunless
