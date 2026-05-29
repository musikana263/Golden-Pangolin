<?php

namespace App\Http\Requests\Activity;

use App\Activity;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ActivityRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:200',
            'destination_id' => 'required|exists:destinations,id',
            'category_id' => 'nullable|exists:categories,id',
            'description' => 'required|string|max:500',
            'duration' => 'nullable|string|max:100',

            // Pricing mode determines which of the following fields matter.
            'price_mode' => ['required', Rule::in(array_keys(Activity::PRICE_MODES))],

            // Used when price_mode = 'fixed'. Required only for fixed (see
            // withValidator). Allowed to be 0 (admin can use 'fixed' + 0 if
            // they prefer that to using the dedicated 'free' mode).
            'unit_price' => 'nullable|numeric|min:0|max:999999.99',

            // Used when price_mode = 'from'. The free-text label like
            // "From $50 per person".
            'price_from' => 'nullable|string|max:100',

            // Used when price_mode = 'tiered'. Each tier independently
            // optional, but at least one must be set (withValidator). A
            // tier set to 0 means free for that group.
            'price_local' => 'nullable|numeric|min:0|max:999999.99',
            'price_african' => 'nullable|numeric|min:0|max:999999.99',
            'price_international' => 'nullable|numeric|min:0|max:999999.99',

            // Optional park / conservation fee. The display label is fixed
            // at "Park fee" — the column is kept in the DB for legacy data
            // but no longer collected from the form.
            'park_fee' => 'nullable|numeric|min:0|max:999999.99',

            'is_active' => 'nullable|boolean',
            'images' => 'nullable|array|max:3',
            'images.*' => 'image|mimes:jpg,jpeg,png,webp|max:5120',
            'delete_media' => 'nullable|array',
            'delete_media.*' => 'integer|exists:media,id',
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $v) {
            $mode = $this->input('price_mode');

            if ($mode === Activity::PRICE_MODE_FIXED) {
                if (! $this->filled('unit_price')) {
                    $v->errors()->add('unit_price', 'Set the fixed price.');
                }
            } elseif ($mode === Activity::PRICE_MODE_FROM) {
                if (! $this->filled('price_from')) {
                    $v->errors()->add('price_from', 'Enter the "from…" label shown to visitors.');
                }
            } elseif ($mode === Activity::PRICE_MODE_TIERED) {
                $hasAtLeastOne =
                    $this->filled('price_local') ||
                    $this->filled('price_african') ||
                    $this->filled('price_international');

                if (! $hasAtLeastOne) {
                    $v->errors()->add(
                        'price_international',
                        'Set at least one tier price. Leave a tier blank to hide the activity from that group; set it to 0 to mark it free for that group.'
                    );
                }
            }
            // PRICE_MODE_FREE → no extra inputs required.

            // If the park-fee box is ticked, the amount is required.
            if ($this->boolean('has_park_fee') && ! $this->filled('park_fee')) {
                $v->errors()->add('park_fee', 'Enter the park / conservation fee, or untick the box.');
            }
        });
    }

    public function messages(): array
    {
        return [
            'price_mode.required' => 'Choose a pricing mode.',
            'price_mode.in' => 'Choose a valid pricing mode.',
            'unit_price.numeric' => 'Fixed price must be a number.',
            'price_local.numeric' => 'Local price must be a number (e.g. 25 or 25.00).',
            'price_african.numeric' => 'African price must be a number.',
            'price_international.numeric' => 'International price must be a number.',
        ];
    }
}
