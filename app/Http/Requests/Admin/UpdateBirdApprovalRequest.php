<?php

namespace App\Http\Requests\Admin;

use App\Enums\ApprovalStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateBirdApprovalRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'approval_status' => ['required', Rule::enum(ApprovalStatus::class)],
            'rejection_reason' => ['nullable', 'string', 'max:1000'],
        ];
    }
}
