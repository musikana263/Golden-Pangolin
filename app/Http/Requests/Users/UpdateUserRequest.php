<?php

namespace App\Http\Requests\Users;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

/**
 * Admin editing any user record. Password is optional — only updated when
 * the admin actually types a new one. is_admin is folded in here too so the
 * separate "make admin" pill in the list view isn't needed any more.
 */
class UpdateUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() && $this->user()->isAdmin();
    }

    public function rules(): array
    {
        $userId = $this->route('user')?->id;

        return [
            'name'     => ['required', 'string', 'max:120'],
            'email'    => ['required', 'email:filter', 'max:180', Rule::unique('users', 'email')->ignore($userId)],
            'password' => ['nullable', Password::min(8)->mixedCase()->numbers()],
            'is_admin' => ['nullable', 'boolean'],
        ];
    }
}
