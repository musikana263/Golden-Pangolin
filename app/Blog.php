<?php

namespace App;

use Database\Factories\BlogFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

class Blog extends Model
{
    use HasFactory, SoftDeletes;

    protected static function newFactory()
    {
        return BlogFactory::new();
    }

    protected $fillable = [
        'title', 'description', 'content', 'image', 'published_at', 'category_id', 'sort_order',
    ];

    protected $casts = [
        'sort_order'   => 'integer',
        'published_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::creating(function (self $post) {
            if ($post->sort_order === null || $post->sort_order === 0) {
                $post->sort_order = (int) static::max('sort_order') + 1;
            }
        });
    }

    /**
     * delete image from storage
     *
     * @return void
     */
    public function deleteImage()
    {
        if ($this->image && Storage::disk('public')->exists($this->image)) {
            Storage::disk('public')->delete($this->image);
        }
    }

    /**
     * Get the full image URL, handling both storage and public paths.
     */
    public function getImageUrlAttribute(): string
    {
        if (! $this->image) {
            return asset('images/about.jpg');
        }

        if (str_starts_with($this->image, 'images/') || str_starts_with($this->image, 'http')) {
            return asset($this->image);
        }

        return asset('storage/'.$this->image);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Scope for published blog posts.
     */
    public function scopePublished($query)
    {
        return $query->whereNotNull('published_at')
            ->where('published_at', '<=', now());
    }

    /**
     * Scope for recent blog posts.
     */
    public function scopeRecent($query, $limit = 10)
    {
        return $query->orderBy('published_at', 'desc')->limit($limit);
    }

    /**
     * Scope for posts by category.
     */
    public function scopeInCategory($query, $categoryId)
    {
        return $query->where('category_id', $categoryId);
    }
}
