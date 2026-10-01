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

    public function label(): string
    {
        return __('ui.order_status.'.$this->value);
    }

    /** Variant of <x-ui.badge> for this status. */
    public function badge(): string
    {
        return match ($this) {
            self::Pending => 'warning',
            self::Confirmed, self::Preparing => 'info',
            self::OutForDelivery => 'primary',
            self::Delivered => 'success',
            self::Cancelled => 'danger',
        };
    }

    public function icon(): string
    {
        return match ($this) {
            self::Pending => 'clock',
            self::Confirmed => 'circle-check',
            self::Preparing => 'package',
            self::OutForDelivery => 'truck',
            self::Delivered => 'package-check',
            self::Cancelled => 'circle-x',
        };
    }

    public function reservesBird(): bool
    {
        return $this === self::Confirmed;
    }

    public function completesSale(): bool
    {
        return $this === self::Delivered;
    }
}
