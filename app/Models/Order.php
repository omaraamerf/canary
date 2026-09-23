<?php

namespace App\Models;

use App\Enums\OrderStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'reference', 'bird_id', 'buyer_name', 'phone', 'city', 'buyer_region_id', 'delivery_method',
        'notes', 'status', 'price_snapshot', 'currency_snapshot',
    ];

    protected function casts(): array
    {
        return ['price_snapshot' => 'decimal:2'];
    }

    public function bird()
    {
        return $this->belongsTo(Bird::class);
    }

    public function statusLogs()
    {
        return $this->hasMany(OrderStatusLog::class)->latest();
    }

    public function buyerRegion()
    {
        return $this->belongsTo(Region::class, 'buyer_region_id');
    }

    public function buyerPhoneCanBeRevealed(): bool
    {
        if ($this->status === OrderStatus::Preparing->value) {
            return true;
        }

        if ($this->relationLoaded('statusLogs')) {
            return $this->statusLogs->contains('new_status', OrderStatus::Preparing->value);
        }

        return $this->statusLogs()
            ->where('new_status', OrderStatus::Preparing->value)
            ->exists();
    }
}
