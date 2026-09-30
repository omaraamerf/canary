<?php

namespace App\Models;

use App\Enums\MediaProvider;
use Illuminate\Database\Eloquent\Model;

class Media extends Model
{
    protected $table = 'media';

    protected $fillable = ['type', 'provider', 'public_id', 'url', 'sort_order'];

    public function mediable()
    {
        return $this->morphTo();
    }

    public function isCloudinary(): bool
    {
        return $this->provider === MediaProvider::Cloudinary->value;
    }
}
