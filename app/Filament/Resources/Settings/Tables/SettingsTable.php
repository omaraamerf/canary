<?php

namespace App\Filament\Resources\Settings\Tables;

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
                    ->searchable(),
                TextColumn::make('value')
                    ->label(__('القيمة'))
                    ->badge()
                    ->formatStateUsing(fn ($state): string => filter_var($state, FILTER_VALIDATE_BOOL) ? __('مفعّل') : __('معطّل')),
                TextColumn::make('updated_at')
                    ->label(__('آخر تحديث'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->recordActions([
                EditAction::make()->label(__('تعديل')),
            ]);
    }
}
