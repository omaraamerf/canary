<?php

namespace App\Filament\Resources\Breeds\Pages;

use App\Filament\Resources\Breeds\BreedResource;
use App\Services\CatalogService;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreateBreed extends CreateRecord
{
    protected static string $resource = BreedResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        return app(CatalogService::class)->saveBreed($data);
    }
}
