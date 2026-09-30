<?php

namespace App\Enums;

enum PostCategory: string
{
    case Health = 'health';
    case Treatment = 'treatment';
    case Nutrition = 'nutrition';
    case Breeding = 'breeding';
    case General = 'general';

    public function label(): string
    {
        return __('ui.post_category.'.$this->value);
    }

    public static function options(): array
    {
        return collect(self::cases())
            ->mapWithKeys(fn (self $category): array => [$category->value => $category->label()])
            ->all();
    }
}
