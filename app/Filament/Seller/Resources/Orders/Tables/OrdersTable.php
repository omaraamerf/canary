<?php

namespace App\Filament\Seller\Resources\Orders\Tables;

use App\Filament\Shared\Actions\UpdateOrderStatusAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class OrdersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('reference')->label('المرجع')->searchable()->sortable(),
                TextColumn::make('bird.title')->label('الطائر')->searchable(),
                TextColumn::make('buyer_name')->label('المشتري')->searchable(),
                TextColumn::make('phone')
                    ->label('الهاتف')
                    ->formatStateUsing(fn (string $state, $record): string => $record->buyerPhoneCanBeRevealed()
                        ? $state
                        : 'يظهر بعد تحويل الطلب إلى قيد التجهيز'),
                TextColumn::make('buyerRegion.name')->label('المنطقة'),
                TextColumn::make('status')
                    ->label('الحالة')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => UpdateOrderStatusAction::statusOptions()[$state] ?? $state),
                TextColumn::make('price_snapshot')
                    ->label('السعر')
                    ->formatStateUsing(fn ($state, $record): string => number_format((float) $state, 2).' '.$record->currency_snapshot),
                TextColumn::make('created_at')->label('تاريخ الطلب')->dateTime()->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('الحالة')
                    ->options(UpdateOrderStatusAction::statusOptions()),
            ])
            ->recordActions([
                ViewAction::make()->label('عرض'),
                UpdateOrderStatusAction::make(),
            ])
            ->defaultSort('created_at', 'desc');
    }
}
