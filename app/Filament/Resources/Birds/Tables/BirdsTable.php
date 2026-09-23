<?php

namespace App\Filament\Resources\Birds\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
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
                ImageColumn::make('primary_image')->label('الصورة')->square(),
                TextColumn::make('title')->label('الإعلان')->searchable()->sortable(),
                TextColumn::make('breed.name')->label('السلالة')->sortable(),
                TextColumn::make('seller.name')->label('البائع')->searchable(),
                TextColumn::make('price')
                    ->label('السعر')
                    ->formatStateUsing(fn ($state, $record): string => number_format((float) $state, 2).' '.$record->currency)
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
                SelectFilter::make('status')->label('الحالة')->options([
                    'available' => 'متاح',
                    'reserved' => 'محجوز',
                    'sold' => 'مباع',
                ]),
                SelectFilter::make('approval_status')->label('المراجعة')->options([
                    'pending' => 'بانتظار المراجعة',
                    'approved' => 'منشور',
                    'rejected' => 'مرفوض',
                ]),
                TrashedFilter::make(),
            ])
            ->recordActions([EditAction::make()])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                ]),
            ]);
    }
}
