<?php

namespace App\Filament\Resources\Regions\Pages;

use App\Filament\Resources\Regions\RegionResource;
use App\Filament\Shared\Actions\EntityActions;
use App\Services\CatalogService;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;

class EditRegion extends EditRecord
{
    protected static string $resource = RegionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EntityActions::delete(),
        ];
    }

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        return app(CatalogService::class)->saveRegion($data, $record);
    }
}
