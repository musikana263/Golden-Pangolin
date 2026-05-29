<?php

namespace App\Http\Controllers;

use App\Destinations;
use App\Http\Requests\Destinations\CreateDestinationsRequest;
use App\Http\Requests\Destinations\UpdateDestinationsRequest;
use App\Services\ImageService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class DestinationsController extends Controller
{
    public function __construct(private ImageService $imageService) {}

    public function index()
    {
        // Eager-load activity count + use the admin-controlled sort_order so
        // this list matches what the public site shows. Drag-and-drop on the
        // table writes to that column via /admin/sort/destinations.
        $destinations = Destinations::withCount('activities')
            ->orderBy('sort_order')
            ->orderByDesc('id')
            ->get();

        return view('destinations.index')->with('destinations', $destinations);
    }

    public function create()
    {
        return view('destinations.create');
    }

    public function store(CreateDestinationsRequest $request)
    {
        // Image uploads on shared hosting can be slow. Give the worker some
        // headroom so we don't time out mid-write (which was producing
        // ERR_HTTP2_PROTOCOL_ERROR — Apache cuts the connection while PHP
        // is still saving the file).
        @ini_set('memory_limit', '256M');
        @set_time_limit(120);

        // Defensive: make sure the target storage folder exists. On a fresh
        // install storage/app/public is empty and the `destinations/` subdir
        // doesn't exist until something writes to it.
        if (! Storage::disk('public')->exists('destinations')) {
            Storage::disk('public')->makeDirectory('destinations');
        }

        try {
            $image = $this->imageService->upload($request->image, 'destinations');
        } catch (\Throwable $e) {
            Log::error('Destination image upload failed', [
                'error' => $e->getMessage(),
                'file'  => $request->image?->getClientOriginalName(),
            ]);
            return back()->withInput()->withErrors([
                'image' => 'Could not save the uploaded image. Try a smaller file or a different format.',
            ]);
        }

        Destinations::create([
            'title'        => $request->title,
            'description'  => $request->description,
            // `content` is no longer collected from the admin form. The
            // column stays in the DB for backwards compatibility, so we
            // simply don't write to it from create/update anymore.
            'image'        => $image,
            'published_at' => $request->published_at ?: now(),
            'country'      => $request->country ?: 'Zimbabwe',
        ]);

        session()->flash('success', 'Destination created.');

        return redirect(route('destinations.index'));
    }

    public function edit(Destinations $destination)
    {
        return view('destinations.create')->with('destinations', $destination);
    }

    public function update(UpdateDestinationsRequest $request, Destinations $destination)
    {
        @ini_set('memory_limit', '256M');
        @set_time_limit(120);

        // `content` is intentionally NOT in the whitelist: the admin form no
        // longer exposes that field, so we leave any existing column value
        // untouched on update (avoids silently wiping older destinations).
        $data = $request->only(['title', 'description', 'published_at', 'country']);

        if ($request->hasFile('image')) {
            if (! Storage::disk('public')->exists('destinations')) {
                Storage::disk('public')->makeDirectory('destinations');
            }
            try {
                $newImage = $this->imageService->upload($request->image, 'destinations');
            } catch (\Throwable $e) {
                Log::error('Destination image upload failed (update)', [
                    'id'    => $destination->id,
                    'error' => $e->getMessage(),
                ]);
                return back()->withInput()->withErrors([
                    'image' => 'Could not save the new image. Try a smaller file or a different format.',
                ]);
            }
            $destination->deleteImage();
            $data['image'] = $newImage;
        }

        $destination->update($data);

        session()->flash('success', 'Destination updated.');

        return redirect(route('destinations.edit', $destination));
    }

    public function destroy($id)
    {
        $destination = Destinations::withTrashed()->where('id', $id)->firstOrFail();

        if ($destination->trashed()) {
            $destination->deleteImage();
            $destination->forceDelete();
        } else {
            $destination->delete();
        }

        session()->flash('success', 'Destination deleted.');

        return redirect(route('destinations.index'));
    }

    public function trashed()
    {
        return view('destinations.index', [
            'destinations' => Destinations::onlyTrashed()->get(),
            'isTrashed' => true,
        ]);
    }

    public function restore($id)
    {
        Destinations::withTrashed()->where('id', $id)->firstOrFail()->restore();

        session()->flash('success', 'Destination restored.');

        return redirect()->back();
    }
}
