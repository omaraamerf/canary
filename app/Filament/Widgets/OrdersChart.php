<?php

namespace App\Filament\Widgets;

use App\Services\DashboardStatsService;
use Filament\Widgets\ChartWidget;

class OrdersChart extends ChartWidget
{
    protected static ?int $sort = 5;

    protected ?string $maxHeight = '260px';

    public function getHeading(): string
    {
        return __('الطلبات خلال 30 يومًا');
    }

    protected function getType(): string
    {
        return 'bar';
    }

    protected function getData(): array
    {
        $series = app(DashboardStatsService::class)->dailyOrders();

        return [
            'labels' => $series['labels'],
            'datasets' => [
                ['label' => __('الطلبات'), 'data' => $series['orders'], 'backgroundColor' => '#f2c94c', 'borderRadius' => 6],
            ],
        ];
    }

    protected function getOptions(): array
    {
        // Whole orders only on the axis; a few upright dates instead of thirty slanted ones.
        return [
            'scales' => [
                'x' => ['ticks' => ['maxRotation' => 0, 'autoSkip' => true, 'maxTicksLimit' => 8]],
                'y' => ['beginAtZero' => true, 'ticks' => ['precision' => 0]],
            ],
            'plugins' => ['legend' => ['display' => false]],
        ];
    }
}
