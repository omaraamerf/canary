<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BirdMedia extends Model
{
    use HasFactory;

    protected $fillable = ['bird_id', 'type', 'url', 'sort_order'];

    public function bird()
    {
        return $this->belongsTo(Bird::class);
    }

    public function getEmbedUrlAttribute(): string
    {
        if ($this->type !== 'video' || str_contains($this->url, '/preview')) {
            return $this->url;
        }

        preg_match('~(?:/d/|[?&]id=)([a-zA-Z0-9_-]+)~', $this->url, $matches);

        return isset($matches[1])
            ? "https://drive.google.com/file/d/{$matches[1]}/preview"
            : $this->url;
    }
}
