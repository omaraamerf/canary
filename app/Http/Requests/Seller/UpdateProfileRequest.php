<?php

namespace App\Http\Requests\Seller;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:100'],
            'display_name' => ['required', 'string', 'max:140'],
            'phone' => ['required', 'string', 'max:30'],
            'email' => ['required', 'email', 'max:190', Rule::unique('users', 'email')->ignore($this->user())],
            'region_id' => ['required', 'exists:regions,id'],
            'bio' => ['nullable', 'string', 'max:1200'],
        ];
    }
}
