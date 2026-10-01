<?php

namespace App\Filament\Widgets;

use App\Services\DashboardStatsService;
use Filament\Widgets\ChartWidget;

class VisitsChart extends ChartWidget
{
    protected static ?int $sort = 4;

    protected ?string $maxHeight = '260px';

    public function getHeading(): string
    {
        return __('الزيارات خلال 30 يومًا');
    }

    protected function getType(): string
    {
        return 'line';
    }

    protected function getData(): array
    {
        $series = app(DashboardStatsService::class)->dailyVisits();

        return [
            'labels' => $series['labels'],
            'datasets' => [
                ['label' => __('مشاهدات الصفحات'), 'data' => $series['views'], 'borderColor' => '#315e43', 'backgroundColor' => 'rgba(49, 94, 67, 0.12)', 'fill' => true, 'tension' => 0.35, 'pointRadius' => 0],
                ['label' => __('الزوار'), 'data' => $series['visitors'], 'borderColor' => '#d9a521', 'backgroundColor' => 'transparent', 'tension' => 0.35, 'pointRadius' => 0],
            ],
        ];
    }

    protected function getOptions(): array
    {
        return [
            'scales' => [
                'x' => ['ticks' => ['maxRotation' => 0, 'autoSkip' => true, 'maxTicksLimit' => 8]],
                'y' => ['beginAtZero' => true, 'ticks' => ['precision' => 0]],
            ],
        ];
    }
}
