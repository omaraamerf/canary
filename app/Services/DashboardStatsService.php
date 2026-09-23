<?php

namespace App\Services;

use App\Enums\OrderStatus;
use App\Enums\UserRole;
use App\Models\Order;
use App\Models\SiteVisit;
use App\Models\User;

class DashboardStatsService
{
    /**
     * @return array{users: int, sellers: int, sales: int, visits: int, unique_visitors: int}
     */
    public function summary(): array
    {
        return [
            'users' => User::query()->count(),
            'sellers' => User::role(UserRole::Seller->value)->count(),
            'sales' => Order::query()->where('status', OrderStatus::Delivered->value)->count(),
            'visits' => (int) SiteVisit::query()->sum('page_views'),
            'unique_visitors' => (int) SiteVisit::query()->sum('unique_visitors'),
        ];
    }
}
