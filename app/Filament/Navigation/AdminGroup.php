<?php

namespace App\Filament\Navigation;

use Filament\Support\Contracts\HasLabel;

/** Sidebar groups of the admin panel, in display order. */
enum AdminGroup implements HasLabel
{
    case Market;
    case Content;
    case Setup;

    public function getLabel(): string
    {
        return match ($this) {
            self::Market => __('السوق'),
            self::Content => __('المحتوى'),
            self::Setup => __('الإعدادات'),
        };
    }
}
