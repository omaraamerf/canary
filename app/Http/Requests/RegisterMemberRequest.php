<?php

namespace App\Http\Requests;

use App\Http\Requests\Concerns\ValidatesLocation;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class RegisterMemberRequest extends FormRequest
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
            'email' => ['required', 'email', 'max:190', 'unique:users,email'],
            'phone' => ['nullable', 'string', 'max:30'],
            ...$this->locationRules(),
            'password' => ['required', 'confirmed', Password::min(8)],
        ];
    }
}
