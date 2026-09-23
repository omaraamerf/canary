<?php

namespace App\Filament\Resources\Settings\Pages;

use App\Filament\Resources\Settings\SettingResource;
use App\Services\SettingService;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;

class EditSetting extends EditRecord
{
    protected static string $resource = SettingResource::class;

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        return app(SettingService::class)->update($record->key, $data['value']);
    }
}
