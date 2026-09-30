<?php

namespace App\Http\Requests\Seller;

use App\Http\Requests\Concerns\ValidatesLocation;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class RegisterRequest extends FormRequest
{
    use ValidatesLocation;

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
            'email' => ['required', 'email', 'max:190', 'unique:users,email'],
            ...$this->locationRules(regionRequired: true),
            'bio' => ['nullable', 'string', 'max:1200'],
            'password' => ['required', 'confirmed', Password::min(8)],
        ];
    }
}
