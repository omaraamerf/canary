<?php

namespace App\Filament\Seller\Widgets;

use App\Filament\Seller\Resources\Orders\OrderResource;
use App\Filament\Shared\Tables\Columns;
use App\Models\Order;
use Filament\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;

class LatestOrders extends TableWidget
{
    protected static ?int $sort = 2;

    protected int|string|array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->heading(__('آخر الطلبات'))
            ->query(fn () => OrderResource::getEloquentQuery()->with('bird.media')->latest()->limit(5))
            ->paginated(false)
            ->columns([
                Columns::birdImage('bird_image', fn (Order $order) => $order->bird),
                TextColumn::make('bird.title')->label(__('الطائر'))->weight('semibold'),
                TextColumn::make('buyerRegion.name')->label(__('المنطقة'))->visibleFrom('md'),
                Columns::orderStatus(),
                Columns::price('price_snapshot', 'currency_snapshot')->visibleFrom('md'),
                TextColumn::make('created_at')->label(__('تاريخ الطلب'))->since(),
            ])
            ->recordUrl(fn (Order $record): string => OrderResource::getUrl('view', ['record' => $record]))
            ->headerActions([
                Action::make('all-orders')->label(__('كل الطلبات'))->url(OrderResource::getUrl())->link(),
            ])
            ->emptyStateIcon('lucide-package')
            ->emptyStateHeading(__('لا توجد طلبات بعد'))
            ->emptyStateDescription(__('تظهر هنا طلبات الحجز على إعلاناتك فور وصولها.'));
    }
}
