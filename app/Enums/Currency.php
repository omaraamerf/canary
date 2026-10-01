<?php

namespace App\Enums;

/**
 * Currencies a seller can price a listing in.
 */
enum Currency: string
{
    case USD = 'USD';
    case SYP = 'SYP';

    public function label(): string
    {
        return __('ui.currency_names.'.$this->value);
    }

    /**
     * Short display label for any stored code, including legacy ones such as SAR.
     */
    public static function labelFor(?string $code): string
    {
        if (blank($code)) {
            return '';
        }

        $key = 'ui.currencies.'.$code;

        return __($key) === $key ? $code : __($key);
    }

    /**
     * @return array<string, string>
     */
    public static function options(): array
    {
        return collect(self::cases())
            ->mapWithKeys(fn (self $currency): array => [$currency->value => $currency->label()])
            ->all();
    }
}
