<?php

namespace App\Filament\Seller\Pages;

use App\Filament\Seller\Resources\Birds\BirdResource;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Pages\Dashboard as BaseDashboard;
use Filament\Support\Enums\Size;

/** Seller home: new orders and how each listing is doing, with "add a bird" up front. */
class Dashboard extends BaseDashboard
{
    protected static string|BackedEnum|null $navigationIcon = 'lucide-layout-dashboard';

    protected function getHeaderActions(): array
    {
        return [
            Action::make('add-bird')
                ->label(__('ui.birds.add'))
                ->icon('lucide-circle-plus')
                ->size(Size::Large)
                ->url(BirdResource::getUrl('create')),
        ];
    }
}
