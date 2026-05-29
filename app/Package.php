<?php

namespace App;

use App\Services\TravellerTier;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Package extends Model
{
    use SoftDeletes;

    /** All valid traveller-tier slugs a package can be restricted to. */
    public const ALL_TIERS = ['local', 'african', 'international'];

    protected $fillable = [
        'name',
        'slug',
        'summary',
        'duration',
        'group_size',
        'price_from',
        'accommodation_id',
        'is_active',
        'is_featured',
        'is_hero',
        'tiers',
        'sort_order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_featured' => 'boolean',
        'is_hero' => 'boolean',
        'sort_order' => 'integer',
        'tiers' => 'array',
    ];

    protected static function booted(): void
    {
        static::saving(function (self $package) {
            if (empty($package->slug)) {
                $package->slug = Str::slug($package->name);
            }
        });

        // New packages append to the public list — admins drag to re-order.
        static::creating(function (self $package) {
            if ($package->sort_order === null || $package->sort_order === 0) {
                $package->sort_order = (int) static::max('sort_order') + 1;
            }
        });
    }

    /**
     * A package can belong to many destinations.
     */
    public function destinations(): BelongsToMany
    {
        return $this->belongsToMany(Destinations::class, 'destination_package', 'package_id', 'destination_id')
            ->withPivot('sort_order')
            ->withTimestamps()
            ->orderBy('pivot_sort_order');
    }

    /**
     * Country comes from the package's first destination.
     */
    public function getCountryAttribute(): ?string
    {
        $first = $this->relationLoaded('destinations')
            ? $this->destinations->first()
            : $this->destinations()->first();

        return $first?->country;
    }

    /**
     * All distinct countries spanned by this package's destinations.
     */
    public function getCountriesAttribute(): array
    {
        return ($this->relationLoaded('destinations') ? $this->destinations : $this->destinations()->get())
            ->pluck('country')
            ->filter()
            ->unique()
            ->values()
            ->all();
    }

    /**
     * A package can have many activities.
     */
    public function activities(): BelongsToMany
    {
        return $this->belongsToMany(Activity::class, 'activity_package')
            ->withPivot('sort_order')
            ->orderBy('pivot_sort_order');
    }

    /**
     * A package can have one accommodation.
     */
    public function accommodation(): BelongsTo
    {
        return $this->belongsTo(Accommodation::class);
    }

    /**
     * A package can have many media files (images).
     */
    public function media(): MorphMany
    {
        return $this->morphMany(Media::class, 'mediable')->orderBy('sort_order');
    }

    public function getCoverUrlAttribute(): ?string
    {
        $first = $this->media->first();
        if ($first) {
            return $first->url;
        }

        // Fallback: first activity's destination image
        return $this->activities->first()?->destination?->image_url;
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeFeatured(Builder $query): Builder
    {
        return $query->where('is_featured', true);
    }

    public function scopeHero(Builder $query): Builder
    {
        return $query->where('is_hero', true);
    }

    /**
     * Returns true if this package is offered to the given traveller tier.
     * A NULL/empty `tiers` column means "available to everyone" (legacy
     * packages from before tier-restriction existed).
     */
    public function isAvailableTo(?string $tier): bool
    {
        $tier = $tier ?: 'international';
        $allowed = $this->tiers ?? [];
        return empty($allowed) || in_array($tier, $allowed, true);
    }

    /**
     * Scope: only packages available to the given tier. NULL `tiers` rows
     * (legacy) match every tier so they stay visible to everyone.
     */
    public function scopeAvailableTo(Builder $query, ?string $tier): Builder
    {
        $tier = $tier ?: 'international';
        return $query->where(function (Builder $q) use ($tier) {
            $q->whereNull('tiers')
              ->orWhereJsonContains('tiers', $tier);
        });
    }

    /** Convenience: scope by the current visitor's tier. */
    public function scopeAvailableToVisitor(Builder $query): Builder
    {
        return $query->availableTo(app(TravellerTier::class)->currentOrDefault());
    }

    /**
     * Mark this package as the homepage hero, and demote any other package
     * currently in that slot. Always wrap the two writes in a transaction so
     * we can never end up with zero or two hero packages.
     */
    public function promoteToHero(): void
    {
        static::getConnection()->transaction(function () {
            static::where('id', '!=', $this->id)
                ->where('is_hero', true)
                ->update(['is_hero' => false]);
            $this->forceFill(['is_hero' => true])->save();
        });
    }

    /**
     * Filter packages whose destinations are in a given country.
     */
    public function scopeInCountry(Builder $query, ?string $country): Builder
    {
        if (! $country) {
            return $query;
        }

        return $query->whereHas('destinations', fn ($q) => $q->where('country', $country));
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }
}
