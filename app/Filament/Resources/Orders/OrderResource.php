<?php

namespace App\Filament\Resources\Orders;

use App\Enums\OrderStatus;
use App\Filament\Navigation\AdminGroup;
use App\Filament\Resources\Orders\Pages\ListOrders;
use App\Filament\Resources\Orders\Pages\ViewOrder;
use App\Filament\Resources\Orders\Schemas\OrderInfolist;
use App\Filament\Resources\Orders\Tables\OrdersTable;
use App\Models\Order;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use UnitEnum;

class OrderResource extends Resource
{
    protected static ?string $model = Order::class;

    protected static string|BackedEnum|null $navigationIcon = 'lucide-package';

    protected static string|UnitEnum|null $navigationGroup = AdminGroup::Market;

    protected static ?int $navigationSort = 2;

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
        $count = Order::query()->where('status', OrderStatus::Pending->value)->count();

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
        return parent::getEloquentQuery()->with(['bird.seller', 'buyerRegion']);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListOrders::route('/'),
            'view' => ViewOrder::route('/{record}'),
        ];
    }
}
