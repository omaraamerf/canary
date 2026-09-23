<?php

namespace App\Filament\Resources\Regions\Pages;

use App\Filament\Resources\Regions\RegionResource;
use App\Services\CatalogService;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreateRegion extends CreateRecord
{
    protected static string $resource = RegionResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        return app(CatalogService::class)->saveRegion($data);
    }
}
