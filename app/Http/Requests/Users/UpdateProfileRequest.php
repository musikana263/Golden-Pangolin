<?php

namespace App\Http\Requests\Users;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Only admins can edit profile details (name / email / about).
        return $this->user() && $this->user()->isAdmin();
    }

    public function rules(): array
    {
        $userId = $this->user()?->id;

        return [
            'name'  => ['required', 'string', 'max:120'],
            'email' => ['required', 'email:filter', 'max:180', Rule::unique('users', 'email')->ignore($userId)],
            'about' => ['nullable', 'string', 'max:1000'],
        ];
    }
}
