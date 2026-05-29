<?php

namespace App\Http\Controllers;

use App\Services\TravellerTier;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class TravellerTierController extends Controller
{
    public function __construct(private TravellerTier $tier) {}

    public function update(Request $request)
    {
        $validated = $request->validate([
            'tier' => ['required', Rule::in(array_keys(TravellerTier::TIERS))],
        ]);

        $this->tier->set($validated['tier']);

        if ($request->wantsJson()) {
            return response()->json([
                'tier' => $validated['tier'],
                'label' => $this->tier->label($validated['tier']),
            ]);
        }

        return back();
    }
}
