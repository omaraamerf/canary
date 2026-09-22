<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Region extends Model
{
    protected $fillable = ['name', 'slug', 'parent_id', 'active', 'sort_order'];

    protected function casts(): array
    {
        return ['active' => 'boolean'];
    }

    public function birds()
    {
        return $this->hasMany(Bird::class);
    }

    public function sellers()
    {
        return $this->hasMany(SellerProfile::class);
    }
}
