<?php

namespace App\Filament\Resources\Breeds\Tables;

use App\Filament\Shared\Actions\EntityActions;
use App\Models\Breed;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class BreedsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label(__('اسم السلالة'))
                    ->formatStateUsing(fn (Breed $record): string => $record->localized_name)
                    ->searchable(),
                TextColumn::make('slug')
                    ->label(__('المعرّف'))
                    ->searchable(),
                IconColumn::make('active')
                    ->label(__('نشطة'))
                    ->boolean(),
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
                //
            ])
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
