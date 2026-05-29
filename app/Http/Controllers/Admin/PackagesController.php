<?php

namespace App\Http\Controllers\Admin;

use App\Accommodation;
use App\Destinations;
use App\Activity;
use App\Http\Controllers\Controller;
use App\Http\Requests\Package\PackageRequest;
use App\Media;
use App\Package;
use App\Services\ImageService;
use Illuminate\Support\Str;

class PackagesController extends Controller
{
    public function __construct(private ImageService $imageService) {}

    public function index()
    {
        return view('admin.packages.index')->with(
            'packages',
            Package::with('activities.destination', 'accommodation', 'media', 'destinations')
                ->orderBy('sort_order')->orderByDesc('id')->paginate(15)
        );
    }

    public function create()
    {
        return view('admin.packages.create')
            ->with('destinations', Destinations::orderBy('title')->get())
            ->with('activities', Activity::with('destination')->orderBy('destination_id')->orderBy('name')->get())
            ->with('accommodations', Accommodation::active()->orderBy('name')->get());
    }

    public function store(PackageRequest $request)
    {
        $package = Package::create($this->payload($request));

        // ✅ Sync destinations
        if ($request->filled('destinations')) {
            $package->destinations()->sync($request->input('destinations'));
        }

        // ✅ Sync activities
        if ($request->filled('activities')) {
            $package->activities()->sync($this->syncedActivities($request->input('activities')));
        }

        $this->syncCoverImage($package, $request);
        $this->syncHeroFlag($package, $request);

        session()->flash('success', 'Package created.');
        return redirect(route('tour-packages.edit', $package));
    }

    public function edit(Package $package)
    {
        return view('admin.packages.create')
            ->with('package', $package->load('activities.destination', 'media', 'destinations'))
            ->with('destinations', Destinations::orderBy('title')->get())
            ->with('activities', Activity::with('destination')->orderBy('destination_id')->orderBy('name')->get())
            ->with('accommodations', Accommodation::active()->orderBy('name')->get());
    }

    public function update(PackageRequest $request, Package $package)
    {
        $data = $this->payload($request);

        if ($package->name !== $request->name) {
            $data['slug'] = $this->uniqueSlug($request->name, $package->id);
        }

        $package->update($data);

        // ✅ Sync destinations
        $package->destinations()->sync(
            $request->filled('destinations') ? $request->input('destinations') : []
        );

        // ✅ Sync activities
        $package->activities()->sync(
            $request->filled('activities') ? $this->syncedActivities($request->input('activities')) : []
        );

        if ($request->filled('delete_media')) {
            $toDelete = Media::whereIn('id', $request->input('delete_media'))
                ->where('mediable_type', Package::class)
                ->where('mediable_id', $package->id)
                ->get();
            foreach ($toDelete as $m) {
                $m->deleteFile();
                $m->delete();
            }
        }

        $this->syncCoverImage($package, $request);
        $this->syncHeroFlag($package, $request);

        session()->flash('success', 'Package updated.');
        return redirect(route('tour-packages.edit', $package));
    }

    /**
     * Enforce a single hero package. Calls Package::promoteToHero() (which
     * clears the flag on every other package in a transaction) when the
     * checkbox is on, otherwise leaves things alone — admins demote a
     * package by promoting a different one.
     */
    private function syncHeroFlag(Package $package, PackageRequest $request): void
    {
        if ($request->boolean('is_hero')) {
            $package->promoteToHero();
        } elseif ($package->is_hero && $request->has('is_hero')) {
            // Checkbox explicitly unticked on a previously-hero package.
            $package->forceFill(['is_hero' => false])->save();
        }
    }

    public function destroy(Package $package)
    {
        if ($package->trashed()) {
            foreach ($package->media as $m) {
                $m->deleteFile();
                $m->delete();
            }
            $package->forceDelete();
        } else {
            $package->delete();
        }

        session()->flash('success', 'Package deleted.');
        return redirect(route('tour-packages.index'));
    }

    private function payload(PackageRequest $request): array
    {
        // Tiers: keep only the canonical slugs to defend against form tampering.
        $tiers = array_values(array_intersect(
            \App\Package::ALL_TIERS,
            (array) $request->input('tiers', [])
        ));
        // All-three is equivalent to "no restriction" — store NULL for that
        // case so the public scopes stay simple and legacy rows behave the same.
        if (count($tiers) === count(\App\Package::ALL_TIERS) || empty($tiers)) {
            $tiers = null;
        }

        return [
            'name' => $request->name,
            'slug' => $request->route('package') ? $request->route('package')->slug : $this->uniqueSlug($request->name),
            'summary' => $request->summary,
            'duration' => $request->duration,
            'group_size' => $request->group_size,
            'price_from' => $request->price_from,
            'accommodation_id' => $request->accommodation_id,
            'is_active' => $request->boolean('is_active'),
            'is_featured' => $request->boolean('is_featured'),
            'tiers' => $tiers,
        ];
    }

    private function syncedActivities(array $ids): array
    {
        $sync = [];
        foreach ($ids as $i => $id) {
            $sync[$id] = ['sort_order' => $i];
        }
        return $sync;
    }

    private function syncCoverImage(Package $package, PackageRequest $request): void
    {
        if (! $request->hasFile('image')) {
            return;
        }

        $path = $this->imageService->upload($request->file('image'), 'packages');
        $nextSort = (int) ($package->media()->max('sort_order') ?? -1) + 1;

        $package->media()->create([
            'path' => $path,
            'sort_order' => $nextSort,
        ]);
    }

    private function uniqueSlug(string $name, ?int $ignoreId = null): string
    {
        $base = Str::slug($name);
        $slug = $base;
        $suffix = 2;
        while (Package::where('slug', $slug)
            ->when($ignoreId, fn ($q) => $q->where('id', '!=', $ignoreId))
            ->exists()) {
            $slug = "{$base}-{$suffix}";
            $suffix++;
        }
        return $slug;
    }
}
