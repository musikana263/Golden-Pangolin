<?php

namespace App\Http\Requests\Accommodation;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AccommodationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        $allowedAmenities = array_keys(config('site.amenities', []));

        return [
            'name' => 'required|string|max:200',
            'accommodation_type_id' => 'nullable|exists:accommodation_types,id',
            'country' => ['required', Rule::in(config('site.countries', ['Zimbabwe', 'Botswana', 'Zambia']))],
            'location' => 'nullable|string|max:200',
            'description' => 'required|string|max:500',
            'price_from' => 'nullable|string|max:100',
            'amenities' => 'nullable|array',
            'amenities.*' => ['string', Rule::in($allowedAmenities)],
            'is_active' => 'nullable|boolean',
            // Hard cap of 6 photos per accommodation. Controller adds a second
            // check for (existing + new) so edits can't smuggle past it.
            'images' => 'nullable|array|max:6',
            'images.*' => 'image|mimes:jpg,jpeg,png,webp|max:5120',
            'delete_media' => 'nullable|array',
            'delete_media.*' => 'integer|exists:media,id',
        ];
    }

    public function amenitiesArray(): array
    {
        return $this->input('amenities', []) ?: [];
    }
}
