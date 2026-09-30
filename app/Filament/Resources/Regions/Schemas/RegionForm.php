<?php

namespace App\Filament\Resources\Regions\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class RegionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('country_id')
                    ->label(__('الدولة'))
                    ->relationship('country', 'name')
                    ->searchable()
                    ->preload()
                    ->required(),
                TextInput::make('name')
                    ->label(__('اسم المنطقة'))
                    ->required(),
                Toggle::make('active')
                    ->label(__('نشطة'))
                    ->required(),
                TextInput::make('sort_order')
                    ->label(__('الترتيب'))
                    ->required()
                    ->numeric()
                    ->default(0),
            ]);
    }
}
