<?php

namespace App\Services;

use App\Enums\ApprovalStatus;
use App\Enums\SettingKey;
use App\Enums\UserRole;
use App\Enums\UserStatus;
use App\Models\SellerProfile;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Arr;

class SellerService
{
    public function register(array $data): User
    {
        $status = Setting::boolean(SettingKey::SellerApprovalRequired->value, true)
            ? UserStatus::Pending
            : UserStatus::Active;

        return DB::transaction(function () use ($data, $status) {
            $user = User::create([
                'name' => $data['name'],
                'phone' => $data['phone'],
                'email' => $data['email'],
                'password' => $data['password'],
                'role' => UserRole::Seller->value,
                'status' => $status->value,
                'region_id' => $data['region_id'],
            ]);

            SellerProfile::create([
                'user_id' => $user->id,
                'display_name' => $data['display_name'],
                'bio' => $data['bio'] ?? null,
                'region_id' => $data['region_id'],
                'approval_status' => $status === UserStatus::Active
                    ? ApprovalStatus::Approved->value
                    : ApprovalStatus::Pending->value,
            ]);

            $user->assignRole(UserRole::Seller->value);

            return $user;
        });
    }

    public function updateProfile(User $seller, array $data): void
    {
        DB::transaction(function () use ($seller, $data) {
            $seller->update([
                'name' => $data['name'],
                'phone' => $data['phone'],
                'email' => $data['email'],
                'region_id' => $data['region_id'],
            ]);

            $seller->sellerProfile()->update([
                'display_name' => $data['display_name'],
                'bio' => $data['bio'] ?? null,
                'region_id' => $data['region_id'],
            ]);
        });
    }

    public function updateDetails(User $seller, array $data): void
    {
        DB::transaction(function () use ($seller, $data) {
            $seller->update([
                'phone' => $data['phone'],
                'region_id' => $data['region_id'],
            ]);

            $seller->sellerProfile()->updateOrCreate(
                ['user_id' => $seller->id],
                [
                    'display_name' => $data['display_name'],
                    'bio' => $data['bio'] ?? null,
                    'region_id' => $data['region_id'],
                ],
            );
        });
    }

    public function updateAccount(User $seller, array $data): User
    {
        return DB::transaction(function () use ($seller, $data): User {
            $seller->update(Arr::only($data, [
                'name',
                'email',
                'password',
                'phone',
                'region_id',
            ]));

            $seller->sellerProfile()->updateOrCreate(
                ['user_id' => $seller->id],
                [
                    'display_name' => $data['display_name'],
                    'bio' => $data['bio'] ?? null,
                    'region_id' => $data['region_id'],
                ],
            );

            return $seller->refresh();
        });
    }

    public function updateStatus(User $seller, UserStatus $status, ?string $reason): void
    {
        DB::transaction(function () use ($seller, $status, $reason) {
            $seller->update(['status' => $status->value]);
            $seller->sellerProfile()->update([
                'approval_status' => match ($status) {
                    UserStatus::Active => ApprovalStatus::Approved->value,
                    UserStatus::Rejected => ApprovalStatus::Rejected->value,
                    default => $status->value,
                },
                'rejection_reason' => $reason,
            ]);
        });
    }
}
