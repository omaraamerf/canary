<?php

namespace App\Filament\Widgets;

use App\Services\DashboardStatsService;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class MarketplaceStats extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        $stats = app(DashboardStatsService::class)->summary();

        return [
            Stat::make(__('المستخدمون'), number_format($stats['users']))
                ->description(__('إجمالي الحسابات المسجلة'))
                ->descriptionIcon('heroicon-m-users')
                ->color('primary'),
            Stat::make(__('البائعون'), number_format($stats['sellers']))
                ->description(__('حسابات البائعين'))
                ->descriptionIcon('heroicon-m-building-storefront')
                ->color('warning'),
            Stat::make(__('المبيعات'), number_format($stats['sales']))
                ->description(__('الطلبات التي تم تسليمها'))
                ->descriptionIcon('heroicon-m-shopping-bag')
                ->color('success'),
            Stat::make(__('الزيارات'), number_format($stats['visits']))
                ->description(number_format($stats['unique_visitors']).__(' زائر فريد'))
                ->descriptionIcon('heroicon-m-eye')
                ->color('info'),
        ];
    }
}
