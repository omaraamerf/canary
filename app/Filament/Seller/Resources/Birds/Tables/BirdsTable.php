<?php

namespace App\Filament\Seller\Resources\Birds\Tables;

use App\Filament\Shared\Actions\EntityActions;
use App\Filament\Shared\Tables\Columns;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class BirdsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                Columns::birdImage(),
                TextColumn::make('title')->label(__('الإعلان'))->searchable()->sortable()->weight('semibold'),
                TextColumn::make('breed.name')
                    ->label(__('السلالة'))
                    ->formatStateUsing(fn ($record): string => $record->breed->localized_name)
                    ->visibleFrom('md'),
                Columns::price('price', 'currency')->sortable(),
                Columns::birdStatus(),
                Columns::approvalStatus()->visibleFrom('md'),
                TextColumn::make('views_count')->label(__('المشاهدات'))->numeric()->sortable()->visibleFrom('md'),
                TextColumn::make('orders_count')->label(__('الطلبات'))->counts('orders')->sortable()->visibleFrom('lg'),
                TextColumn::make('created_at')->label(__('تاريخ الإضافة'))->date('j F Y')->sortable()->visibleFrom('lg'),
            ])
            ->filters([
                SelectFilter::make('approval_status')->label(__('المراجعة'))->options([
                    'pending' => __('بانتظار المراجعة'),
                    'approved' => __('منشور'),
                    'rejected' => __('مرفوض'),
                ]),
            ])
            ->recordActions([EditAction::make()])
            ->toolbarActions([EntityActions::deleteBulk()])
            ->defaultSort('created_at', 'desc');
    }
}
