<?php

namespace App\Http\Requests;

use App\Http\Requests\Concerns\ValidatesLocation;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateAccountRequest extends FormRequest
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
            'email' => ['required', 'email', 'max:190', Rule::unique('users', 'email')->ignore($this->user()->id)],
            'phone' => ['nullable', 'string', 'max:30'],
            'bio' => ['nullable', 'string', 'max:600'],
            'avatar' => ['nullable', 'file', 'mimes:jpg,jpeg,jfif,png,webp,avif,heic,heif', 'max:'.config('services.cloudinary.image_max_kb', 10240)],
            'remove_avatar' => ['nullable', 'boolean'],
            ...$this->locationRules(),
        ];
    }

    public function messages(): array
    {
        return [
            'avatar.mimes' => __('صيغة الصورة غير مدعومة. استخدم JPG أو PNG أو WebP أو HEIC.'),
            'avatar.max' => __('حجم الصورة أكبر من الحد المسموح.'),
        ];
    }
}
