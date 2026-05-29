@php
    // $item: the model (Package, Activity, or Accommodation)
    // $type: 'package' | 'activity' | 'accommodation'
    // Wishlist is a guest-facing feature — admins viewing the site shouldn't
    // see hearts on cards.
    $hideWishBtn = auth()->check() && auth()->user()->isAdmin();
    $wishlistIds = $wishlistIds ?? ['package' => [], 'activity' => [], 'accommodation' => []];
    $idsForType = $wishlistIds[$type] ?? [];
    $inWishlist = in_array($item->id, $idsForType, true);
    $routeMap = [
        'package'       => 'wishlist.packages.toggle',
        'activity'      => 'wishlist.activities.toggle',
        'accommodation' => 'wishlist.accommodations.toggle',
    ];
    $route = $routeMap[$type] ?? 'wishlist.packages.toggle';
@endphp

@unless ($hideWishBtn)

<form action="{{ route($route, $item) }}" method="POST" class="gp-wish-form d-inline">
    @csrf
    <button type="submit"
            class="gp-wish-btn {{ $inWishlist ? 'is-active' : '' }}"
            data-wish-active="{{ $inWishlist ? '1' : '0' }}"
            title="{{ $inWishlist ? 'Remove from wishlist' : 'Add to wishlist' }}"
            aria-label="{{ $inWishlist ? 'Remove from wishlist' : 'Add to wishlist' }}">
        <i class="{{ $inWishlist ? 'fas' : 'far' }} fa-heart"></i>
    </button>
</form>
@endunless

