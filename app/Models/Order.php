<?php

namespace App\Models;

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
}
