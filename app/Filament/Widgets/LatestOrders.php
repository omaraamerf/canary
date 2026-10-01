<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\Orders\OrderResource;
use App\Filament\Shared\Tables\Columns;
use App\Models\Order;
use Filament\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;

class LatestOrders extends TableWidget
{
    protected static ?int $sort = 6;

    protected int|string|array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->heading(__('آخر الطلبات'))
            ->query(fn () => Order::query()->with(['bird.media', 'bird.seller.sellerProfile'])->latest()->limit(6))
            ->paginated(false)
            ->columns([
                Columns::birdImage('bird_image', fn (Order $order) => $order->bird),
                TextColumn::make('bird.title')->label(__('الطائر'))->weight('semibold'),
                TextColumn::make('bird.seller.public_name')->label(__('البائع'))->visibleFrom('lg'),
                TextColumn::make('buyer_name')->label(__('المشتري'))->visibleFrom('md'),
                Columns::orderStatus(),
                Columns::price('price_snapshot', 'currency_snapshot')->visibleFrom('md'),
                TextColumn::make('created_at')->label(__('تاريخ الطلب'))->since(),
            ])
            ->recordUrl(fn (Order $record): string => OrderResource::getUrl('view', ['record' => $record]))
            ->headerActions([
                Action::make('all-orders')->label(__('كل الطلبات'))->url(OrderResource::getUrl())->link(),
            ])
            ->emptyStateIcon('lucide-package')
            ->emptyStateHeading(__('لا توجد طلبات بعد'));
    }
}
