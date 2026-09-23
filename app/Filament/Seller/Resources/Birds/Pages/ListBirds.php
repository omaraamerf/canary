<?php

namespace App\Filament\Seller\Resources\Birds\Pages;

use App\Filament\Seller\Resources\Birds\BirdResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListBirds extends ListRecords
{
    protected static string $resource = BirdResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
