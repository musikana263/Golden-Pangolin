<?php

namespace App\Http\Requests\Accommodation;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AccommodationTypeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        $typeId = $this->route('accommodation_type')?->id;

        return [
            'name' => [
                'required', 'string', 'max:120',
                Rule::unique('accommodation_types', 'name')->ignore($typeId),
            ],
            'icon' => 'nullable|string|max:100',
            'description' => 'nullable|string|max:255',
        ];
    }
}
