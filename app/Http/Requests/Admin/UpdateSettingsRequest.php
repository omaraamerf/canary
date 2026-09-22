<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSettingsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'seller_approval_required' => ['boolean'],
            'listing_approval_required' => ['boolean'],
            'guide_enabled' => ['boolean'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'seller_approval_required' => $this->boolean('seller_approval_required'),
            'listing_approval_required' => $this->boolean('listing_approval_required'),
            'guide_enabled' => $this->boolean('guide_enabled'),
        ]);
    }
}
