{{--
    First-visit theme-toggle hint.
    A travel-themed pangolin peeks from the navbar near the [data-gp-theme-toggle]
    button, taps the moon/sun, and shows a tooltip. Dismisses on toggle use, on
    click of the X, or after 12s. Shown once per browser via localStorage.
    Respects prefers-reduced-motion (no bounce, instant fade).
--}}
<div id="gp-theme-hint" class="gp-theme-hint" aria-hidden="true" hidden>
    <div class="gp-theme-hint__bubble" role="status" aria-live="polite">
        <span class="gp-theme-hint__title">Light or dark mode</span>
        <span class="gp-theme-hint__body">Tap to switch</span>
        <button type="button" class="gp-theme-hint__close" aria-label="Dismiss hint">&times;</button>
    </div>

    <svg class="gp-theme-hint__pangolin" viewBox="0 0 96 96" width="72" height="72" aria-hidden="true" focusable="false">
        <defs>
            <linearGradient id="gpScale" x1="0" y1="0" x2="1" y2="1">
                <stop offset="0%"  stop-color="#f3c779"/>
                <stop offset="60%" stop-color="#d4a03c"/>
                <stop offset="100%" stop-color="#8a5e1c"/>
            </linearGradient>
            <radialGradient id="gpEye" cx="50%" cy="50%" r="50%">
                <stop offset="0%"  stop-color="#fff8e1"/>
                <stop offset="60%" stop-color="#1b1208"/>
                <stop offset="100%" stop-color="#000"/>
            </radialGradient>
        </defs>

        {{-- Body curl --}}
        <path d="M16 70 C 10 46, 30 18, 56 22 C 80 26, 88 50, 76 68 C 64 84, 32 84, 16 70 Z"
              fill="url(#gpScale)" stroke="#5c3d12" stroke-width="1.5"/>

        {{-- Scale rows --}}
        <g fill="none" stroke="#7a521b" stroke-width="1.3" stroke-linecap="round" opacity="0.9">
            <path d="M30 32 Q 38 28 46 32"/>
            <path d="M44 30 Q 52 26 60 30"/>
            <path d="M58 32 Q 66 28 74 34"/>
            <path d="M26 44 Q 36 40 46 44"/>
            <path d="M46 44 Q 56 40 66 44"/>
            <path d="M64 46 Q 72 44 78 50"/>
            <path d="M24 58 Q 34 54 44 58"/>
            <path d="M44 58 Q 54 54 64 58"/>
            <path d="M62 60 Q 70 60 76 64"/>
        </g>

        {{-- Head --}}
        <ellipse cx="22" cy="66" rx="14" ry="10" fill="#e6b76a" stroke="#5c3d12" stroke-width="1.5"/>
        {{-- Snout --}}
        <path d="M8 64 Q 4 66 8 70 L 14 70 Z" fill="#c89148" stroke="#5c3d12" stroke-width="1.2"/>
        {{-- Eye --}}
        <circle cx="22" cy="64" r="2.4" fill="url(#gpEye)"/>
        <circle cx="21.4" cy="63.4" r="0.7" fill="#fff"/>
        {{-- Tiny ear nub --}}
        <path d="M28 58 Q 30 54 33 58" stroke="#5c3d12" stroke-width="1.2" fill="#e6b76a"/>

        {{-- Pointing arm tapping the toggle --}}
        <g class="gp-theme-hint__arm">
            <path d="M36 70 Q 44 76 54 74" stroke="#5c3d12" stroke-width="2.4" fill="none" stroke-linecap="round"/>
            <circle cx="54" cy="74" r="3.2" fill="#e6b76a" stroke="#5c3d12" stroke-width="1.2"/>
        </g>
    </svg>
</div>

