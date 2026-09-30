<?php

namespace App\Services;

use App\Enums\MediaType;
use App\Exceptions\CloudinaryUploadException;
use App\Models\User;
use App\Support\LocationOptions;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Arr;
use Illuminate\Validation\ValidationException;

class AccountService
{
    public function __construct(
        private readonly CloudinaryMediaService $cloudinary,
        private readonly LocationOptions $locations,
    ) {}

    public function updateProfile(User $user, array $data): User
    {
        $oldAvatar = $this->avatarAsset($user);
        // A disabled region select is not submitted, so missing optional fields mean "cleared".
        $attributes = $this->locations->normalize([
            'phone' => null, 'bio' => null, 'country_id' => null, 'region_id' => null,
            ...Arr::only($data, ['name', 'email', 'phone', 'bio', 'country_id', 'region_id']),
        ]);

        if (($data['avatar'] ?? null) instanceof UploadedFile) {
            $uploaded = $this->uploadAvatar($data['avatar']);
            $attributes['avatar_url'] = $uploaded['url'];
            $attributes['avatar_public_id'] = $uploaded['public_id'];
        } elseif (! empty($data['remove_avatar'])) {
            $attributes['avatar_url'] = null;
            $attributes['avatar_public_id'] = null;
        }

        try {
            $user->update($attributes);
        } catch (\Throwable $exception) {
            if (isset($uploaded)) {
                $this->cloudinary->deleteUploaded([$uploaded]);
            }

            throw $exception;
        }

        if ($oldAvatar && array_key_exists('avatar_url', $attributes)) {
            $this->cloudinary->deleteUploaded([$oldAvatar]);
        }

        return $user->refresh();
    }

    public function updatePassword(User $user, string $password): void
    {
        $user->update(['password' => $password]);
    }

    private function uploadAvatar(UploadedFile $file): array
    {
        try {
            return $this->cloudinary->upload([$file], [])[0];
        } catch (CloudinaryUploadException $exception) {
            throw ValidationException::withMessages(['avatar' => $exception->getMessage()]);
        }
    }

    private function avatarAsset(User $user): ?array
    {
        return $user->avatar_public_id
            ? ['type' => MediaType::Image->value, 'provider' => 'cloudinary', 'public_id' => $user->avatar_public_id]
            : null;
    }
}
