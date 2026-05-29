<?php

namespace App\Http\Requests\Package;

use Illuminate\Foundation\Http\FormRequest;

class PackageRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Only allow admins to create/update packages
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:200',
            'summary' => 'required|string|max:500',
            'duration' => 'nullable|string|max:100',
            'group_size' => 'nullable|string|max:100',
            'price_from' => 'nullable|string|max:100',
            'accommodation_id' => 'nullable|exists:accommodations,id',

            // ✅ Destinations are now required instead of country
            'destinations' => ['required', 'array', 'min:1'],
            'destinations.*' => ['integer', 'exists:destinations,id'],

            // ✅ Activities optional
            'activities' => 'nullable|array',
            'activities.*' => 'integer|exists:activities,id',

            'is_active' => 'nullable|boolean',
            'is_featured' => 'nullable|boolean',
            'is_hero' => 'nullable|boolean',

            // Traveller-tier availability. Each entry must be a known slug.
            // Empty array (no boxes ticked) is treated as "available to all"
            // by the controller payload helper.
            'tiers' => 'nullable|array',
            'tiers.*' => 'in:local,african,international',

            // ✅ Image optional
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',

            // ✅ Media deletion optional
            'delete_media' => 'nullable|array',
            'delete_media.*' => 'integer|exists:media,id',
        ];
    }
}
