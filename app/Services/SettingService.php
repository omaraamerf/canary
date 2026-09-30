<?php

namespace App\Services;

use App\Enums\SettingKey;
use App\Models\Setting;
use Illuminate\Support\Facades\DB;

class SettingService
{
    public function create(array $data): Setting
    {
        return Setting::query()->create($data);
    }

    public function update(string $key, mixed $value): Setting
    {
        Setting::put($key, $value);

        return Setting::query()->where('key', $key)->firstOrFail();
    }

    public function approvalAndGuideSettings(): array
    {
        return [
            'sellerApprovalRequired' => Setting::boolean(SettingKey::SellerApprovalRequired->value, true),
            'listingApprovalRequired' => Setting::boolean(SettingKey::ListingApprovalRequired->value, true),
            'guideEnabled' => Setting::boolean(SettingKey::GuideEnabled->value, true),
        ];
    }

    public function updateApprovalAndGuideSettings(array $data): void
    {
        DB::transaction(function () use ($data) {
            foreach (SettingKey::cases() as $key) {
                if (array_key_exists($key->value, $data)) {
                    Setting::put($key->value, $data[$key->value] ? '1' : '0');
                }
            }
        });
    }
}
