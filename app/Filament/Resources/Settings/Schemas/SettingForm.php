<?php

namespace App\Filament\Resources\Settings\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class SettingForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('key')
                    ->label('المفتاح')
                    ->disabled()
                    ->dehydrated(),
                Select::make('value')
                    ->label('القيمة')
                    ->options([
                        '1' => 'مفعّل',
                        '0' => 'معطّل',
                    ])
                    ->required(),
            ]);
    }
}
