<?php

namespace App\Services;

use App\Enums\ApprovalStatus;
use App\Enums\BirdStatus;
use App\Enums\OrderStatus;
use App\Models\Order;
use App\Models\User;

class SellerDashboardStatsService
{
    /**
     * @return array{birds: int, available_birds: int, live_birds: int, views: int, pending_orders: int, open_orders: int, delivered_orders: int}
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
            // Visible on the site right now: approved and still available.
            'live_birds' => $seller->birds()
                ->where('status', BirdStatus::Available->value)
                ->where('approval_status', ApprovalStatus::Approved->value)
                ->count(),
            'views' => (int) $seller->birds()->sum('views_count'),
            'pending_orders' => (clone $orders)
                ->where('status', OrderStatus::Pending->value)
                ->count(),
            'open_orders' => (clone $orders)
                ->whereIn('status', [OrderStatus::Confirmed->value, OrderStatus::Preparing->value, OrderStatus::OutForDelivery->value])
                ->count(),
            'delivered_orders' => (clone $orders)
                ->where('status', OrderStatus::Delivered->value)
                ->count(),
        ];
    }
}