<style>
    .gp-wish-btn {
        background: rgba(255, 255, 255, 0.92);
        border: none;
        width: 38px;
        height: 38px;
        border-radius: 999px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        color: #d9534f;
        cursor: pointer;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.12);
        transition: transform 0.15s ease, background 0.2s ease;
    }
    .gp-wish-btn:hover { transform: scale(1.08); }
    .gp-wish-btn.is-active { background: #d9534f; color: #fff; }
    /* Pop animation when the heart flips on. */
    .gp-wish-btn--pop { animation: gpWishPop .35s ease; }
    @keyframes gpWishPop {
        0%   { transform: scale(1); }
        45%  { transform: scale(1.32); }
        100% { transform: scale(1); }
    }
    html[data-theme="dark"] .gp-wish-btn { background: rgba(24, 24, 27, 0.92); }
    html[data-theme="dark"] .gp-wish-btn.is-active { background: #d9534f; color: #fff; }

    /* Fly-to-wishlist heart — a tiny copy of the heart that arcs from the
       clicked button toward the wishlist icon in the navbar. */
    .gp-fly-heart {
        position: fixed;
        z-index: 1060;
        width: 28px;
        height: 28px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 1.4rem;
        color: #d9534f;
        pointer-events: none;
        transform: translate(-50%, -50%) scale(1);
        transition:
            top .7s cubic-bezier(.55, -.05, .35, 1.05),
            left .7s cubic-bezier(.55, .05, .85, .6),
            transform .7s ease-in,
            opacity .25s ease;
        filter: drop-shadow(0 4px 8px rgba(217, 83, 79, .45));
        will-change: top, left, transform, opacity;
    }
    .gp-fly-heart--landing {
        transform: translate(-50%, -50%) scale(.35);
        opacity: 0;
    }

    /* Target wiggle when a heart lands. */
    .gp-wish-target--bump {
        animation: gpWishTargetBump .55s ease;
    }
    @keyframes gpWishTargetBump {
        0%, 100% { transform: scale(1); }
        30%      { transform: scale(1.25); }
        60%      { transform: scale(.92); }
    }

    /* Badge pop when the counter changes. */
    .gp-wishlist-badge--pop {
        animation: gpWishBadgePop .45s ease;
    }
    @keyframes gpWishBadgePop {
        0%   { transform: scale(1); }
        45%  { transform: scale(1.45); }
        100% { transform: scale(1); }
    }

    /* Floating confirmation toast for ajax wishlist toggles. */
    .gp-wish-toast {
        position: fixed;
        bottom: 92px;
        right: 24px;
        z-index: 1045;
        background: linear-gradient(135deg, #1a1208, #2a1e10);
        color: #fbf3e1;
        border: 1px solid rgba(212, 160, 60, .45);
        border-radius: 12px;
        padding: 10px 16px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, .35);
        font-family: 'Inter', system-ui, sans-serif;
        font-size: .9rem;
        display: inline-flex;
        align-items: center;
        gap: .55rem;
        opacity: 0;
        transform: translateY(8px);
        transition: opacity .25s ease, transform .25s ease;
        pointer-events: none;
    }
    .gp-wish-toast[data-shown="1"] {
        opacity: 1;
        transform: translateY(0);
    }
    .gp-wish-toast i { color: #f3c779; }
    @media (max-width: 575.98px) {
        .gp-wish-toast { right: 16px; bottom: 78px; font-size: .82rem; }
    }
</style>

@once
    {{-- Installs once per page. Intercepts every .gp-wish-form submit, posts
         the toggle via fetch (no reload, no scroll-to-top), and flips the
         button state in place. Falls back to a normal submit if JS is off. --}}
    <script>
        (function () {
            var inflight = new WeakSet();

            function toast(message, success) {
                var el = document.getElementById('gp-wish-toast');
                if (!el) {
                    el = document.createElement('div');
                    el.id = 'gp-wish-toast';
                    el.className = 'gp-wish-toast';
                    document.body.appendChild(el);
                }
                el.innerHTML = (success
                    ? '<i class="fas fa-heart"></i>'
                    : '<i class="fas fa-info-circle"></i>') + ' ' + message;
                requestAnimationFrame(function () { el.setAttribute('data-shown', '1'); });
                clearTimeout(el._t);
                el._t = setTimeout(function () { el.removeAttribute('data-shown'); }, 2200);
            }

            function setState(btn, active) {
                btn.dataset.wishActive = active ? '1' : '0';
                btn.classList.toggle('is-active', active);
                var label = active ? 'Remove from wishlist' : 'Add to wishlist';
                btn.setAttribute('aria-label', label);
                btn.setAttribute('title', label);
                var icon = btn.querySelector('i');
                if (icon) {
                    icon.classList.toggle('fas', active);
                    icon.classList.toggle('far', !active);
                }
                if (active) {
                    btn.classList.remove('gp-wish-btn--pop');
                    // force reflow to restart animation
                    void btn.offsetWidth;
                    btn.classList.add('gp-wish-btn--pop');
                }
            }

            // Visible wishlist target — capsule heart on mobile, classic
            // wishlist link on desktop. Returns whichever is currently rendered.
            function visibleWishTarget() {
                var candidates = [
                    document.querySelector('.gp-qa-btn--heart'),     // mobile capsule
                    document.querySelector('.gp-wishlist-link'),     // desktop classic
                ];
                for (var i = 0; i < candidates.length; i++) {
                    var el = candidates[i];
                    if (!el) continue;
                    var r = el.getBoundingClientRect();
                    if (r.width > 0 && r.height > 0) return el;
                }
                return null;
            }

            function bumpBadge(target, count) {
                if (!target) return;
                var badge = target.querySelector('.gp-wishlist-badge');
                if (count > 0) {
                    if (!badge) {
                        badge = document.createElement('span');
                        badge.className = 'gp-wishlist-badge';
                        target.appendChild(badge);
                    }
                    badge.textContent = count;
                    badge.classList.remove('gp-wishlist-badge--pop');
                    void badge.offsetWidth;
                    badge.classList.add('gp-wishlist-badge--pop');
                } else if (badge) {
                    badge.remove();
                }
            }

            function updateBadge(count) {
                // Update EVERY potential badge (both capsule + classic) so
                // the count stays in sync if the user rotates from phone to laptop.
                document.querySelectorAll('.gp-qa-btn--heart, .gp-wishlist-link').forEach(function (target) {
                    var badge = target.querySelector('.gp-wishlist-badge');
                    if (count > 0) {
                        if (!badge) {
                            badge = document.createElement('span');
                            badge.className = 'gp-wishlist-badge';
                            target.appendChild(badge);
                        }
                        badge.textContent = count;
                    } else if (badge) {
                        badge.remove();
                    }
                });
            }

            // Fly a little heart from the clicked button to the wishlist icon,
            // then pop the badge count. No-op if user prefers reduced motion.
            function flyToWishlist(sourceBtn) {
                if (window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
                    return;
                }
                var target = visibleWishTarget();
                if (!target || !sourceBtn) return;

                var from = sourceBtn.getBoundingClientRect();
                var to   = target.getBoundingClientRect();

                var heart = document.createElement('span');
                heart.className = 'gp-fly-heart';
                heart.innerHTML = '<i class="fas fa-heart"></i>';
                heart.style.left = (from.left + from.width / 2) + 'px';
                heart.style.top  = (from.top  + from.height / 2) + 'px';
                document.body.appendChild(heart);

                // Two-stage transition: first frame sets starting position,
                // next frame triggers the move so CSS can transition between them.
                requestAnimationFrame(function () {
                    requestAnimationFrame(function () {
                        heart.style.left = (to.left + to.width / 2) + 'px';
                        heart.style.top  = (to.top  + to.height / 2) + 'px';
                        heart.classList.add('gp-fly-heart--landing');
                    });
                });

                // Mid-flight: ~600ms after start, bump the target and pop the badge.
                setTimeout(function () {
                    target.classList.remove('gp-wish-target--bump');
                    void target.offsetWidth;
                    target.classList.add('gp-wish-target--bump');
                    var badge = target.querySelector('.gp-wishlist-badge');
                    if (badge) {
                        badge.classList.remove('gp-wishlist-badge--pop');
                        void badge.offsetWidth;
                        badge.classList.add('gp-wishlist-badge--pop');
                    }
                }, 580);

                // Clean up.
                setTimeout(function () { heart.remove(); }, 950);
            }

            document.addEventListener('submit', function (e) {
                var form = e.target.closest('.gp-wish-form');
                if (!form) return;
                if (inflight.has(form)) { e.preventDefault(); return; }
                inflight.add(form);

                e.preventDefault();
                var btn = form.querySelector('.gp-wish-btn');
                var tokenInput = form.querySelector('input[name="_token"]');
                var token = tokenInput ? tokenInput.value : '';

                fetch(form.action, {
                    method: 'POST',
                    credentials: 'same-origin',
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': token,
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                    body: new FormData(form),
                })
                .then(function (r) {
                    if (!r.ok) throw new Error('Network');
                    return r.json();
                })
                .then(function (data) {
                    setState(btn, !!data.added);
                    if (data.added) {
                        // Fly animation will land on the navbar heart and pop
                        // the badge there. Update the count first so the
                        // badge shows the new number when it pops.
                        if (typeof data.count === 'number') updateBadge(data.count);
                        flyToWishlist(btn);
                    } else {
                        if (typeof data.count === 'number') updateBadge(data.count);
                    }
                    toast(data.message || (data.added ? 'Added to wishlist' : 'Removed from wishlist'), data.added);
                })
                .catch(function () {
                    // Fall back to a real submit if the ajax round-trip fails.
                    form.removeEventListener('submit', arguments.callee);
                    form.submit();
                })
                .finally(function () { inflight.delete(form); });
            });
        })();
    </script>
@endonce
