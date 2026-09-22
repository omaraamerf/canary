<?php

namespace App\Http\Requests\Admin;

use App\Enums\BirdStatus;
use App\Http\Requests\BirdRequest;
use Illuminate\Validation\Rule;

class SaveBirdRequest extends BirdRequest
{
    public function rules(): array
    {
        return [
            ...$this->commonRules(),
            'currency' => ['required', 'string', 'size:3'],
            'region_id' => ['required', 'exists:regions,id'],
            'status' => ['required', Rule::enum(BirdStatus::class)],
            'featured' => ['nullable', 'boolean'],
        ];
    }

    protected function prepareForValidation(): void
    {
        parent::prepareForValidation();
        $this->merge(['featured' => $this->boolean('featured')]);
    }
}
