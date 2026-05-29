<?php

namespace App\Http\Controllers\Admin;

use App\Activity;
use App\Category;
use App\Destinations;
use App\Http\Controllers\Controller;
use App\Http\Requests\Activity\ActivityRequest;
use App\Media;
use App\Services\ImageService;
use Illuminate\Support\Str;

class ActivitiesController extends Controller
{
    public function __construct(private ImageService $imageService) {}

    public function index()
    {
        $activities = Activity::with('destination', 'category', 'media')
            ->orderBy('destination_id')
            ->orderBy('sort_order')
            ->paginate(20);

        return view('admin.activities.index', compact('activities'));
    }

    public function create()
    {
        return view('admin.activities.create', [
            'destinations' => Destinations::orderBy('title')->get(),
            'categories' => Category::orderBy('name')->get(),
            'selectedDestinationId' => request('destination_id'),
        ]);
    }

    public function store(ActivityRequest $request)
    {
        $data = $this->pricingPayload($request);
        $data += $request->only([
            'name', 'destination_id', 'category_id', 'description', 'duration',
        ]);
        $data += $this->parkFeePayload($request);
        $data['slug'] = $this->uniqueSlug($data['destination_id'], $data['name']);
        $data['is_active'] = $request->boolean('is_active');

        $activity = Activity::create($data);

        $this->syncImages($activity, $request);

        session()->flash('success', 'Activity added.');

        return redirect(route('activities.edit', $activity));
    }

    public function edit(Activity $activity)
    {
        return view('admin.activities.create', [
            'activity' => $activity->load('media'),
            'destinations' => Destinations::orderBy('title')->get(),
            'categories' => Category::orderBy('name')->get(),
        ]);
    }

    public function update(ActivityRequest $request, Activity $activity)
    {
        $data = $this->pricingPayload($request);
        $data += $request->only([
            'name', 'destination_id', 'category_id', 'description', 'duration',
        ]);
        $data += $this->parkFeePayload($request);
        $data['is_active'] = $request->boolean('is_active');

        if ($activity->name !== $request->name || $activity->destination_id !== (int) $request->destination_id) {
            $data['slug'] = $this->uniqueSlug($request->destination_id, $request->name, $activity->id);
        }

        $activity->update($data);

        if ($request->filled('delete_media')) {
            $toDelete = Media::whereIn('id', $request->input('delete_media'))
                ->where('mediable_type', Activity::class)
                ->where('mediable_id', $activity->id)
                ->get();
            foreach ($toDelete as $m) {
                $m->deleteFile();
                $m->delete();
            }
        }

        $this->syncImages($activity, $request);

        session()->flash('success', 'Activity updated.');

        return redirect(route('activities.edit', $activity));
    }

    public function destroy(Activity $activity)
    {
        foreach ($activity->media as $m) {
            $m->deleteFile();
            $m->delete();
        }
        $activity->delete();

        session()->flash('success', 'Activity deleted.');

        return redirect(route('activities.index'));
    }

    /**
     * Build the pricing-related part of the payload based on the chosen mode.
     * Fields not used by the current mode are explicitly nulled so switching
     * modes doesn't leave stale data behind (e.g. old tier prices showing up
     * silently after the admin moves a row to 'fixed').
     */
    private function pricingPayload(ActivityRequest $request): array
    {
        $mode = $request->input('price_mode', Activity::PRICE_MODE_FIXED);

        $payload = [
            'price_mode'          => $mode,
            'unit_price'          => null,
            'price_from'          => null,
            'price_local'         => null,
            'price_african'       => null,
            'price_international' => null,
        ];

        switch ($mode) {
            case Activity::PRICE_MODE_FIXED:
                $payload['unit_price'] = $request->input('unit_price');
                break;
            case Activity::PRICE_MODE_FROM:
                $payload['price_from'] = $request->input('price_from');
                break;
            case Activity::PRICE_MODE_TIERED:
                $payload['price_local']         = $request->filled('price_local')         ? $request->input('price_local')         : null;
                $payload['price_african']       = $request->filled('price_african')       ? $request->input('price_african')       : null;
                $payload['price_international'] = $request->filled('price_international') ? $request->input('price_international') : null;
                break;
            case Activity::PRICE_MODE_FREE:
                // All pricing inputs stay null — the mode flag carries the meaning.
                break;
        }

        return $payload;
    }

    /**
     * Park fee inputs only matter when the "this activity has a park fee"
     * checkbox is ticked. When it isn't, force both fields to null so
     * unchecking the box actually removes a previously saved fee.
     */
    private function parkFeePayload(ActivityRequest $request): array
    {
        // Label is always wiped — display uses the fixed "Park fee" wording
        // from the model. Only the amount matters.
        if (! $request->boolean('has_park_fee')) {
            return ['park_fee' => null, 'park_fee_label' => null];
        }

        return [
            'park_fee'       => $request->input('park_fee'),
            'park_fee_label' => null,
        ];
    }

    /** Hard cap on photos per activity. Matches ActivityRequest::rules(). */
    private const MAX_IMAGES = 3;

    private function syncImages(Activity $activity, ActivityRequest $request): void
    {
        if (! $request->hasFile('images')) {
            return;
        }

        // Count existing images that survived this request's delete_media list
        // so admins can't sneak past the cap by editing.
        $existing = $activity->media()->count();
        $incoming = count($request->file('images'));
        $deleting = is_array($request->input('delete_media')) ? count($request->input('delete_media')) : 0;
        $total    = $existing - $deleting + $incoming;

        if ($total > self::MAX_IMAGES) {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'images' => "An activity can have at most ".self::MAX_IMAGES." photos. "
                          . "This activity already has {$existing}; remove some before adding more.",
            ]);
        }

        $nextSort = (int) ($activity->media()->max('sort_order') ?? -1) + 1;

        foreach ($request->file('images') as $file) {
            $path = $this->imageService->upload($file, 'activities');
            $activity->media()->create([
                'path' => $path,
                'sort_order' => $nextSort++,
            ]);
        }
    }

    private function uniqueSlug(int $destinationId, string $name, ?int $ignoreId = null): string
    {
        $base = Str::slug($name);
        $slug = $base;
        $suffix = 2;
        while (Activity::where('destination_id', $destinationId)
            ->where('slug', $slug)
            ->when($ignoreId, fn ($q) => $q->where('id', '!=', $ignoreId))
            ->exists()) {
            $slug = "{$base}-{$suffix}";
            $suffix++;
        }

        return $slug;
    }
}
