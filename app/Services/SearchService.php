<?php

namespace App\Services;

use App\Destinations;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class SearchService
{
    /**
     * Search destinations by keyword.
     */
    public function searchDestinations(string $keyword, int $limit = 10): Collection
    {
        return Destinations::query()
            ->where('title', 'like', "%{$keyword}%")
            ->orWhere('description', 'like', "%{$keyword}%")
            ->orWhere('content', 'like', "%{$keyword}%")
            ->limit($limit)
            ->get();
    }

    /**
     * Filter destinations by category.
     */
    public function filterByCategory(int $categoryId): Collection
    {
        return Destinations::where('category_id', $categoryId)->get();
    }

    /**
     * Get featured/popular destinations.
     */
    public function getFeatured(int $limit = 6): Collection
    {
        return Destinations::latest('published_at')
            ->limit($limit)
            ->get();
    }

    /**
     * Get destinations with filters.
     */
    public function getFiltered(array $filters = []): Builder
    {
        $query = Destinations::query();

        if (! empty($filters['category_id'])) {
            $query->where('category_id', $filters['category_id']);
        }

        if (! empty($filters['search'])) {
            $keyword = $filters['search'];
            $query->where(function (Builder $q) use ($keyword) {
                $q->where('title', 'like', "%{$keyword}%")
                    ->orWhere('description', 'like', "%{$keyword}%");
            });
        }

        $sortBy = $filters['sort_by'] ?? 'published_at';
        $sortOrder = $filters['sort_order'] ?? 'desc';
        $query->orderBy($sortBy, $sortOrder);

        return $query;
    }
}
