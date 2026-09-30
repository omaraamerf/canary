<?php

namespace App\Services;

use App\Enums\MediaProvider;
use App\Enums\MediaType;
use App\Exceptions\CloudinaryUploadException;
use App\Models\BirdMedia;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\RequestException;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class CloudinaryMediaService
{
    /**
     * @param  array<int, UploadedFile>  $images
     * @param  array<int, UploadedFile>  $videos
     * @return array<int, array{type: string, url: string, provider: string, public_id: string}>
     */
    public function upload(array $images, array $videos): array
    {
        $uploaded = [];

        try {
            foreach ($images as $image) {
                $uploaded[] = $this->uploadFile($image, MediaType::Image);
            }

            foreach ($videos as $video) {
                $uploaded[] = $this->uploadFile($video, MediaType::Video);
            }
        } catch (CloudinaryUploadException $exception) {
            $this->deleteUploaded($uploaded);

            throw $exception;
        }

        return $uploaded;
    }

    /**
     * @param  iterable<int, BirdMedia|array{type: string, provider?: string|null, public_id?: string|null}>  $media
     */
    public function deleteUploaded(iterable $media): void
    {
        foreach ($media as $item) {
            $provider = $item instanceof BirdMedia ? $item->provider : ($item['provider'] ?? null);
            $publicId = $item instanceof BirdMedia ? $item->public_id : ($item['public_id'] ?? null);
            $type = $item instanceof BirdMedia ? $item->type : $item['type'];

            if ($provider !== MediaProvider::Cloudinary->value || ! $publicId) {
                continue;
            }

            try {
                $this->destroy($publicId, MediaType::from($type));
            } catch (CloudinaryUploadException $exception) {
                Log::warning('Cloudinary asset deletion failed.', [
                    'public_id' => $publicId,
                    'resource_type' => $type,
                    'message' => $exception->getMessage(),
                ]);
            }
        }
    }

    /**
     * @return array{type: string, url: string, provider: string, public_id: string}
     */
    private function uploadFile(UploadedFile $file, MediaType $type): array
    {
        $this->assertConfigured($type === MediaType::Image ? 'images' : 'videos');

        $timestamp = time();
        $parameters = [
            'folder' => config('services.cloudinary.folder'),
            'timestamp' => $timestamp,
        ];
        $stream = fopen($file->getRealPath(), 'r');

        try {
            $response = Http::asMultipart()
                ->timeout((int) config('services.cloudinary.upload_timeout', 120))
                ->attach('file', $stream, $file->getClientOriginalName())
                ->post($this->endpoint($type, 'upload'), [
                    ...$parameters,
                    'api_key' => config('services.cloudinary.api_key'),
                    'signature' => $this->signature($parameters),
                ])
                ->throw();
        } catch (ConnectionException|RequestException $exception) {
            throw new CloudinaryUploadException(
                $type === MediaType::Image ? 'images' : 'videos',
                __('تعذر رفع الملف إلى Cloudinary. حاول مرة أخرى.'),
            );
        } finally {
            if (is_resource($stream)) {
                fclose($stream);
            }
        }

        $url = $response->json('secure_url');
        $publicId = $response->json('public_id');

        if (! is_string($url) || ! is_string($publicId)) {
            throw new CloudinaryUploadException(
                $type === MediaType::Image ? 'images' : 'videos',
                __('لم يرجع Cloudinary بيانات الملف المتوقعة.'),
            );
        }

        return [
            'type' => $type->value,
            'url' => $url,
            'provider' => MediaProvider::Cloudinary->value,
            'public_id' => $publicId,
        ];
    }

    private function destroy(string $publicId, MediaType $type): void
    {
        $this->assertConfigured('media');

        $parameters = [
            'invalidate' => 'true',
            'public_id' => $publicId,
            'timestamp' => time(),
        ];

        try {
            Http::asForm()
                ->timeout(30)
                ->post($this->endpoint($type, 'destroy'), [
                    ...$parameters,
                    'api_key' => config('services.cloudinary.api_key'),
                    'signature' => $this->signature($parameters),
                ])
                ->throw();
        } catch (ConnectionException|RequestException $exception) {
            throw new CloudinaryUploadException('media', __('تعذر حذف الملف من Cloudinary.'));
        }
    }

    private function endpoint(MediaType $type, string $action): string
    {
        return sprintf(
            'https://api.cloudinary.com/v1_1/%s/%s/%s',
            config('services.cloudinary.cloud_name'),
            $type->value,
            $action,
        );
    }

    private function signature(array $parameters): string
    {
        ksort($parameters);
        $payload = collect($parameters)
            ->map(fn ($value, $key) => $key.'='.$value)
            ->implode('&');

        return sha1($payload.config('services.cloudinary.api_secret'));
    }

    private function assertConfigured(string $field): void
    {
        if (! config('services.cloudinary.cloud_name') || ! config('services.cloudinary.api_key') || ! config('services.cloudinary.api_secret')) {
            throw new CloudinaryUploadException($field, __('إعدادات Cloudinary غير مكتملة على الخادم.'));
        }
    }
}
