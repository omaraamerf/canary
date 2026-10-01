<?php

namespace App\Support;

/**
 * Turns locally written phone numbers (e.g. "0999 000 111") into the international
 * digits WhatsApp links need (e.g. "963999000111").
 */
final class PhoneNumber
{
    /** Dialling codes for the marketplace countries, keyed by ISO 3166-1 alpha-2 code. */
    private const DIAL_CODES = [
        'AE' => '971', 'BH' => '973', 'DZ' => '213', 'EG' => '20', 'IQ' => '964', 'JO' => '962',
        'KW' => '965', 'LB' => '961', 'LY' => '218', 'MA' => '212', 'OM' => '968', 'PS' => '970',
        'QA' => '974', 'SA' => '966', 'SD' => '249', 'SY' => '963', 'TN' => '216', 'YE' => '967',
    ];

    public static function international(?string $number, ?string $countryCode): ?string
    {
        $digits = preg_replace('/\D+/', '', (string) $number);
        $dial = self::DIAL_CODES[strtoupper((string) $countryCode)] ?? null;

        if (str_starts_with($digits, '00')) {
            $digits = substr($digits, 2);
        } elseif ($dial && str_starts_with($digits, '0')) {
            $digits = $dial.substr($digits, 1);
        } elseif ($dial && strlen($digits) <= 10 && ! str_starts_with($digits, $dial)) {
            $digits = $dial.$digits;
        }

        // A leading 0 left over means a local number whose country is unknown.
        return strlen($digits) >= 10 && strlen($digits) <= 15 && ! str_starts_with($digits, '0') ? $digits : null;
    }

    public static function whatsappUrl(?string $number, ?string $countryCode, string $message = ''): ?string
    {
        $digits = self::international($number, $countryCode);

        if ($digits === null) {
            return null;
        }

        return 'https://wa.me/'.$digits.($message !== '' ? '?text='.rawurlencode($message) : '');
    }
}
