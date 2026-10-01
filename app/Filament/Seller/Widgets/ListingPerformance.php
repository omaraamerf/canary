<?php

namespace App\Filament\Seller\Widgets;

use App\Filament\Seller\Resources\Birds\BirdResource;
use App\Filament\Shared\Tables\Columns;
use App\Models\Bird;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;

/** Each listing's views and reservation requests, most viewed first. */
class ListingPerformance extends TableWidget
{
    protected static ?int $sort = 3;

    protected int|string|array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->heading(__('أداء إعلاناتك'))
            ->description(__('تُحسب المشاهدة مرة واحدة لكل زائر، ولا تُحسب زياراتك أنت.'))
            ->query(fn () => BirdResource::getEloquentQuery()->withCount('orders')->orderByDesc('views_count'))
            ->defaultPaginationPageOption(5)
            ->columns([
                Columns::birdImage(),
                TextColumn::make('title')->label(__('الإعلان'))->weight('semibold'),
                Columns::birdStatus(),
                Columns::approvalStatus()->visibleFrom('md'),
                TextColumn::make('views_count')->label(__('المشاهدات'))->numeric()->icon('lucide-eye'),
                TextColumn::make('orders_count')->label(__('الطلبات'))->numeric()->visibleFrom('sm'),
            ])
            ->recordUrl(fn (Bird $record): string => BirdResource::getUrl('edit', ['record' => $record]))
            ->emptyStateIcon('lucide-bird')
            ->emptyStateHeading(__('لم تضف أي طائر بعد'))
            ->emptyStateDescription(__('أضف أول إعلان ليظهر أداؤه هنا.'));
    }
}
