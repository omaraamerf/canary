<?php

namespace App\Filament\Resources\Birds\Tables;

use App\Filament\Shared\Actions\EntityActions;
use App\Filament\Shared\Tables\Columns;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;

class BirdsTable
{
    public static function configure(Table $table): Table
    {
        // On phones: photo, title, price and status; the rest from tablet width up.
        return $table
            ->columns([
                Columns::birdImage(),
                TextColumn::make('title')->label(__('الإعلان'))->searchable()->sortable()->weight('semibold'),
                TextColumn::make('breed.name')
                    ->label(__('السلالة'))
                    ->formatStateUsing(fn ($record): string => $record->breed->localized_name)
                    ->sortable()
                    ->visibleFrom('md'),
                TextColumn::make('seller.name')->label(__('البائع'))->searchable()->visibleFrom('lg'),
                Columns::price('price', 'currency')->sortable(),
                Columns::birdStatus(),
                Columns::approvalStatus()->visibleFrom('md'),
                TextColumn::make('views_count')->label(__('المشاهدات'))->numeric()->sortable()->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('created_at')->label(__('تاريخ الإضافة'))->date('j F Y')->sortable()->visibleFrom('xl'),
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
            ])
            ->defaultSort('created_at', 'desc');
    }
}
