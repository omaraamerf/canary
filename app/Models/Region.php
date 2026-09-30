<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Region extends Model
{
    protected $fillable = ['country_id', 'name', 'slug', 'parent_id', 'active', 'sort_order'];

    protected function casts(): array
    {
        return ['active' => 'boolean', 'country_id' => 'integer'];
    }

    public function country()
    {
        return $this->belongsTo(Country::class);
    }

    public function birds()
    {
        return $this->hasMany(Bird::class);
    }

    public function sellers()
    {
        return $this->hasMany(SellerProfile::class);
    }

    public function getFullNameAttribute(): string
    {
        return $this->country ? $this->name.'، '.$this->country->localized_name : $this->name;
    }

    /**
     * Active regions grouped by country name, for grouped select inputs.
     *
     * @return array<string, array<int, string>>
     */
    public static function groupedOptions(): array
    {
        return static::query()
            ->with('country')
            ->where('active', true)
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get()
            ->sortBy(fn (Region $region) => $region->country?->sort_order ?? PHP_INT_MAX)
            ->groupBy(fn (Region $region) => $region->country?->localized_name ?? __('أخرى'))
            ->map(fn ($regions) => $regions->pluck('name', 'id')->all())
            ->all();
    }
}
