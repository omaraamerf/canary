<?php

namespace App\Filament\Resources\Birds\Tables;

use App\Filament\Shared\Actions\EntityActions;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;

class BirdsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('primary_image')->label(__('الصورة'))->square(),
                TextColumn::make('title')->label(__('الإعلان'))->searchable()->sortable(),
                TextColumn::make('breed.name')
                    ->label(__('السلالة'))
                    ->formatStateUsing(fn ($record): string => $record->breed->localized_name)
                    ->sortable(),
                TextColumn::make('seller.name')->label(__('البائع'))->searchable(),
                TextColumn::make('price')
                    ->label(__('السعر'))
                    ->formatStateUsing(fn ($state, $record): string => number_format((float) $state, 2).' '.$record->currency)
                    ->sortable(),
                TextColumn::make('status')
                    ->label(__('الحالة'))
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'available' => __('متاح'),
                        'reserved' => __('محجوز'),
                        'sold' => __('مباع'),
                        default => $state,
                    }),
                TextColumn::make('approval_status')
                    ->label(__('المراجعة'))
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'pending' => __('بانتظار المراجعة'),
                        'approved' => __('منشور'),
                        'rejected' => __('مرفوض'),
                        default => $state,
                    }),
                TextColumn::make('created_at')->label(__('تاريخ الإضافة'))->dateTime()->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')->label(__('الحالة'))->options([
                    'available' => __('متاح'),
                    'reserved' => __('محجوز'),
                    'sold' => __('مباع'),
                ]),
                SelectFilter::make('approval_status')->label(__('المراجعة'))->options([
                    'pending' => __('بانتظار المراجعة'),
                    'approved' => __('منشور'),
                    'rejected' => __('مرفوض'),
                ]),
                TrashedFilter::make(),
            ])
            ->recordActions([EditAction::make()])
            ->toolbarActions([
                BulkActionGroup::make([
                    EntityActions::deleteBulk(),
                    EntityActions::forceDeleteBulk(),
                    EntityActions::restoreBulk(),
                ]),
            ]);
    }
}
