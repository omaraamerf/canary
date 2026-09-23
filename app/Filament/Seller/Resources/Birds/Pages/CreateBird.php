<?php

namespace App\Filament\Seller\Resources\Birds\Pages;

use App\Filament\Seller\Resources\Birds\BirdResource;
use App\Services\BirdService;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreateBird extends CreateRecord
{
    protected static string $resource = BirdResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        return app(BirdService::class)->createForSeller($data, auth()->user());
    }
}
