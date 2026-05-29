<?php

namespace App\Http\Controllers\Admin;

use App\Accommodation;
use App\AccommodationType;
use App\Http\Controllers\Controller;
use App\Http\Requests\Accommodation\AccommodationRequest;
use App\Media;
use App\Services\ImageService;
use Illuminate\Support\Str;

class AccommodationsController extends Controller
{
    public function __construct(private ImageService $imageService) {}

    public function index()
    {
        return view('admin.accommodations.index')->with(
            'accommodations',
            Accommodation::with('type', 'media')->orderBy('sort_order')->orderByDesc('id')->paginate(15)
        );
    }

    public function create()
    {
        return view('admin.accommodations.create')->with('types', AccommodationType::orderBy('name')->get());
    }

    public function store(AccommodationRequest $request)
    {
        $data = $request->only([
            'accommodation_type_id',
            'name',
            'country',
            'location',
            'description',
            'price_from',
        ]);

        $data['slug'] = $this->uniqueSlug($request->name);
        $data['amenities'] = $request->amenitiesArray();
        $data['is_active'] = $request->boolean('is_active');

        $accommodation = Accommodation::create($data);

        $this->syncImages($accommodation, $request);

        session()->flash('success', 'Accommodation created.');

        return redirect(route('accommodations.edit', $accommodation));
    }

    public function edit(Accommodation $accommodation)
    {
        return view('admin.accommodations.create')
            ->with('accommodation', $accommodation->load('media'))
            ->with('types', AccommodationType::orderBy('name')->get());
    }

    public function update(AccommodationRequest $request, Accommodation $accommodation)
    {
        $data = $request->only([
            'accommodation_type_id',
            'name',
            'country',
            'location',
            'description',
            'price_from',
        ]);
        $data['amenities'] = $request->amenitiesArray();
        $data['is_active'] = $request->boolean('is_active');

        if ($accommodation->name !== $request->name) {
            $data['slug'] = $this->uniqueSlug($request->name, $accommodation->id);
        }

        $accommodation->update($data);

        if ($request->filled('delete_media')) {
            $toDelete = Media::whereIn('id', $request->input('delete_media'))
                ->where('mediable_type', Accommodation::class)
                ->where('mediable_id', $accommodation->id)
                ->get();
            foreach ($toDelete as $m) {
                $m->deleteFile();
                $m->delete();
            }
        }

        $this->syncImages($accommodation, $request);

        session()->flash('success', 'Accommodation updated.');

        return redirect(route('accommodations.edit', $accommodation));
    }

    public function destroy(Accommodation $accommodation)
    {
        if ($accommodation->trashed()) {
            foreach ($accommodation->media as $media) {
                $media->deleteFile();
                $media->delete();
            }
            $accommodation->forceDelete();
        } else {
            $accommodation->delete();
        }

        session()->flash('success', 'Accommodation deleted.');

        return redirect(route('accommodations.index'));
    }

    /** Hard cap on photos per accommodation. Matches AccommodationRequest::rules(). */
    private const MAX_IMAGES = 6;

    private function syncImages(Accommodation $accommodation, AccommodationRequest $request): void
    {
        if (! $request->hasFile('images')) {
            return;
        }

        // Count existing minus those being deleted plus new — must stay
        // under the cap. Stops admins from blowing past 6 by uploading in
        // batches on consecutive edits.
        $existing = $accommodation->media()->count();
        $incoming = count($request->file('images'));
        $deleting = is_array($request->input('delete_media')) ? count($request->input('delete_media')) : 0;
        $total    = $existing - $deleting + $incoming;

        if ($total > self::MAX_IMAGES) {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'images' => "An accommodation can have at most ".self::MAX_IMAGES." photos. "
                          . "This one already has {$existing}; remove some before adding more.",
            ]);
        }

        $nextSort = (int) ($accommodation->media()->max('sort_order') ?? -1) + 1;

        foreach ($request->file('images') as $file) {
            $path = $this->imageService->upload($file, 'accommodations');
            $accommodation->media()->create([
                'path' => $path,
                'sort_order' => $nextSort++,
            ]);
        }
    }

    private function uniqueSlug(string $name, ?int $ignoreId = null): string
    {
        $base = Str::slug($name);
        $slug = $base;
        $suffix = 2;
        while (Accommodation::where('slug', $slug)
            ->when($ignoreId, fn ($q) => $q->where('id', '!=', $ignoreId))
            ->exists()) {
            $slug = "{$base}-{$suffix}";
            $suffix++;
        }

        return $slug;
    }
}
