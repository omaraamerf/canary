<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ArticleCategory extends Model
{
    protected $fillable = ['name', 'slug', 'description', 'image', 'sort_order'];

    public function articles()
    {
        return $this->hasMany(Article::class, 'category_id');
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }
}
