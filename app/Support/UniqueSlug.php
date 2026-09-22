<?php

namespace App\Support;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class UniqueSlug
{
    /**
     * @param  class-string<Model>  $model
     */
    public function for(string $model, string $value, string $fallback, ?int $exceptId = null, bool $withTrashed = false): string
    {
        $base = Str::slug($value) ?: $fallback;
        $slug = $base;
        $counter = 2;

        do {
            $query = $model::query();

            if ($withTrashed) {
                $query->withTrashed();
            }

            $exists = $query->where('slug', $slug)
                ->when($exceptId, fn ($query) => $query->whereKeyNot($exceptId))
                ->exists();

            if ($exists) {
                $slug = $base.'-'.$counter++;
            }
        } while ($exists);

        return $slug;
    }
}
