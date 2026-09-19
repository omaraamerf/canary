<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Bird extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'seller_id', 'breed_id', 'title', 'slug', 'sex', 'hatch_year', 'color',
        'molt_status', 'breeding_ready', 'singing_status', 'ring_number', 'price',
        'currency', 'city', 'delivery_type', 'description', 'status', 'featured',
    ];

    protected function casts(): array
    {
        return [
            'breeding_ready' => 'boolean',
            'featured' => 'boolean',
            'price' => 'decimal:2',
        ];
    }

    public function breed()
    {
        return $this->belongsTo(Breed::class);
    }

    public function seller()
    {
        return $this->belongsTo(User::class, 'seller_id');
    }

    public function media()
    {
        return $this->hasMany(BirdMedia::class)->orderBy('sort_order');
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function getPrimaryImageAttribute(): string
    {
        return $this->media->firstWhere('type', 'image')?->url ?? '/images/birds/yellow-canary.jpg';
    }
}
