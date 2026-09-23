<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SiteVisit extends Model
{
    protected $fillable = [
        'visited_on',
        'page_views',
        'unique_visitors',
    ];

    protected function casts(): array
    {
        return [
            'visited_on' => 'date',
            'page_views' => 'integer',
            'unique_visitors' => 'integer',
        ];
    }
}
