<?php

namespace App\Http\Controllers\Admin;

use App\Accommodation;
use App\Activity;
use App\Blog;
use App\Destinations;
use App\Http\Controllers\Controller;
use App\Package;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * Generic drag-and-drop reorder endpoint. Front-end JS POSTs the new id
 * order; we whitelist the resource string to a known Eloquent model and
 * update `sort_order` in a single transaction so the public list reflects
 * the new arrangement immediately.
 */
class SortController extends Controller
{
    /**
     * Map of allowed resource slugs to their Eloquent model classes. Keeps
     * the URL clean (`/admin/sort/destinations`) and prevents the user from
     * passing an arbitrary class name and sort-updating tables we don't
     * intend (users, login_otps, etc).
     */
    private const RESOURCES = [
        'destinations'   => Destinations::class,
        'activities'     => Activity::class,
        'packages'       => Package::class,
        'accommodations' => Accommodation::class,
        'blogs'          => Blog::class,
    ];

    public function update(Request $request, string $resource): JsonResponse
    {
        if (! isset(self::RESOURCES[$resource])) {
            return response()->json(['ok' => false, 'error' => 'Unknown resource'], 422);
        }

        $data = $request->validate([
            'ids'   => 'required|array|min:1|max:500',
            'ids.*' => 'required|integer',
        ]);

        $model = self::RESOURCES[$resource];

        // Single transaction: number the supplied ids 1..N. Any row whose id
        // isn't in the list is left untouched (preserves order of items on
        // other pages when paginated, though most admin lists aren't paged).
        DB::transaction(function () use ($model, $data) {
            foreach ($data['ids'] as $position => $id) {
                $model::where('id', $id)->update(['sort_order' => $position + 1]);
            }
        });

        return response()->json(['ok' => true, 'count' => count($data['ids'])]);
    }
}
