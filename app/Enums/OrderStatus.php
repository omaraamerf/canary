<?php

namespace App\Enums;

enum OrderStatus: string
{
    case Pending = 'pending';
    case Confirmed = 'confirmed';
    case Preparing = 'preparing';
    case OutForDelivery = 'out_for_delivery';
    case Delivered = 'delivered';
    case Cancelled = 'cancelled';

    public function reservesBird(): bool
    {
        return $this === self::Confirmed;
    }

    public function completesSale(): bool
    {
        return $this === self::Delivered;
    }
}
