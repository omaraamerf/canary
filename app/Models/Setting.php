<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    /** Container key for all values, bound as scoped in AppServiceProvider: one query per request or queued job. */
    public const VALUES = 'settings.values';

    protected $fillable = ['key', 'value'];

    protected static function booted(): void
    {
        // A change must be visible later in the same request.
        static::saved(fn () => app()->forgetInstance(self::VALUES));
        static::deleted(fn () => app()->forgetInstance(self::VALUES));
    }

    public static function boolean(string $key, bool $default = false): bool
    {
        $value = app(self::VALUES)[$key] ?? null;

        return $value === null ? $default : filter_var($value, FILTER_VALIDATE_BOOL);
    }

    public static function put(string $key, mixed $value): void
    {
        static::query()->updateOrCreate(['key' => $key], ['value' => (string) $value]);
    }
}
