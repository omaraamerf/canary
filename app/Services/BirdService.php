<?php

namespace App\Services;

use App\Enums\ApprovalStatus;
use App\Enums\BirdStatus;
use App\Enums\MediaType;
use App\Enums\SettingKey;
use App\Exceptions\CloudinaryUploadException;
use App\Models\Bird;
use App\Models\Setting;
use App\Models\User;
use App\Support\UniqueSlug;
use Illuminate\Support\Arr;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Throwable;

class BirdService
{
    public function __construct(
        private readonly UniqueSlug $slugs,
        private readonly CloudinaryMediaService $cloudinary,
    ) {}

    public function createForAdmin(array $data, User $admin): Bird
    {
        $uploaded = $this->uploadMedia($data);

        try {
            return DB::transaction(function () use ($data, $admin, $uploaded) {
                $bird = Bird::create([
                    ...$this->attributes($data),
                    'seller_id' => $admin->id,
                    'approval_status' => ApprovalStatus::Approved->value,
                    'published_at' => now(),
                    'slug' => $this->slug($data['title']),
                ]);

                $this->persistMedia($bird, $uploaded);

                return $bird;
            });
        } catch (Throwable $exception) {
            $this->cloudinary->deleteUploaded($uploaded);

            throw $exception;
        }
    }

    public function updateForAdmin(Bird $bird, array $data): Bird
    {
        $approval = ApprovalStatus::from($data['approval_status']);

        return $this->updateBird($bird, $data, [
            'slug' => $bird->title === $data['title'] ? $bird->slug : $this->slug($data['title'], $bird->id),
            'rejection_reason' => $approval === ApprovalStatus::Rejected ? ($data['rejection_reason'] ?? null) : null,
            'published_at' => $approval === ApprovalStatus::Approved ? ($bird->published_at ?: now()) : null,
        ]);
    }

    public function createForSeller(array $data, User $seller): Bird
    {
        $profile = $seller->sellerProfile()->firstOrFail();
        $approval = $this->listingApprovalStatus();
        $uploaded = $this->uploadMedia($data);

        try {
            return DB::transaction(function () use ($data, $seller, $profile, $approval, $uploaded) {
                $bird = Bird::create([
                    ...$this->attributes($data),
                    'seller_id' => $seller->id,
                    'region_id' => $profile->region_id,
                    'status' => BirdStatus::Available->value,
                    'featured' => false,
                    'approval_status' => $approval->value,
                    'published_at' => $approval === ApprovalStatus::Approved ? now() : null,
                    'slug' => $this->slug($data['title']),
                ]);

                $this->persistMedia($bird, $uploaded);

                return $bird;
            });
        } catch (Throwable $exception) {
            $this->cloudinary->deleteUploaded($uploaded);

            throw $exception;
        }
    }

    public function updateForSeller(Bird $bird, array $data): Bird
    {
        $approval = $this->listingApprovalStatus();

        return $this->updateBird($bird, $data, [
            'approval_status' => $approval->value,
            'rejection_reason' => null,
            'published_at' => $approval === ApprovalStatus::Approved ? ($bird->published_at ?: now()) : null,
            'slug' => $bird->title === $data['title'] ? $bird->slug : $this->slug($data['title'], $bird->id),
        ]);
    }

    public function updateApproval(Bird $bird, ApprovalStatus $status, ?string $reason): void
    {
        $bird->update([
            'approval_status' => $status->value,
            'rejection_reason' => $status === ApprovalStatus::Rejected ? $reason : null,
            'published_at' => $status === ApprovalStatus::Approved ? ($bird->published_at ?: now()) : null,
        ]);
    }

    public function delete(Bird $bird): bool
    {
        return DB::transaction(fn (): bool => (bool) $bird->delete());
    }

    public function restore(Bird $bird): bool
    {
        return DB::transaction(fn (): bool => (bool) $bird->restore());
    }

