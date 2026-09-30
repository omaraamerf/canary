<?php

namespace App\Filament\Seller\Resources\Birds\Pages;

use App\Filament\Seller\Resources\Birds\BirdResource;
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
        ];
    }

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        return app(BirdService::class)->updateForSeller($record, $data);
    }
}
