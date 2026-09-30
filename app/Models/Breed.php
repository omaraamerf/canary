<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Lang;

class Breed extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'slug', 'description', 'active'];

    protected function casts(): array
    {
        return ['active' => 'boolean'];
    }

    public function getLocalizedNameAttribute(): string
    {
        $key = "breeds.{$this->slug}.name";

        return Lang::hasForLocale($key, app()->getLocale()) ? __($key) : $this->name;
    }

    public function getLocalizedDescriptionAttribute(): ?string
    {
        $key = "breeds.{$this->slug}.description";

        return Lang::hasForLocale($key, app()->getLocale()) ? __($key) : $this->description;
    }

    public function birds()
    {
        return $this->hasMany(Bird::class);
    }
}
