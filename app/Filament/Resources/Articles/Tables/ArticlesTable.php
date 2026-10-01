<?php

namespace App\Filament\Resources\Articles\Tables;

use App\Filament\Shared\Actions\EntityActions;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ArticlesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('category.name')
                    ->label(__('القسم'))
                    ->sortable()
                    ->visibleFrom('md'),
                TextColumn::make('title')
                    ->label(__('العنوان'))
                    ->searchable(),
                TextColumn::make('slug')
                    ->label(__('المعرّف'))
                    ->searchable()
                    ->visibleFrom('lg'),
                TextColumn::make('status')
                    ->label(__('الحالة'))
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'draft' => __('مسودة'),
                        'published' => __('منشور'),
                        'archived' => __('مؤرشف'),
                        default => $state,
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'published' => 'success',
                        'archived' => 'warning',
                        default => 'gray',
                    }),
                TextColumn::make('published_at')
                    ->label(__('تاريخ النشر'))
                    ->dateTime()
                    ->sortable()
                    ->visibleFrom('md'),
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
