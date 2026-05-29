<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class AccommodationType extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'icon',
        'description',
    ];

    protected static function booted(): void
    {
        static::saving(function (self $type) {
            if (empty($type->slug)) {
                $type->slug = Str::slug($type->name);
            }
        });
    }

    public function accommodations(): HasMany
    {
        return $this->hasMany(Accommodation::class);
    }
}
