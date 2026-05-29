<?php

namespace App\Services;

use Illuminate\Cookie\CookieJar;
use Illuminate\Http\Request;

/**
 * Resolves and persists the visitor's traveller tier.
 *
 * Tiers (canonical strings stored on user / cookie):
 *   - 'local'         → Zimbabwean / local resident
 *   - 'african'       → African national (non-local)
 *   - 'international' → Everyone else
 *
 * Resolution order in current():
 *   1. The signed-in user's saved `traveller_tier`
 *   2. The unsigned cookie `gp_traveller_tier`
 *   3. null  (caller decides the default — usually 'international' for pricing,
 *      or "ask the user" if they haven't picked yet)
 */
class TravellerTier
{
    public const COOKIE = 'gp_traveller_tier';
    public const COOKIE_TTL_MINUTES = 60 * 24 * 365; // 1 year

    public const TIERS = [
        'local'         => 'Local traveller',
        'african'       => 'Regional traveller',
        'international' => 'International traveller',
    ];

    public function __construct(
        private Request $request,
        private CookieJar $cookies,
    ) {}

    /** Get the current visitor's tier, or null if they have not chosen yet. */
    public function current(): ?string
    {
        $user = $this->request->user();
        if ($user && $this->isValid($user->traveller_tier ?? null)) {
            return $user->traveller_tier;
        }

        $fromCookie = $this->request->cookie(self::COOKIE);
        return $this->isValid($fromCookie) ? $fromCookie : null;
    }

    /**
     * The tier to use when displaying prices. Falls back to 'international'
     * when the visitor hasn't picked one (safe default — anyone who hasn't
     * told us where they're from is treated as an international guest).
     */
    public function currentOrDefault(): string
    {
        return $this->current() ?? 'international';
    }

    /** Friendly label for the current tier, suitable for UI. */
    public function label(?string $tier = null): string
    {
        $tier = $tier ?? $this->currentOrDefault();
        return self::TIERS[$tier] ?? self::TIERS['international'];
    }

    /**
     * Save the tier. Always writes the cookie; also persists onto the user
     * record when one is signed in so the choice follows them across devices.
     */
    public function set(string $tier): void
    {
        if (! $this->isValid($tier)) {
            return;
        }

        // Queue a cookie that gets attached to the next response.
        $this->cookies->queue($this->cookies->make(
            self::COOKIE,
            $tier,
            self::COOKIE_TTL_MINUTES,
            '/',
            null,        // domain
            null,        // secure: auto (true behind HTTPS)
            false,       // httpOnly: false — JS reads it for the navbar indicator
            false,
            'lax',
        ));

        $user = $this->request->user();
        if ($user && $user->traveller_tier !== $tier) {
            $user->forceFill(['traveller_tier' => $tier])->save();
        }
    }

    public function clear(): void
    {
        $this->cookies->queue($this->cookies->forget(self::COOKIE));
        $user = $this->request->user();
        if ($user && $user->traveller_tier !== null) {
            $user->forceFill(['traveller_tier' => null])->save();
        }
    }

    public function isValid(?string $tier): bool
    {
        return $tier !== null && array_key_exists($tier, self::TIERS);
    }
}
