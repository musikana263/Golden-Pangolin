<?php

namespace App\Http\Controllers\Admin;

use App\Accommodation;
use App\Blog;
use App\Destinations;
use App\Http\Controllers\Controller;
use App\Package;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;

/**
 * Unified soft-deleted bin. Shows the five most recently trashed items per
 * model and lets admins restore or permanently destroy any of them.
 */
class TrashController extends Controller
{
    private const PER_SECTION = 5;

    /** Map URL `type` slug → model class. Anything not in here is rejected. */
    private const TYPES = [
        'destinations'   => Destinations::class,
        'packages'       => Package::class,
        'accommodations' => Accommodation::class,
        'blog'           => Blog::class,
    ];

    public function index()
    {
        $sections = [
            [
                'key'   => 'destinations',
                'label' => 'Destinations',
                'icon'  => 'fas fa-map-marker-alt',
                'items' => Destinations::onlyTrashed()->latest('deleted_at')->take(self::PER_SECTION)->get(),
            ],
            [
                'key'   => 'packages',
                'label' => 'Packages',
                'icon'  => 'fas fa-suitcase-rolling',
                'items' => Package::onlyTrashed()->with('media')->latest('deleted_at')->take(self::PER_SECTION)->get(),
            ],
            [
                'key'   => 'accommodations',
                'label' => 'Accommodation',
                'icon'  => 'fas fa-bed',
                'items' => Accommodation::onlyTrashed()->with('media')->latest('deleted_at')->take(self::PER_SECTION)->get(),
            ],
            [
                'key'   => 'blog',
                'label' => 'Blog posts',
                'icon'  => 'fas fa-newspaper',
                'items' => Blog::onlyTrashed()->latest('deleted_at')->take(self::PER_SECTION)->get(),
            ],
        ];

        return view('admin.trash.index', compact('sections'));
    }

    public function restore(string $type, int $id): RedirectResponse
    {
        $model = $this->resolveTrashed($type, $id);
        $model->restore();
        session()->flash('success', $this->labelFor($type).' restored.');

        return redirect()->route('trash.index');
    }

    public function forceDestroy(string $type, int $id): RedirectResponse
    {
        $model = $this->resolveTrashed($type, $id);

        // Models that own uploaded image files clean them up on force-delete.
        if (method_exists($model, 'deleteImage')) {
            $model->deleteImage();
        }
        if (method_exists($model, 'media')) {
            foreach ($model->media as $media) {
                if (method_exists($media, 'deleteFile')) $media->deleteFile();
                $media->delete();
            }
        }

        $model->forceDelete();
        session()->flash('success', $this->labelFor($type).' permanently removed.');

        return redirect()->route('trash.index');
    }

    private function resolveTrashed(string $type, int $id): Model
    {
        $class = self::TYPES[$type] ?? abort(404);
        return $class::onlyTrashed()->where('id', $id)->firstOrFail();
    }

    private function labelFor(string $type): string
    {
        return [
            'destinations'   => 'Destination',
            'packages'       => 'Package',
            'accommodations' => 'Accommodation',
            'blog'           => 'Blog post',
        ][$type] ?? 'Item';
    }
}
