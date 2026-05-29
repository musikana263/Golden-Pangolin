<?php

namespace App;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Accommodation extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'accommodation_type_id',
        'name',
        'slug',
        'country',
        'location',
        'description',
        'content',
        'price_from',
        'amenities',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'amenities' => 'array',
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    protected static function booted(): void
    {
        static::saving(function (self $accommodation) {
            if (empty($accommodation->slug)) {
                $accommodation->slug = Str::slug($accommodation->name);
            }
        });

        static::creating(function (self $accommodation) {
            if ($accommodation->sort_order === null || $accommodation->sort_order === 0) {
                $accommodation->sort_order = (int) static::max('sort_order') + 1;
            }
        });
    }

    public function type(): BelongsTo
    {
        return $this->belongsTo(AccommodationType::class, 'accommodation_type_id');
    }

    public function media(): MorphMany
    {
        return $this->morphMany(Media::class, 'mediable')->orderBy('sort_order');
    }

    public function getCoverUrlAttribute(): ?string
    {
        $first = $this->media->first();

        return $first ? $first->url : null;
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeOfType(Builder $query, ?int $typeId): Builder
    {
        return $typeId ? $query->where('accommodation_type_id', $typeId) : $query;
    }

    public function scopeInCountry(Builder $query, ?string $country): Builder
    {
        return $country ? $query->where('country', $country) : $query;
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }
}
