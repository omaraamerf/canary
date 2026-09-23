<?php

namespace App\Services;

use App\Enums\BirdStatus;
use App\Enums\OrderStatus;
use App\Models\Order;
use App\Models\User;

class SellerDashboardStatsService
{
    /**
     * @return array{birds: int, available_birds: int, pending_orders: int, delivered_orders: int}
     */
    public function summary(User $seller): array
    {
        $orders = Order::query()
            ->whereHas('bird', fn ($query) => $query->where('seller_id', $seller->id));

        return [
            'birds' => $seller->birds()->count(),
            'available_birds' => $seller->birds()
                ->where('status', BirdStatus::Available->value)
                ->count(),
            'pending_orders' => (clone $orders)
                ->where('status', OrderStatus::Pending->value)
                ->count(),
            'delivered_orders' => (clone $orders)
                ->where('status', OrderStatus::Delivered->value)
                ->count(),
        ];
    }
}
