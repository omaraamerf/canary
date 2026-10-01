<?php

namespace App\Filament\Resources\Settings\Schemas;

use App\Enums\SettingKey;
use Filament\Forms\Components\Select;
use Filament\Schemas\Schema;

class SettingForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('value')
                    ->label(fn ($record): string => SettingKey::labelFor($record->key))
                    ->helperText(fn ($record): ?string => SettingKey::descriptionFor($record->key))
                    ->options([
                        '1' => __('مفعّل'),
                        '0' => __('معطّل'),
                    ])
                    ->native(false)
                    ->required(),
            ]);
    }
}
