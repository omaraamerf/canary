<?php

namespace App\Filament\Widgets;

use App\Services\DashboardStatsService;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class MarketplaceStats extends StatsOverviewWidget
{
    protected static ?int $sort = 1;

    // Two per row on phones instead of four full-width cards.
    protected int|array|null $columns = ['default' => 2, 'lg' => 4];

    protected function getStats(): array
    {
        $service = app(DashboardStatsService::class);
        $stats = $service->summary();
        // Two-week trend lines under the traffic and order figures.
        $visits = $service->dailyVisits(14);
        $orders = $service->dailyOrders(14);

        return [
            Stat::make(__('الزيارات'), number_format($stats['visits']))
                ->description(number_format($stats['unique_visitors']).__(' زائر فريد'))
                ->descriptionIcon('lucide-eye')
                ->chart($visits['views'])
                ->color('info'),
            Stat::make(__('المبيعات'), number_format($stats['sales']))
                ->description(__('الطلبات التي تم تسليمها'))
                ->descriptionIcon('lucide-package-check')
                ->chart($orders['orders'])
                ->color('success'),
            Stat::make(__('البائعون'), number_format($stats['sellers']))
                ->description(__('حسابات البائعين'))
                ->descriptionIcon('lucide-store')
                ->color('primary'),
            Stat::make(__('المستخدمون'), number_format($stats['users']))
                ->description(__('إجمالي الحسابات المسجلة'))
                ->descriptionIcon('lucide-users')
                ->color('gray'),
        ];
    }
}
