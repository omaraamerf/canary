<?php

namespace App\Filament\Resources\Orders\Tables;

use App\Filament\Shared\Actions\UpdateOrderStatusAction;
use App\Filament\Shared\Tables\Columns;
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
                TextColumn::make('reference')->label(__('المرجع'))->searchable()->sortable()->fontFamily('mono')->visibleFrom('md'),
                TextColumn::make('bird.title')->label(__('الطائر'))->searchable()->weight('semibold'),
                TextColumn::make('bird.seller.name')->label(__('البائع'))->searchable()->visibleFrom('lg'),
                TextColumn::make('buyer_name')->label(__('المشتري'))->searchable()->visibleFrom('md'),
                TextColumn::make('phone')->label(__('الهاتف'))->searchable()->visibleFrom('lg'),
                Columns::orderStatus(),
                Columns::price('price_snapshot', 'currency_snapshot')->visibleFrom('md'),
                TextColumn::make('created_at')->label(__('تاريخ الطلب'))->since()->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label(__('الحالة'))
                    ->options(UpdateOrderStatusAction::statusOptions()),
            ])
            ->recordActions([
                ViewAction::make()->label(__('عرض')),
                UpdateOrderStatusAction::make(),
            ])
            ->defaultSort('created_at', 'desc');
    }
}
