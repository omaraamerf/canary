<?php

namespace App\Filament\Resources\Settings\Tables;

use App\Enums\SettingKey;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class SettingsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('key')
                    ->label(__('الإعداد'))
                    ->formatStateUsing(fn (string $state): string => SettingKey::labelFor($state))
                    ->description(fn ($record): ?string => SettingKey::descriptionFor($record->key))
                    ->weight('medium')
                    ->wrap(),
                TextColumn::make('value')
                    ->label(__('الحالة'))
                    ->badge()
                    ->formatStateUsing(fn ($state): string => filter_var($state, FILTER_VALIDATE_BOOL) ? __('مفعّل') : __('معطّل'))
                    ->color(fn ($state): string => filter_var($state, FILTER_VALIDATE_BOOL) ? 'success' : 'gray'),
                TextColumn::make('updated_at')
                    ->label(__('آخر تحديث'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->paginated(false)
            ->recordActions([
                EditAction::make()->label(__('تعديل')),
            ]);
    }
}
