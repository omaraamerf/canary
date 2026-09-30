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
                    ->label(__('المفتاح'))
                    ->disabled()
                    ->dehydrated(),
                Select::make('value')
                    ->label(__('القيمة'))
                    ->options([
                        '1' => __('مفعّل'),
                        '0' => __('معطّل'),
                    ])
                    ->required(),
            ]);
    }
}
