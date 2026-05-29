<?php

namespace App\Http\Controllers\Api;

use App\Destinations;
use App\Http\Controllers\Controller;
use App\Http\Resources\DestinationCollection;
use App\Http\Resources\DestinationResource;
use App\Services\SearchService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DestinationApiController extends Controller
{
    protected SearchService $searchService;

    public function __construct(SearchService $searchService)
    {
        $this->searchService = $searchService;
    }

    /**
     * List all destinations with filtering and pagination.
     */
    /** Columns the public API is allowed to sort by. Anything else 400s. */
    private const ALLOWED_SORTS = ['published_at', 'title', 'created_at', 'id'];

    public function index(Request $request): DestinationCollection
    {
        $sortBy = $request->input('sort_by', 'published_at');
        abort_unless(in_array($sortBy, self::ALLOWED_SORTS, true), 400, 'Invalid sort_by');

        $sortOrder = strtolower($request->input('sort_order', 'desc'));
        if (! in_array($sortOrder, ['asc', 'desc'], true)) {
            $sortOrder = 'desc';
        }

        $perPage = min(50, max(1, (int) $request->input('per_page', 10)));

        $query = $this->searchService->getFiltered([
            'category_id' => $request->input('category'),
            'search'      => $request->input('search'),
            'sort_by'     => $sortBy,
            'sort_order'  => $sortOrder,
        ]);

        $destinations = $query->with(['category'])
            ->published()
            ->paginate($perPage);

        return new DestinationCollection($destinations);
    }

    /**
     * Get a single destination with its details.
     */
    public function show(Destinations $destination): DestinationResource
    {
        $destination->load(['category', 'reviews.user']);

        return new DestinationResource($destination);
    }

    /**
     * Get featured destinations.
     */
    public function featured(Request $request): JsonResponse
    {
        $limit = min(50, max(1, (int) $request->input('limit', 6)));

        $destinations = Destinations::published()
            ->recent($limit)
            ->with(['category'])
            ->get();

        return response()->json([
            'data' => DestinationResource::collection($destinations),
        ]);
    }

    /**
     * Search destinations.
     */
    public function search(Request $request): JsonResponse
    {
        $request->validate([
            'q' => 'required|string|min:2',
        ]);

        $destinations = $this->searchService->searchDestinations(
            $request->input('q'),
            $request->input('limit', 10)
        );

        return response()->json([
            'data' => DestinationResource::collection($destinations),
            'query' => $request->input('q'),
        ]);
    }
}
