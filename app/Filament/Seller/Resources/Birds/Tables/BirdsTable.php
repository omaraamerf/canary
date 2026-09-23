<?php

namespace App\Filament\Seller\Resources\Birds\Tables;

use Filament\Actions\DeleteBulkAction;
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
                ImageColumn::make('primary_image')->label('الصورة')->square(),
                TextColumn::make('title')->label('الإعلان')->searchable()->sortable(),
                TextColumn::make('breed.name')->label('السلالة'),
                TextColumn::make('price')
                    ->label('السعر')
                    ->formatStateUsing(fn ($state): string => number_format((float) $state, 2).' SAR')
                    ->sortable(),
                TextColumn::make('status')
                    ->label('الحالة')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'available' => 'متاح',
                        'reserved' => 'محجوز',
                        'sold' => 'مباع',
                        default => $state,
                    }),
                TextColumn::make('approval_status')
                    ->label('المراجعة')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'pending' => 'بانتظار المراجعة',
                        'approved' => 'منشور',
                        'rejected' => 'مرفوض',
                        default => $state,
                    }),
                TextColumn::make('created_at')->label('تاريخ الإضافة')->dateTime()->sortable(),
            ])
            ->filters([
                SelectFilter::make('approval_status')->label('المراجعة')->options([
                    'pending' => 'بانتظار المراجعة',
                    'approved' => 'منشور',
                    'rejected' => 'مرفوض',
                ]),
            ])
            ->recordActions([EditAction::make()])
            ->toolbarActions([DeleteBulkAction::make()]);
    }
}