<style>
    .gp-theme-hint {
        position: fixed;
        /* top + right are set dynamically by the script so the BUBBLE (the
           rightmost child via row-reverse) sits under whichever theme toggle
           is currently visible (mobile capsule or desktop button). */
        z-index: 1080;
        display: flex;
        flex-direction: row-reverse;   /* bubble on right (under toggle), pangolin on left */
        align-items: flex-start;
        gap: 10px;
        pointer-events: none;
        opacity: 0;
        transform: translateY(-12px) scale(.95);
        transform-origin: top right;
        transition: opacity .35s ease, transform .45s cubic-bezier(.16, 1.4, .3, 1);
    }
    .gp-theme-hint[data-shown="1"] {
        opacity: 1;
        transform: translateY(0) scale(1);
        pointer-events: auto;
        animation: gpHintWiggle 1.8s ease-in-out .55s 2;
    }
    @keyframes gpHintWiggle {
        0%, 100% { transform: translateY(0) rotate(0); }
        25%      { transform: translateY(-2px) rotate(-1deg); }
        50%      { transform: translateY(0) rotate(.8deg); }
        75%      { transform: translateY(-1px) rotate(-.4deg); }
    }
    .gp-theme-hint--gone {
        opacity: 0 !important;
        transform: translateY(-12px) !important;
        pointer-events: none !important;
    }

    .gp-theme-hint__bubble {
        position: relative;
        background: linear-gradient(135deg, #1a1208, #2a1e10);
        color: #fbf3e1;
        border: 1px solid rgba(212, 160, 60, 0.55);
        border-radius: 14px;
        padding: 10px 34px 10px 14px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.35);
        max-width: 240px;
        font-family: 'Inter', system-ui, sans-serif;
    }
    .gp-theme-hint__title {
        display: block;
        font-weight: 700;
        font-size: .92rem;
        color: #f3c779;
        margin-bottom: 2px;
    }
    .gp-theme-hint__body {
        display: block;
        font-size: .82rem;
        line-height: 1.35;
        color: #f3eadb;
    }
    /* Pointer tail — sits ON TOP of the bubble and aims up at the toggle
       button (the bubble is positioned underneath it). */
    .gp-theme-hint__bubble::after {
        content: "";
        position: absolute;
        top: -8px;
        right: 22px;
        width: 0;
        height: 0;
        border: 8px solid transparent;
        border-bottom-color: rgba(212, 160, 60, 0.55);
    }
    .gp-theme-hint__bubble::before {
        content: "";
        position: absolute;
        top: -6px;
        right: 23px;
        width: 0;
        height: 0;
        border: 7px solid transparent;
        border-bottom-color: #2a1e10;
        z-index: 1;
    }
    .gp-theme-hint__close {
        position: absolute;
        top: 4px;
        right: 6px;
        background: transparent;
        border: 0;
        color: #f3c779;
        font-size: 1.1rem;
        line-height: 1;
        padding: 2px 6px;
        cursor: pointer;
        border-radius: 6px;
    }
    .gp-theme-hint__close:hover { background: rgba(243, 199, 121, 0.15); }

    .gp-theme-hint__pangolin {
        filter: drop-shadow(0 4px 10px rgba(0, 0, 0, 0.35));
        transform-origin: 70% 70%;
        animation: gpPangolinPeek 2.2s ease-in-out infinite;
    }
    .gp-theme-hint__arm {
        transform-origin: 38px 70px;
        animation: gpPangolinTap 1.6s ease-in-out infinite;
    }

    @keyframes gpPangolinPeek {
        0%, 100% { transform: rotate(-4deg) translateY(0); }
        50%      { transform: rotate(4deg) translateY(-3px); }
    }
    @keyframes gpPangolinTap {
        0%, 100% { transform: rotate(0deg); }
        40%      { transform: rotate(-14deg); }
        60%      { transform: rotate(-14deg); }
    }

    /* Light-mode bubble override for readability over the warm cream backdrop */
    html:not([data-theme="dark"]) .gp-theme-hint__bubble {
        background: linear-gradient(135deg, #2a1e10, #3a2814);
    }

    /* Mobile sizing — bubble + pangolin slightly smaller. Positioning is
       handled by the script so the bubble docks under the visible toggle
       on both phone and laptop. */
    @media (max-width: 768px) {
        .gp-theme-hint {
            flex-direction: row-reverse;
            gap: 6px;
        }
        .gp-theme-hint__bubble {
            max-width: 200px;
            padding: 9px 28px 9px 12px;
        }
        .gp-theme-hint__pangolin { width: 56px; height: 56px; }
    }

    @media (prefers-reduced-motion: reduce) {
        .gp-theme-hint__pangolin,
        .gp-theme-hint__arm {
            animation: none !important;
        }
        .gp-theme-hint {
            transition: opacity .15s linear;
            transform: none !important;
        }
    }
</style>

<script>
    (function () {
        // Bump the storage key whenever the hint design changes — visitors
        // who dismissed an older version then see the refreshed one once.
        var STORAGE_KEY = 'gp_theme_hint_seen_v3';
        try {
            localStorage.removeItem('gp_theme_hint_seen');
            localStorage.removeItem('gp_theme_hint_seen_v2');
        } catch (e) {}
        var HINT_DELAY_MS = 900;
        var AUTO_HIDE_MS  = 20000;

        function ready(fn) {
            if (document.readyState !== 'loading') fn();
            else document.addEventListener('DOMContentLoaded', fn);
        }

        // Find the FIRST [data-gp-theme-toggle] that is currently rendered.
        // The navbar has two — a mobile capsule (hidden on >=lg) and a desktop
        // button (hidden on <lg) — so we have to skip the invisible one or
        // the bubble ends up positioned against (0,0,0,0) and flies off-screen.
        function findVisibleToggle() {
            var candidates = document.querySelectorAll('[data-gp-theme-toggle]');
            for (var i = 0; i < candidates.length; i++) {
                var el = candidates[i];
                if (el.offsetWidth > 0 && el.offsetHeight > 0) return el;
            }
            return null;
        }

        // The cookie banner takes priority on first visit — it's a required
        // choice and would distract the visitor from a hint anyway. Returns
        // true if the banner is currently visible on the page.
        function cookieBannerActive() {
            var banner = document.getElementById('gp-cookie-consent');
            return !!(banner && !banner.hidden);
        }

        ready(function () {
            var hint = document.getElementById('gp-theme-hint');
            if (!hint) return;

            try {
                if (localStorage.getItem(STORAGE_KEY) === '1') return;
            } catch (e) {}

            // Don't compete with the cookie banner — it triggers a page reload
            // on accept, which would cut the hint off mid-fade. On the next
            // page load (no banner), the hint will appear and stay the full
            // duration.
            if (cookieBannerActive()) return;

            // Anchor the bubble next to whichever toggle is on-screen now.
            // The container uses row-reverse so the BUBBLE is the rightmost
            // child — meaning the container's right edge == the bubble's
            // right edge. The pointer tail sits 22px inside the bubble's
            // right edge, so to centre the tail under the toggle we offset
            // the right by `toggleHalfWidth - pointerInset`.
            var POINTER_INSET = 22; // keep in sync with ::after right value
            function positionNearToggle() {
                var toggle = findVisibleToggle();
                if (!toggle) return false;
                var rect = toggle.getBoundingClientRect();

                // Clear any previous mobile-specific positioning.
                hint.style.left = 'auto';
                hint.style.bottom = 'auto';

                hint.style.top = (rect.bottom + 10) + 'px';

                // X position: solve `bubble_right_edge_from_viewport_right`
                // so that pointer X = toggle centre X.
                var rightOffset = (window.innerWidth - rect.right) + (rect.width / 2) - POINTER_INSET;
                hint.style.right = Math.max(8, rightOffset) + 'px';

                return true;
            }

            // Some pages render the navbar after a tick (Alpine/Bootstrap collapse,
            // deferred assets) — retry a few times until the toggle exists.
            function tryShow(attempts) {
                if (positionNearToggle()) {
                    hint.hidden = false;
                    requestAnimationFrame(function () {
                        hint.setAttribute('data-shown', '1');
                        hint.setAttribute('aria-hidden', 'false');
                    });
                    return;
                }
                if (attempts > 0) setTimeout(function () { tryShow(attempts - 1); }, 200);
            }

            setTimeout(function () { tryShow(8); }, HINT_DELAY_MS);

            function dismiss() {
                hint.classList.add('gp-theme-hint--gone');
                hint.setAttribute('aria-hidden', 'true');
                try { localStorage.setItem(STORAGE_KEY, '1'); } catch (e) {}
            }

            // Dismiss interactions.
            hint.querySelector('.gp-theme-hint__close').addEventListener('click', dismiss);
            hint.querySelector('.gp-theme-hint__bubble').addEventListener('click', function (e) {
                if (!e.target.closest('.gp-theme-hint__close')) dismiss();
            });
            // Any toggle click anywhere dismisses (covers desktop AND mobile
            // toggles, plus future ones added without rewiring this script).
            document.addEventListener('click', function (e) {
                if (e.target.closest('[data-gp-theme-toggle]')) dismiss();
            });

            // Keep the bubble glued to the toggle on resize / orientation change.
            window.addEventListener('resize', function () {
                if (hint.hidden) return;
                positionNearToggle();
            }, { passive: true });

            // On mobile only: when the wishlist hint appears (triggered when
            // the user reaches the Popular Choice section), step aside so
            // there are never two pangolin hints on screen at once.
            // Desktop has plenty of room for both — and yielding here would
            // cut the theme hint off after only ~900ms.
            document.addEventListener('gp:wish-hint-shown', function () {
                if (window.innerWidth < 992) dismiss();
            });

            setTimeout(dismiss, AUTO_HIDE_MS);
        });
    })();
</script>
