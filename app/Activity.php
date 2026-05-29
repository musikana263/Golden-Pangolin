<?php

namespace App;

use App\Services\TravellerTier;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Str;

class Activity extends Model
{
    /** Map tier slug → DB column. Lives on the model so views never see strings. */
    public const PRICE_COLUMNS = [
        'local'         => 'price_local',
        'african'       => 'price_african',
        'international' => 'price_international',
    ];

    /** Pricing modes — see migration 2026_05_25_000004 for the semantics. */
    public const PRICE_MODE_TIERED = 'tiered';
    public const PRICE_MODE_FIXED  = 'fixed';
    public const PRICE_MODE_FROM   = 'from';
    public const PRICE_MODE_FREE   = 'free';

    public const PRICE_MODES = [
        self::PRICE_MODE_FIXED  => 'Fixed (same price for everyone)',
        self::PRICE_MODE_FREE   => 'Free (no charge for anyone)',
        self::PRICE_MODE_TIERED => 'Tiered (different price per traveller tier — leave a tier blank to hide; set 0 = free for that tier)',
        self::PRICE_MODE_FROM   => 'From… (approximate label, shown to everyone)',
    ];

    protected $fillable = [
        'destination_id',
        'category_id',
        'name',
        'slug',
        'description',
        'duration',
        'price_mode',
        'unit_price',
        'price_from',          // free-text, used when price_mode = 'from'
        'price_local',
        'price_african',
        'price_international',
        'park_fee',
        'park_fee_label',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer',
        'unit_price' => 'decimal:2',
        'price_local' => 'decimal:2',
        'price_african' => 'decimal:2',
        'price_international' => 'decimal:2',
        'park_fee' => 'decimal:2',
    ];

    protected static function booted(): void
    {
        static::saving(function (self $activity) {
            if (empty($activity->slug)) {
                $activity->slug = Str::slug($activity->name);
            }
        });
    }

    public function destination(): BelongsTo
    {
        return $this->belongsTo(Destinations::class, 'destination_id');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function packages(): BelongsToMany
    {
        return $this->belongsToMany(Package::class, 'activity_package')
            ->withPivot('sort_order')
            ->orderBy('pivot_sort_order');
    }

    public function media(): MorphMany
    {
        return $this->morphMany(Media::class, 'mediable')->orderBy('sort_order');
    }

    public function getCoverUrlAttribute(): ?string
    {
        return $this->media->first()?->url;
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    /**
     * Visitor-facing scope. Hide activities that have no price for the given
     * traveller tier so each visitor only sees activities they can actually
     * book at a known price.
     *
     * `fixed` and `from` mode activities are visible to ALL tiers — their
     * price is the same regardless of where the visitor is travelling from.
     * Only `tiered` activities need a per-tier column to be set.
     *
     * Admin queries should NOT chain this.
     */
    public function scopeAvailableTo(Builder $query, ?string $tier): Builder
    {
        $tier = $tier ?? 'international';
        $column = self::PRICE_COLUMNS[$tier] ?? self::PRICE_COLUMNS['international'];

        return $query->where(function (Builder $q) use ($column) {
            $q->whereIn('price_mode', [
                  self::PRICE_MODE_FIXED,
                  self::PRICE_MODE_FROM,
                  self::PRICE_MODE_FREE,
              ])
              ->orWhere(function (Builder $tq) use ($column) {
                  // legacy rows with no price_mode set, or explicit 'tiered'
                  $tq->where(function (Builder $sub) {
                          $sub->whereNull('price_mode')
                              ->orWhere('price_mode', self::PRICE_MODE_TIERED);
                      })
                      ->whereNotNull($column);
              });
        });
    }

    /** Convenience scope: reads the current visitor's tier from the service. */
    public function scopeAvailableToVisitor(Builder $query): Builder
    {
        return $query->availableTo(app(TravellerTier::class)->currentOrDefault());
    }

    /** Numeric price for the current visitor. Null if none configured. */
    public function getApplicablePriceAttribute(): ?string
    {
        // Mode-driven price first — keeps cards consistent with the picker.
        if ($this->price_mode === self::PRICE_MODE_FIXED) {
            return $this->unit_price !== null ? (string) $this->unit_price : null;
        }
        if ($this->price_mode === self::PRICE_MODE_FREE) {
            return '0';
        }
        if ($this->price_mode === self::PRICE_MODE_FROM) {
            // 'from' is text only; numeric is unavailable.
            return null;
        }

        // Tiered (or legacy null mode) — look up the visitor's column.
        $tier = app(TravellerTier::class)->currentOrDefault();
        $col = self::PRICE_COLUMNS[$tier];

        return $this->{$col};
    }

    /** Pretty-printed price for cards/lists. Falls back to legacy `price_from`. */
    public function getApplicablePriceFormattedAttribute(): string
    {
        if ($this->price_mode === self::PRICE_MODE_FREE) {
            return 'Free';
        }
        if ($this->price_mode === self::PRICE_MODE_FROM) {
            return $this->price_from ?: 'On request';
        }

        $price = $this->applicable_price;

        if ($price !== null) {
            // Any zero amount (tier price set to 0 or fixed mode at 0) means
            // free for the visitor — don't print "$0.00".
            if ((float) $price === 0.0) {
                return 'Free';
            }
            return '$'.number_format((float) $price, 2);
        }

        return $this->price_from ?: 'On request';
    }

    /** True if this activity charges a park / conservation fee. */
    public function hasParkFee(): bool
    {
        return $this->park_fee !== null && (float) $this->park_fee > 0;
    }

    /** Pretty-printed park fee (e.g. "$20.00"). Empty string if none. */
    public function getParkFeeFormattedAttribute(): string
    {
        return $this->hasParkFee()
            ? '$'.number_format((float) $this->park_fee, 2)
            : '';
    }

    /** Label to use next to the park fee (admin override, with sensible default). */
    public function getParkFeeDisplayLabelAttribute(): string
    {
        return $this->park_fee_label ?: 'Park fee';
    }
}
