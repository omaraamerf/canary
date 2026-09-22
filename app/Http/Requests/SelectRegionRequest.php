<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SelectRegionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return ['region' => ['required', 'string']];
    }
}
