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
            Stat::make('المستخدمون', number_format($stats['users']))
                ->description('إجمالي الحسابات المسجلة')
                ->descriptionIcon('heroicon-m-users')
                ->color('primary'),
            Stat::make('البائعون', number_format($stats['sellers']))
                ->description('حسابات البائعين')
                ->descriptionIcon('heroicon-m-building-storefront')
                ->color('warning'),
            Stat::make('المبيعات', number_format($stats['sales']))
                ->description('الطلبات التي تم تسليمها')
                ->descriptionIcon('heroicon-m-shopping-bag')
                ->color('success'),
            Stat::make('الزيارات', number_format($stats['visits']))
                ->description(number_format($stats['unique_visitors']).' زائر فريد')
                ->descriptionIcon('heroicon-m-eye')
                ->color('info'),
        ];
    }
}
