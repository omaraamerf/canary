<?php

namespace App\Filament\Resources\Users\Pages;

use App\Filament\Resources\Users\UserResource;
use App\Filament\Shared\Actions\EntityActions;
use App\Services\UserService;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;

class EditUser extends EditRecord
{
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EntityActions::delete(),
        ];
    }

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        return app(UserService::class)->update($record, $data);
    }
}
