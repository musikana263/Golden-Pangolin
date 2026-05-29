<?php

namespace App\Http\Requests\Users;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class StoreUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() && $this->user()->isAdmin();
    }

    public function rules(): array
    {
        return [
            'name'     => ['required', 'string', 'max:120'],
            'email'    => ['required', 'email:filter', 'max:180', 'unique:users,email'],
            'password' => ['required', Password::min(8)->mixedCase()->numbers()],
            'is_admin' => ['nullable', 'boolean'],
        ];
    }
}
