<?php

namespace App;

use Database\Factories\DestinationsFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

class Destinations extends Model
{
    use HasFactory, SoftDeletes;

    protected static function newFactory()
    {
        return DestinationsFactory::new();
    }

    protected $fillable = [
        'title',
        'description',
        'content',
        'image',
        'published_at',
        'country',
        'sort_order',
    ];

    protected $casts = [
        'published_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'sort_order' => 'integer',
    ];

    /**
     * New destinations slot in at the END of the public order by default
     * (max sort_order + 1) — admins then drag to feature them higher.
     */
    protected static function booted(): void
    {
        static::creating(function (self $row) {
            if ($row->sort_order === null || $row->sort_order === 0) {
                $row->sort_order = (int) static::max('sort_order') + 1;
            }
        });
    }

    /**
     * Get the full image URL, handling both storage and public paths.
     */
    public function getImageUrlAttribute(): string
    {
        if (! $this->image) {
            return asset('images/destination-2.jpg');
        }

        if (str_starts_with($this->image, 'images/') || str_starts_with($this->image, 'http')) {
            return asset($this->image);
        }

        return asset('storage/'.ltrim($this->image, '/'));
    }

    public function deleteImage(): void
    {
        if ($this->image && Storage::disk('public')->exists($this->image)) {
            Storage::disk('public')->delete($this->image);
        }
    }

    /**
     * A destination can have many activities.
     */
    public function activities()
    {
        return $this->hasMany(Activity::class, 'destination_id')->orderBy('sort_order');
    }

    /**
     * A destination can have many reviews.
     */
    public function reviews()
    {
        return $this->hasMany(Review::class, 'destination_id');
    }

    /**
     * A destination can belong to many packages.
     */
    public function packages()
    {
        return $this->belongsToMany(Package::class, 'destination_package', 'destination_id', 'package_id')
            ->withTimestamps();
    }

    /**
     * Scope: only published destinations.
     */
    public function scopePublished($query)
    {
        return $query->whereNotNull('published_at')
            ->where('published_at', '<=', now());
    }

    /**
     * Scope: most recent destinations.
     */
    public function scopeRecent($query, $limit = 10)
    {
        return $query->orderBy('published_at', 'desc')->limit($limit);
    }

    /**
     * Scope: filter by country (optional).
     */
    public function scopeInCountry($query, ?string $country)
    {
        return $country ? $query->where('country', $country) : $query;
    }
}
