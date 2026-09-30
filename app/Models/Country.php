<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Country extends Model
{
    protected $fillable = ['name', 'name_en', 'code', 'active', 'sort_order'];

    protected function casts(): array
    {
        return ['active' => 'boolean'];
    }

    public function regions()
    {
        return $this->hasMany(Region::class);
    }

    public function scopeActive($query)
    {
        return $query->where('active', true)->orderBy('sort_order')->orderBy('name');
    }

    public function getLocalizedNameAttribute(): string
    {
        return app()->isLocale('en') && $this->name_en ? $this->name_en : $this->name;
    }
}
