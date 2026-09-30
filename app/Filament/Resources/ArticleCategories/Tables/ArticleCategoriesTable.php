<?php

namespace App\Filament\Resources\ArticleCategories\Tables;

use App\Filament\Shared\Actions\EntityActions;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ArticleCategoriesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label(__('اسم القسم'))
                    ->searchable(),
                TextColumn::make('slug')
                    ->label(__('المعرّف'))
                    ->searchable(),
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
