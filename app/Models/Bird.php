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
        'currency', 'city', 'region_id', 'delivery_type', 'description', 'status', 'featured',
        'approval_status', 'rejection_reason', 'published_at',
    ];

    protected function casts(): array
    {
        return [
            'breeding_ready' => 'boolean',
            'featured' => 'boolean',
            'price' => 'decimal:2',
            'published_at' => 'datetime',
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

    public function region()
    {
        return $this->belongsTo(Region::class);
    }

    public function scopePublished($query)
    {
        return $query->where('approval_status', 'approved')
            ->whereHas('seller', fn ($seller) => $seller->where('status', 'active'));
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

    public function getCurrencyLabelAttribute(): string
    {
        if (blank($this->currency)) {
            return '';
        }

        $key = 'ui.currencies.'.$this->currency;

        return __($key) === $key ? $this->currency : __($key);
    }

    // "City, Region" — the region is dropped when it only repeats the city (e.g. "Damascus, Damascus").
    public function getLocationLabelAttribute(): string
    {
        return collect([$this->city, $this->region?->name])->filter()->unique()->implode('، ');
    }
}
