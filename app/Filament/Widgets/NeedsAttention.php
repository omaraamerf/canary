<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\Birds\BirdResource;
use App\Filament\Resources\Orders\OrderResource;
use App\Filament\Resources\Users\UserResource;
use App\Services\DashboardStatsService;
use Filament\Widgets\Widget;

/** What is waiting on the admin team, each row opening the matching filtered list. */
class NeedsAttention extends Widget
{
    protected static ?int $sort = 2;

    protected string $view = 'filament.widgets.link-list';

    protected function getViewData(): array
    {
        $counts = app(DashboardStatsService::class)->attention();

        return [
            'heading' => __('بانتظار الإجراء'),
            'icon' => 'lucide-bell-ring',
            'items' => [
                ['label' => __('طلبات جديدة'), 'icon' => 'lucide-package', 'count' => $counts['new_orders'],
                    'url' => OrderResource::getUrl('index', ['filters' => ['status' => ['value' => 'pending']]])],
                ['label' => __('طيور بانتظار المراجعة'), 'icon' => 'lucide-bird', 'count' => $counts['pending_birds'],
                    'url' => BirdResource::getUrl('index', ['filters' => ['approval_status' => ['value' => 'pending']]])],
                ['label' => __('بائعون بانتظار الموافقة'), 'icon' => 'lucide-store', 'count' => $counts['pending_sellers'],
                    'url' => UserResource::getUrl('index', ['filters' => ['status' => ['value' => 'pending']]])],
            ],
        ];
    }
}
