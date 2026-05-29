{{--
    First-visit wishlist hint.
    Finds the first visible .gp-wish-btn on the page, gently pulses it, and
    anchors a speech bubble next to it that says "Tap the heart to save it
    for your trip!". Dismisses on any wishlist-button click, on the X, or
    after 12s. Shown once per browser via localStorage.
    Respects prefers-reduced-motion.
--}}
<div id="gp-wish-hint" class="gp-wish-hint" hidden aria-hidden="true">
    <svg class="gp-wish-hint__pangolin" viewBox="0 0 96 96" width="64" height="64" aria-hidden="true" focusable="false">
        <defs>
            <linearGradient id="gpwScale" x1="0" y1="0" x2="1" y2="1">
                <stop offset="0%"  stop-color="#f3c779"/>
                <stop offset="60%" stop-color="#d4a03c"/>
                <stop offset="100%" stop-color="#8a5e1c"/>
            </linearGradient>
            <radialGradient id="gpwEye" cx="50%" cy="50%" r="50%">
                <stop offset="0%"  stop-color="#fff8e1"/>
                <stop offset="60%" stop-color="#1b1208"/>
                <stop offset="100%" stop-color="#000"/>
            </radialGradient>
        </defs>

        {{-- Curled body --}}
        <path d="M16 70 C 10 46, 30 18, 56 22 C 80 26, 88 50, 76 68 C 64 84, 32 84, 16 70 Z"
              fill="url(#gpwScale)" stroke="#5c3d12" stroke-width="1.5"/>
        <g fill="none" stroke="#7a521b" stroke-width="1.3" stroke-linecap="round" opacity="0.9">
            <path d="M30 32 Q 38 28 46 32"/>
            <path d="M44 30 Q 52 26 60 30"/>
            <path d="M58 32 Q 66 28 74 34"/>
            <path d="M26 44 Q 36 40 46 44"/>
            <path d="M46 44 Q 56 40 66 44"/>
            <path d="M64 46 Q 72 44 78 50"/>
        </g>
        {{-- Head + eye --}}
        <ellipse cx="22" cy="66" rx="14" ry="10" fill="#e6b76a" stroke="#5c3d12" stroke-width="1.5"/>
        <path d="M8 64 Q 4 66 8 70 L 14 70 Z" fill="#c89148" stroke="#5c3d12" stroke-width="1.2"/>
        <circle cx="22" cy="64" r="2.4" fill="url(#gpwEye)"/>
        <circle cx="21.4" cy="63.4" r="0.7" fill="#fff"/>

        {{-- Tiny heart it's holding up --}}
        <g class="gp-wish-hint__heart-anim">
            <path d="M44 58
                     C 44 54, 48 52, 50 55
                     C 52 52, 56 54, 56 58
                     C 56 62, 50 66, 50 66
                     C 50 66, 44 62, 44 58 Z"
                  fill="#e85a5a" stroke="#7a1f1f" stroke-width="1"/>
        </g>
    </svg>

    <div class="gp-wish-hint__bubble" role="status" aria-live="polite">
        <span class="gp-wish-hint__title">Heart it, keep it</span>
        <span class="gp-wish-hint__body">Tap the heart to save trips you love &mdash; we'll keep them in your wishlist.</span>
        <button type="button" class="gp-wish-hint__close" aria-label="Dismiss hint">&times;</button>
    </div>
</div>

<style>
    /* Pulse the first wishlist heart so the user's eye lands on it. */
    .gp-wish-btn--hint-pulse {
        animation: gpWishPulse 1.4s ease-in-out infinite;
        box-shadow: 0 0 0 0 rgba(217, 83, 79, 0.55);
    }
    @keyframes gpWishPulse {
        0%   { box-shadow: 0 2px 8px rgba(0, 0, 0, 0.12), 0 0 0 0 rgba(217, 83, 79, 0.55); }
        70%  { box-shadow: 0 2px 8px rgba(0, 0, 0, 0.12), 0 0 0 14px rgba(217, 83, 79, 0); }
        100% { box-shadow: 0 2px 8px rgba(0, 0, 0, 0.12), 0 0 0 0 rgba(217, 83, 79, 0); }
    }

    .gp-wish-hint {
        position: absolute;
        z-index: 1070;
        display: flex;
        align-items: center;
        gap: 8px;
        pointer-events: none;
        opacity: 0;
        transform: translateY(-6px) scale(.95);
        transition: opacity .35s ease, transform .35s ease;
        max-width: 280px;
    }
    .gp-wish-hint[data-shown="1"] {
        opacity: 1;
        transform: translateY(0) scale(1);
        pointer-events: auto;
    }
    .gp-wish-hint--gone {
        opacity: 0 !important;
        transform: translateY(-8px) scale(.95) !important;
        pointer-events: none !important;
    }

    .gp-wish-hint__pangolin {
        flex-shrink: 0;
        filter: drop-shadow(0 4px 10px rgba(0, 0, 0, 0.35));
        animation: gpWishPangolinBob 2.4s ease-in-out infinite;
    }
    .gp-wish-hint__heart-anim {
        transform-origin: 50px 60px;
        animation: gpWishHeartBeat 1s ease-in-out infinite;
    }
    @keyframes gpWishPangolinBob {
        0%, 100% { transform: translateY(0) rotate(-2deg); }
        50%      { transform: translateY(-3px) rotate(3deg); }
    }
    @keyframes gpWishHeartBeat {
        0%, 100% { transform: scale(1); }
        30%      { transform: scale(1.25); }
        60%      { transform: scale(1); }
    }

    .gp-wish-hint__bubble {
        position: relative;
        background: linear-gradient(135deg, #1a1208, #2a1e10);
        color: #fbf3e1;
        border: 1px solid rgba(232, 90, 90, 0.55);
        border-radius: 14px;
        padding: 10px 32px 10px 14px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.35);
        font-family: 'Inter', system-ui, sans-serif;
        cursor: pointer;
    }
    .gp-wish-hint__title {
        display: block;
        font-weight: 700;
        font-size: .92rem;
        color: #ffb3b3;
        margin-bottom: 2px;
    }
    .gp-wish-hint__body {
        display: block;
        font-size: .82rem;
        line-height: 1.35;
        color: #f3eadb;
    }
    .gp-wish-hint__bubble::before {
        content: "";
        position: absolute;
        top: 22px;
        left: -7px;
        width: 0;
        height: 0;
        border: 7px solid transparent;
        border-right-color: #2a1e10;
    }
    .gp-wish-hint__close {
        position: absolute;
        top: 3px;
        right: 6px;
        background: transparent;
        border: 0;
        color: #ffb3b3;
        font-size: 1.1rem;
        line-height: 1;
        padding: 2px 6px;
        cursor: pointer;
        border-radius: 6px;
    }
    .gp-wish-hint__close:hover { background: rgba(255, 179, 179, 0.15); }

    html:not([data-theme="dark"]) .gp-wish-hint__bubble {
        background: linear-gradient(135deg, #2a1e10, #3a2814);
    }

    @media (max-width: 575.98px) {
        .gp-wish-hint { max-width: 240px; }
        .gp-wish-hint__pangolin { width: 52px; height: 52px; }
        .gp-wish-hint__bubble { padding: 8px 28px 8px 12px; }
        .gp-wish-hint__title { font-size: .86rem; }
        .gp-wish-hint__body { font-size: .76rem; }
    }

    @media (prefers-reduced-motion: reduce) {
        .gp-wish-btn--hint-pulse,
        .gp-wish-hint__pangolin,
        .gp-wish-hint__heart-anim { animation: none !important; }
        .gp-wish-hint { transition: opacity .15s linear; transform: none !important; }
    }
</style>

<script>
    (function () {
        var STORAGE_KEY = 'gp_wish_hint_seen';
        var SHOW_DELAY_MS = 1800;   // desktop: time-based reveal
        var AUTO_HIDE_MS  = 12000;
        var MOBILE_BP     = 992;    // < this width = "mobile" — wait for scroll instead

        function ready(fn) {
            if (document.readyState !== 'loading') fn();
            else document.addEventListener('DOMContentLoaded', fn);
        }

        function isInViewport(el) {
            var r = el.getBoundingClientRect();
            return r.top >= 0 && r.left >= 0
                && r.bottom <= (window.innerHeight || document.documentElement.clientHeight)
                && r.right  <= (window.innerWidth  || document.documentElement.clientWidth);
        }

        // True if the element is actually rendered (not display:none via a
        // responsive utility like `d-none d-lg-block`). querySelectorAll
        // returns hidden hearts too, and observing one means the IO callback
        // never fires (intersection rect is empty for display:none).
        function isRendered(el) {
            if (! el || ! el.getClientRects) return false;
            return el.getClientRects().length > 0;
        }

        function pickAnchor() {
            // Only consider hearts that are actually rendered on this viewport.
            var btns = Array.prototype.slice
                .call(document.querySelectorAll('.gp-wish-btn'))
                .filter(isRendered);
            if (btns.length === 0) return null;
            var visible = btns.find(isInViewport);
            return visible || btns[0];
        }

        ready(function () {
            var hint = document.getElementById('gp-wish-hint');
            if (!hint) return;

            try {
                if (localStorage.getItem(STORAGE_KEY) === '1') return;
            } catch (e) { /* private mode — still show */ }

            // No wishlist hearts on this page → nothing to hint at.
            var anchor = pickAnchor();
            if (!anchor) return;

            var isMobile = window.innerWidth < MOBILE_BP;

            function positionHint() {
                var a = pickAnchor();
                if (!a) return;
                var rect = a.getBoundingClientRect();
                var hintW = hint.offsetWidth || 280;
                var maxLeft = (document.documentElement.clientWidth || window.innerWidth) - hintW - 8;
                var top  = rect.top  + window.scrollY - 12;
                var left = rect.right + window.scrollX + 12;

                // If we'd overflow the right edge, flip to the left side of the anchor.
                if (left + hintW > window.innerWidth) {
                    left = rect.left + window.scrollX - hintW - 12;
                    hint.classList.add('gp-wish-hint--flipped');
                }
                // Hard clamp so the hint can never cause horizontal page scroll.
                left = Math.min(Math.max(8, left), Math.max(8, maxLeft));
                hint.style.top  = top  + 'px';
                hint.style.left = left + 'px';
            }

            function dismiss() {
                hint.classList.add('gp-wish-hint--gone');
                hint.setAttribute('aria-hidden', 'true');
                document.querySelectorAll('.gp-wish-btn--hint-pulse')
                    .forEach(function (b) { b.classList.remove('gp-wish-btn--hint-pulse'); });
                try { localStorage.setItem(STORAGE_KEY, '1'); } catch (e) {}
            }

            var shown = false;
            function reveal() {
                if (shown) return;
                shown = true;
                // Tell the theme hint to get out of the way — we don't want two
                // pangolins on screen at the same time on mobile.
                document.dispatchEvent(new CustomEvent('gp:wish-hint-shown'));

                var a = pickAnchor();
                if (a) a.classList.add('gp-wish-btn--hint-pulse');
                hint.hidden = false;
                positionHint();
                requestAnimationFrame(function () {
                    hint.setAttribute('data-shown', '1');
                    hint.setAttribute('aria-hidden', 'false');
                });
                setTimeout(dismiss, AUTO_HIDE_MS);
            }

            if (isMobile && 'IntersectionObserver' in window) {
                // Mobile: wait until the first wishlist heart actually scrolls
                // into the viewport (i.e. the user reaches the Popular Choice
                // section) before showing the hint.
                var io = new IntersectionObserver(function (entries) {
                    for (var i = 0; i < entries.length; i++) {
                        if (entries[i].isIntersecting) {
                            io.disconnect();
                            reveal();
                            break;
                        }
                    }
                }, { threshold: 0.4 });
                io.observe(anchor);
            } else {
                // Desktop (or no IO support): keep the original time-based reveal.
                setTimeout(reveal, SHOW_DELAY_MS);
            }

            // Once shown, if the anchor heart scrolls out of view (the user
            // is moving past the section the hint was pointing at), get out
            // of the way — otherwise the hint stays glued to the heart's
            // document position and clips against the navbar at the top of
            // the viewport, looking like it's "hanging" there.
            if ('IntersectionObserver' in window) {
                var leaveObserver = new IntersectionObserver(function (entries) {
                    if (!shown) return;
                    for (var i = 0; i < entries.length; i++) {
                        if (!entries[i].isIntersecting) {
                            dismiss();
                            leaveObserver.disconnect();
                            break;
                        }
                    }
                }, { threshold: 0 });
                leaveObserver.observe(anchor);
            }

            window.addEventListener('resize',  positionHint, { passive: true });
            window.addEventListener('scroll',  positionHint, { passive: true });

            hint.querySelector('.gp-wish-hint__close').addEventListener('click', function (e) {
                e.stopPropagation();
                dismiss();
            });
            hint.addEventListener('click', dismiss);

            // Any wishlist click — anywhere on the page — counts as "hint received".
            document.addEventListener('click', function (e) {
                if (e.target.closest('.gp-wish-btn')) dismiss();
            }, true);
        });
    })();
</script>
