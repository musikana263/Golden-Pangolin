<?php

namespace App\Http\Requests\Destinations;

use Illuminate\Foundation\Http\FormRequest;

class UpdateDestinationsRequest extends FormRequest
{
    public function authorize()
    {
        return $this->user() !== null;
    }

    public function rules()
    {
        return [
            'title' => 'required|string|max:200',
            'description' => 'required|string|max:500',
            'content' => 'nullable|string|max:20000',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
            'country' => 'nullable|string|max:100',
        ];
    }
}