    public function forceDelete(Bird $bird): bool
    {
        return DB::transaction(fn (): bool => (bool) $bird->forceDelete());
    }

    private function listingApprovalStatus(): ApprovalStatus
    {
        return Setting::boolean(SettingKey::ListingApprovalRequired->value, true)
            ? ApprovalStatus::Pending
            : ApprovalStatus::Approved;
    }

    private function updateBird(Bird $bird, array $data, array $extraAttributes): Bird
    {
        $removed = $this->mediaToRemove($bird, $data['remove_media'] ?? []);
        $this->assertBirdKeepsAnImage($bird, $removed, $data['images'] ?? []);
        $this->assertMediaLimits($bird, $removed, $data);
        $uploaded = $this->uploadMedia($data);

        try {
            $updatedBird = DB::transaction(function () use ($bird, $data, $extraAttributes, $removed, $uploaded) {
                $bird->update([
                    ...$this->attributes($data),
                    ...$extraAttributes,
                ]);

                $bird->media()->whereKey($removed->pluck('id')->all())->delete();
                $this->persistMedia($bird, $uploaded);

                return $bird;
            });
        } catch (Throwable $exception) {
            $this->cloudinary->deleteUploaded($uploaded);

            throw $exception;
        }

        $this->cloudinary->deleteUploaded($removed);

        return $updatedBird;
    }

    private function uploadMedia(array $data): array
    {
        try {
            return $this->cloudinary->upload($data['images'] ?? [], $data['videos'] ?? []);
        } catch (CloudinaryUploadException $exception) {
            throw ValidationException::withMessages([$exception->field => $exception->getMessage()]);
        }
    }

    private function persistMedia(Bird $bird, array $uploaded): void
    {
        $position = ((int) $bird->media()->max('sort_order')) + 1;

        foreach ($uploaded as $media) {
            $bird->media()->create([...$media, 'sort_order' => $position++]);
        }
    }

    private function mediaToRemove(Bird $bird, array $ids): Collection
    {
        return $ids === []
            ? $bird->media()->getRelated()->newCollection()
            : $bird->media()->whereKey($ids)->get();
    }

    private function assertBirdKeepsAnImage(Bird $bird, Collection $removed, array $newImages): void
    {
        $removedImageIds = $removed
            ->where('type', MediaType::Image->value)
            ->pluck('id')
            ->all();
        $remainingImages = $bird->media()
            ->where('type', MediaType::Image->value)
            ->when($removedImageIds !== [], fn ($query) => $query->whereKeyNot($removedImageIds))
            ->count();

        if ($remainingImages + count($newImages) === 0) {
            throw ValidationException::withMessages(['images' => __('يجب أن يبقى للإعلان صورة واحدة على الأقل.')]);
        }
    }

    private function assertMediaLimits(Bird $bird, Collection $removed, array $data): void
    {
        foreach ([
            MediaType::Image->value => ['input' => 'images', 'limit' => (int) config('services.cloudinary.max_images', 8)],
            MediaType::Video->value => ['input' => 'videos', 'limit' => (int) config('services.cloudinary.max_videos', 3)],
        ] as $type => $settings) {
            $removedIds = $removed
                ->where('type', $type)
                ->pluck('id')
                ->all();
            $existing = $bird->media()
                ->where('type', $type)
                ->when($removedIds !== [], fn ($query) => $query->whereKeyNot($removedIds))
                ->count();

            if ($existing + count($data[$settings['input']] ?? []) > $settings['limit']) {
                throw ValidationException::withMessages([
                    $settings['input'] => __('تجاوز عدد الملفات الحد المسموح لهذا الإعلان.'),
                ]);
            }
        }
    }

    private function attributes(array $data): array
    {
        return Arr::except($data, ['images', 'videos', 'remove_media']);
    }

    private function slug(string $title, ?int $exceptId = null): string
    {
        return $this->slugs->for(Bird::class, $title, 'bird', $exceptId, true);
    }
}
