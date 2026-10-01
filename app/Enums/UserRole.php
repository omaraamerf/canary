<?php

namespace App\Enums;

enum UserRole: string
{
    case Admin = 'admin';
    case Seller = 'seller';
    case Member = 'member';

    public function label(): string
    {
        return match ($this) {
            self::Admin => __('مدير'),
            self::Seller => __('بائع'),
            self::Member => __('عضو'),
        };
    }

    /** Built-in roles keep their names; the rest of the code checks them by name. */
    public static function isBuiltIn(string $name): bool
    {
        return self::tryFrom($name) !== null;
    }

    public static function labelFor(string $name): string
    {
        return self::tryFrom($name)?->label() ?? $name;
    }
}
