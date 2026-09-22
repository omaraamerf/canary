<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'buyer_name' => ['required', 'string', 'max:100'],
            'phone' => ['required', 'string', 'max:30'],
            'buyer_region_id' => ['required', 'exists:regions,id'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ];
    }
}
