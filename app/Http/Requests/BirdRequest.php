<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

abstract class BirdRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function commonRules(): array
    {
        return [
            'breed_id' => ['required', 'exists:breeds,id'],
            'title' => ['required', 'string', 'max:180'],
            'sex' => ['required', 'in:male,female,unknown'],
            'hatch_year' => ['nullable', 'integer', 'min:2000', 'max:'.now()->year],
            'color' => ['required', 'string', 'max:80'],
            'molt_status' => ['nullable', 'in:ready,young,molting'],
            'breeding_ready' => ['nullable', 'boolean'],
            'singing_status' => ['nullable', 'in:singing,not_singing,young,female'],
            'ring_number' => ['nullable', 'string', 'max:80'],
            'price' => ['required', 'numeric', 'min:0'],
            'city' => ['required', 'string', 'max:120'],
            'delivery_type' => ['required', 'in:pickup,delivery,agreement'],
            'description' => ['nullable', 'string', 'max:3000'],
            'images' => [$this->route('bird') ? 'nullable' : 'required', 'array', 'max:'.config('services.cloudinary.max_images', 8)],
            'images.*' => [
                'file',
                'mimes:jpg,jpeg,jfif,png,webp,gif,bmp,avif,heic,heif',
                'max:'.config('services.cloudinary.image_max_kb', 10240),
            ],
            'videos' => ['nullable', 'array', 'max:'.config('services.cloudinary.max_videos', 3)],
            'videos.*' => ['file', 'mimes:mp4,mov,webm,m4v', 'max:'.config('services.cloudinary.video_max_kb', 51200)],
            'remove_media' => ['nullable', 'array'],
            'remove_media.*' => ['integer', 'distinct', 'exists:bird_media,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'images.required' => __('أضف صورة واحدة على الأقل للطائر.'),
            'images.*.file' => __('تعذر قراءة أحد ملفات الصور المرفوعة.'),
            'images.*.mimes' => __('صيغة إحدى الصور غير مدعومة. استخدم JPG أو PNG أو WebP أو GIF أو AVIF أو HEIC.'),
            'images.*.max' => __('حجم إحدى الصور أكبر من الحد المسموح.'),
            'videos.*.mimes' => __('صيغة أحد الفيديوهات غير مدعومة. استخدم MP4 أو MOV أو WebM.'),
            'videos.*.max' => __('حجم أحد الفيديوهات أكبر من الحد المسموح.'),
        ];
    }

    protected function prepareForValidation(): void
    {
        if ($this->filled('breeding_ready')) {
            $this->merge(['breeding_ready' => $this->boolean('breeding_ready')]);
        }
    }
}
