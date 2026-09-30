<?php

namespace App\Filament\Resources\Birds\Pages;

use App\Filament\Resources\Birds\BirdResource;
use App\Filament\Shared\Actions\EntityActions;
use App\Services\BirdService;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;

class EditBird extends EditRecord
{
    protected static string $resource = BirdResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EntityActions::delete(),
            EntityActions::forceDelete(),
            EntityActions::restore(),
        ];
    }

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        return app(BirdService::class)->updateForAdmin($record, $data);
    }
}
