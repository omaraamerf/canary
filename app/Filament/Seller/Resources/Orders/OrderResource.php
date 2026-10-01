<?php

namespace App\Filament\Seller\Resources\Orders;

use App\Enums\OrderStatus;
use App\Filament\Seller\Resources\Orders\Pages\ListOrders;
use App\Filament\Seller\Resources\Orders\Pages\ViewOrder;
use App\Filament\Seller\Resources\Orders\Schemas\OrderInfolist;
use App\Filament\Seller\Resources\Orders\Tables\OrdersTable;
use App\Models\Order;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class OrderResource extends Resource
{
    protected static ?string $model = Order::class;

    protected static string|BackedEnum|null $navigationIcon = 'lucide-package';

    protected static ?int $navigationSort = 3;

    public static function getModelLabel(): string { return __('طلب'); }

    public static function getPluralModelLabel(): string { return __('الطلبات'); }

    public static function getNavigationLabel(): string { return __('الطلبات'); }

    protected static ?string $recordTitleAttribute = 'reference';

    public static function infolist(Schema $schema): Schema
    {
        return OrderInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return OrdersTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    /** Count of what is waiting, shown beside the sidebar item. */
    public static function getNavigationBadge(): ?string
    {
        $count = static::getEloquentQuery()->where('status', OrderStatus::Pending->value)->count();

        return $count > 0 ? (string) $count : null;
    }

    public static function getNavigationBadgeColor(): string
    {
        return 'warning';
    }

    public static function getNavigationBadgeTooltip(): ?string
    {
        return __('طلبات جديدة');
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->whereHas('bird', fn (Builder $query) => $query->where('seller_id', auth()->id()))
            ->with(['bird', 'buyerRegion', 'statusLogs']);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListOrders::route('/'),
            'view' => ViewOrder::route('/{record}'),
        ];
    }
}
