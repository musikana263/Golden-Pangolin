@php
    // `$mode` is passed from layouts/front.blade.php — either 'wishlist' or 'chat'.
    $mode = $mode ?? 'chat';

    if ($mode === 'wishlist') {
        $waUrl = route('wishlist.whatsapp');
        $waAria = 'Send your wishlist on WhatsApp';
    } else {
        $waNumber = site_whatsapp_number();
        $waMessage = rawurlencode(config('site.whatsapp.default_message'));
        $waUrl = "https://wa.me/{$waNumber}?text={$waMessage}";
        $waAria = 'Chat with us on WhatsApp';
    }
@endphp

<a href="{{ $waUrl }}"
   class="gp-whatsapp-float gp-whatsapp-float--{{ $mode }}"
   @if ($mode === 'chat') target="_blank" rel="noopener" @endif
   aria-label="{{ $waAria }}"
   title="{{ $waAria }}">
    @if ($mode === 'wishlist')
        <span class="gp-whatsapp-float__stack">
            <i class="fas fa-heart gp-whatsapp-float__heart"></i>
            <i class="fab fa-whatsapp"></i>
        </span>
        <span class="gp-whatsapp-float-label">Send wishlist</span>
    @else
        <i class="fab fa-whatsapp"></i>
    @endif
</a>

<style>
    /* Base — chat mode is now a clean circular FAB, no label. */
    .gp-whatsapp-float {
        position: fixed;
        bottom: 24px;
        right: 24px;
        z-index: 1040;
        width: 60px;
        height: 60px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
        background: #25D366;
        color: #fff !important;
        text-decoration: none;
        font-weight: 600;
        box-shadow: 0 6px 22px rgba(37, 211, 102, 0.45), 0 2px 6px rgba(0, 0, 0, 0.15);
        transition: transform 0.2s ease, box-shadow 0.2s ease, filter 0.15s ease;
    }
    .gp-whatsapp-float:hover {
        transform: translateY(-2px) scale(1.05);
        box-shadow: 0 12px 30px rgba(37, 211, 102, 0.55), 0 3px 10px rgba(0, 0, 0, 0.18);
    }
    .gp-whatsapp-float i { font-size: 1.85rem; }

    /* Subtle pulse so the FAB doesn't blend into the page edges. */
    .gp-whatsapp-float--chat::before {
        content: "";
        position: absolute;
        inset: 0;
        border-radius: 50%;
        box-shadow: 0 0 0 0 rgba(37, 211, 102, 0.55);
        animation: gpWaPulse 2.6s ease-out infinite;
        pointer-events: none;
    }
    @keyframes gpWaPulse {
        0%   { box-shadow: 0 0 0 0 rgba(37, 211, 102, 0.55); }
        70%  { box-shadow: 0 0 0 16px rgba(37, 211, 102, 0); }
        100% { box-shadow: 0 0 0 0 rgba(37, 211, 102, 0); }
    }
    .gp-whatsapp-float-label { display: none; }

    /* Wishlist mode — WhatsApp green pill with a tiny red heart on the icon,
       so it reads as "send your saved items via WhatsApp". */
    .gp-whatsapp-float--wishlist {
        width: auto;
        height: auto;
        padding: 12px 20px 12px 16px;
        border-radius: 999px;
        gap: 10px;
        background: linear-gradient(135deg, #25D366, #1ebe5b);
        box-shadow: 0 6px 22px rgba(37, 211, 102, 0.45), 0 2px 6px rgba(0, 0, 0, 0.15);
        color: #fff !important;
    }
    .gp-whatsapp-float--wishlist:hover {
        box-shadow: 0 12px 30px rgba(37, 211, 102, 0.55), 0 3px 10px rgba(0, 0, 0, 0.18);
        filter: brightness(1.05);
    }
    .gp-whatsapp-float--wishlist .gp-whatsapp-float-label {
        display: inline;
        font-size: .92rem;
    }
    .gp-whatsapp-float__stack {
        position: relative;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 28px;
        height: 28px;
    }
    .gp-whatsapp-float__stack i { font-size: 1.5rem; }
    .gp-whatsapp-float__heart {
        position: absolute;
        top: -4px;
        right: -6px;
        font-size: .95rem !important;
        color: #d9534f;
        background: transparent;
        text-shadow: 0 1px 2px rgba(0, 0, 0, .4);
    }

    @media (max-width: 575.98px) {
        .gp-whatsapp-float {
            width: 54px;
            height: 54px;
            bottom: 18px;
            right: 18px;
        }
        .gp-whatsapp-float i { font-size: 1.6rem; }
        .gp-whatsapp-float--wishlist {
            width: auto;
            height: auto;
            padding: 11px 16px 11px 14px;
        }
    }

    @media (prefers-reduced-motion: reduce) {
        .gp-whatsapp-float--chat::before { animation: none; }
    }
</style>
