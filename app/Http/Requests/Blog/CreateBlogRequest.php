<?php

namespace App\Http\Requests\Blog;

use Illuminate\Foundation\Http\FormRequest;

class CreateBlogRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return $this->user() !== null;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'title' => 'required|string|max:200|unique:blogs',
            'description' => 'required|string|max:500',
            'image' => 'required|image|mimes:jpg,jpeg,png,webp|max:5120',
            'content' => 'required|string|max:20000',
            'category' => 'required|integer|exists:categories,id',
        ];
    }
}
