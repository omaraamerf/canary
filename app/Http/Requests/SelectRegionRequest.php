<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SelectRegionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'country_id' => ['nullable', 'integer', Rule::exists('countries', 'id')->where('active', true)],
            'region_id' => ['nullable', 'integer', Rule::exists('regions', 'id')->where('active', true)],
        ];
    }

    protected function prepareForValidation(): void
    {
        // scope=all (or an empty choice) means every country and region.
        if ($this->input('scope') === 'all') {
            $this->merge(['country_id' => null, 'region_id' => null]);
        }
    }
}
