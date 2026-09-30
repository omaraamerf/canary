<?php

namespace App\Filament\Resources\Breeds\Schemas;

use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class BreedForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label(__('اسم السلالة'))
                    ->required(),
                Textarea::make('description')
                    ->label(__('الوصف'))
                    ->columnSpanFull(),
                Toggle::make('active')
                    ->label(__('نشطة'))
                    ->required(),
            ]);
    }
}
