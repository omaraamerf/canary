<?php

namespace App\Filament\Resources\Breeds\Pages;

use App\Filament\Resources\Breeds\BreedResource;
use App\Filament\Shared\Actions\EntityActions;
use App\Services\CatalogService;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;

class EditBreed extends EditRecord
{
    protected static string $resource = BreedResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EntityActions::delete(),
        ];
    }

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        return app(CatalogService::class)->saveBreed($data, $record);
    }
}
