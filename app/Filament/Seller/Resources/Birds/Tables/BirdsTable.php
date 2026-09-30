<?php

namespace App\Filament\Seller\Resources\Birds\Tables;

use App\Filament\Shared\Actions\EntityActions;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
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
                    ->formatStateUsing(fn ($record): string => $record->breed->localized_name),
                TextColumn::make('price')
                    ->label(__('السعر'))
                    ->formatStateUsing(fn ($state): string => number_format((float) $state, 2).' SAR')
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
                SelectFilter::make('approval_status')->label(__('المراجعة'))->options([
                    'pending' => __('بانتظار المراجعة'),
                    'approved' => __('منشور'),
                    'rejected' => __('مرفوض'),
                ]),
            ])
            ->recordActions([EditAction::make()])
            ->toolbarActions([EntityActions::deleteBulk()]);
    }
}
