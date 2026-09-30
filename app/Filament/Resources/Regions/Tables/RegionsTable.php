<?php

namespace App\Filament\Resources\Regions\Tables;

use App\Filament\Shared\Actions\EntityActions;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class RegionsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label(__('اسم المنطقة'))
                    ->searchable(),
                TextColumn::make('country.name')
                    ->label(__('الدولة'))
                    ->badge()
                    ->sortable(),
                TextColumn::make('slug')
                    ->label(__('المعرّف'))
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                IconColumn::make('active')
                    ->label(__('نشطة'))
                    ->boolean(),
                TextColumn::make('sort_order')
                    ->label(__('الترتيب'))
                    ->numeric()
                    ->sortable(),
                TextColumn::make('created_at')
                    ->label(__('تاريخ الإنشاء'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->label(__('آخر تحديث'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('country_id')
                    ->label(__('الدولة'))
                    ->relationship('country', 'name')
                    ->searchable()
                    ->preload(),
            ])
            ->defaultSort('sort_order')
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    EntityActions::deleteBulk(),
                ]),
            ]);
    }
}
