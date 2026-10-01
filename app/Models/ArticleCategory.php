<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ArticleCategory extends Model
{
    /** Lucide icon per seeded category slug; new categories fall back to a book. */
    private const ICONS = [
        'care' => 'sprout',
        'health' => 'stethoscope',
        'sexing' => 'venus-and-mars',
        'readiness' => 'calendar-check',
        'breeding' => 'egg',
        'breeds' => 'bird',
        'colors-genetics' => 'dna',
        'common-problems' => 'circle-help',
    ];

    protected $fillable = ['name', 'slug', 'description', 'image', 'sort_order'];

    public function articles()
    {
        return $this->hasMany(Article::class, 'category_id');
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function getIconAttribute(): string
    {
        return self::ICONS[$this->slug] ?? 'book-open';
    }
}
