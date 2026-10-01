<?php

namespace App\Filament\Seller\Widgets;

use App\Filament\Seller\Resources\Birds\BirdResource;
use App\Filament\Seller\Resources\Orders\OrderResource;
use App\Models\User;
use App\Services\SellerDashboardStatsService;
use Filament\Facades\Filament;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class SellerStats extends StatsOverviewWidget
{
    protected static ?int $sort = 1;

    // Two per row on phones instead of four full-width cards.
    protected int|array|null $columns = ['default' => 2, 'lg' => 4];

    protected function getStats(): array
    {
        /** @var User $seller */
        $seller = Filament::auth()->user();
        $stats = app(SellerDashboardStatsService::class)->summary($seller);
        $waiting = $stats['pending_orders'] > 0;

        return [
            Stat::make(__('طلبات جديدة'), number_format($stats['pending_orders']))
                ->description($waiting ? __('بانتظار تأكيدك') : __('لا طلبات تنتظرك'))
                ->descriptionIcon($waiting ? 'lucide-bell-ring' : 'lucide-circle-check')
                ->color($waiting ? 'warning' : 'success')
                ->url(OrderResource::getUrl('index', ['filters' => ['status' => ['value' => 'pending']]])),
            Stat::make(__('طلبات قيد التنفيذ'), number_format($stats['open_orders']))
                ->description(number_format($stats['delivered_orders']).__(' طلبات تم تسليمها'))
                ->descriptionIcon('lucide-truck')
                ->color('info')
                ->url(OrderResource::getUrl()),
            Stat::make(__('إعلانات ظاهرة'), number_format($stats['live_birds']))
                ->description(__('من أصل :count إعلان', ['count' => number_format($stats['birds'])]))
                ->descriptionIcon('lucide-bird')
                ->color('primary')
                ->url(BirdResource::getUrl()),
            Stat::make(__('المشاهدات'), number_format($stats['views']))
                ->description(__('زيارات صفحات إعلاناتك'))
                ->descriptionIcon('lucide-eye')
                ->color('gray'),
        ];
    }
}
