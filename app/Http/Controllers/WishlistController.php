<?php

namespace App\Http\Controllers;

use App\Accommodation;
use App\Activity;
use App\Package;
use App\Services\SessionWishlist;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class WishlistController extends Controller
{
    public function __construct(private SessionWishlist $wishlist) {}

    public function index()
    {
        $body = $this->wishlist->whatsappMessage();
        $mailto = 'mailto:info@goldenpangolin.com'
            .'?subject='.rawurlencode('My Golden Pangolin wishlist')
            .'&body='.rawurlencode($body);

        return view('wishlist.index', [
            'packages' => $this->wishlist->packages(),
            'activities' => $this->wishlist->activities(),
            'accommodations' => $this->wishlist->accommodations(),
            'whatsappUrl' => $this->wishlist->whatsappUrl(),
            'mailtoUrl' => $mailto,
        ]);
    }

    public function togglePackage(Request $request, Package $package): RedirectResponse|JsonResponse
    {
        $added = $this->wishlist->toggle(SessionWishlist::TYPE_PACKAGE, $package->id);

        return $this->respondToggle($request, $added);
    }

    public function destroyPackage(Request $request, Package $package): RedirectResponse|JsonResponse
    {
        $this->wishlist->remove(SessionWishlist::TYPE_PACKAGE, $package->id);

        return $this->respondToggle($request, false);
    }

    public function toggleActivity(Request $request, Activity $activity): RedirectResponse|JsonResponse
    {
        $added = $this->wishlist->toggle(SessionWishlist::TYPE_ACTIVITY, $activity->id);

        return $this->respondToggle($request, $added);
    }

    public function destroyActivity(Request $request, Activity $activity): RedirectResponse|JsonResponse
    {
        $this->wishlist->remove(SessionWishlist::TYPE_ACTIVITY, $activity->id);

        return $this->respondToggle($request, false);
    }

    public function toggleAccommodation(Request $request, Accommodation $accommodation): RedirectResponse|JsonResponse
    {
        $added = $this->wishlist->toggle(SessionWishlist::TYPE_ACCOMMODATION, $accommodation->id);

        return $this->respondToggle($request, $added);
    }

    public function destroyAccommodation(Request $request, Accommodation $accommodation): RedirectResponse|JsonResponse
    {
        $this->wishlist->remove(SessionWishlist::TYPE_ACCOMMODATION, $accommodation->id);

        return $this->respondToggle($request, false);
    }

    public function clear(Request $request): RedirectResponse|JsonResponse
    {
        $this->wishlist->clear();

        if ($request->wantsJson()) {
            return response()->json(['count' => 0, 'message' => 'Wishlist cleared']);
        }

        session()->flash('success', 'Wishlist cleared');

        return redirect()->back();
    }

    public function whatsapp()
    {
        return redirect()->away($this->wishlist->whatsappUrl());
    }

    /**
     * When the request expects JSON (ajax from .gp-wish-form), reply with the
     * new state. Otherwise fall back to the classic flash-and-redirect that
     * works with JS disabled.
     */
    private function respondToggle(Request $request, bool $added): RedirectResponse|JsonResponse
    {
        if ($request->wantsJson()) {
            return response()->json([
                'added' => $added,
                'count' => $this->wishlist->count(),
                'message' => $added ? 'Added to wishlist' : 'Removed from wishlist',
            ]);
        }

        session()->flash('success', $added ? 'Added to wishlist' : 'Removed from wishlist');

        return redirect()->back();
    }
}
