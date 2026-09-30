<?php

namespace App\Filament\Resources\Settings\Pages;

use App\Filament\Resources\Settings\SettingResource;
use App\Services\SettingService;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreateSetting extends CreateRecord
{
    protected static string $resource = SettingResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        return app(SettingService::class)->create($data);
    }
}
