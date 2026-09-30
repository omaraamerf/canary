<?php

namespace App\Http\Requests\Concerns;

trait ValidatesCommunityMedia
{
    protected function mediaRules(int $maxImages, int $maxVideos): array
    {
        return [
            'images' => ['nullable', 'array', 'max:'.$maxImages],
            'images.*' => [
                'file',
                'mimes:jpg,jpeg,jfif,png,webp,gif,bmp,avif,heic,heif',
                'max:'.config('services.cloudinary.image_max_kb', 10240),
            ],
            'videos' => ['nullable', 'array', 'max:'.$maxVideos],
            'videos.*' => ['file', 'mimes:mp4,mov,webm,m4v', 'max:'.config('services.cloudinary.video_max_kb', 51200)],
        ];
    }

    public function messages(): array
    {
        return [
            'images.max' => __('ui.community.too_many_images'),
            'images.*.mimes' => __('صيغة إحدى الصور غير مدعومة. استخدم JPG أو PNG أو WebP أو GIF أو AVIF أو HEIC.'),
            'images.*.max' => __('حجم إحدى الصور أكبر من الحد المسموح.'),
            'videos.max' => __('ui.community.too_many_videos'),
            'videos.*.mimes' => __('صيغة أحد الفيديوهات غير مدعومة. استخدم MP4 أو MOV أو WebM.'),
            'videos.*.max' => __('حجم أحد الفيديوهات أكبر من الحد المسموح.'),
        ];
    }
}
