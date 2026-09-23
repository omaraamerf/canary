<?php

namespace App\Filament\Seller\Resources\Birds\Pages;

use App\Filament\Seller\Resources\Birds\BirdResource;
use App\Services\BirdService;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;

class EditBird extends EditRecord
{
    protected static string $resource = BirdResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        return app(BirdService::class)->updateForSeller($record, $data);
    }
}
