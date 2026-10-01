<?php

namespace App\Filament\Support;

use Closure;
use Filament\Enums\ThemeMode;
use Filament\FontProviders\LocalFontProvider;
use Filament\Panel;
use Filament\View\PanelsRenderHook;
use Illuminate\Support\Facades\Vite;

/**
 * The site's identity applied to a Filament panel: brand ramps, font, logo and a
 * light default. Filament's own stylesheet stays; resources/css/panels.css adds the
 * few brand touches on top of it.
 */
class SiteTheme
{
    /*
     * The site's ramps (resources/css/tokens.css), with the middle steps moved to the lightness
     * Filament's components expect. Filament gives a button white text on shade 600 unless a
     * dark shade reads on it; here 600 is light enough for dark text, so primary buttons come
     * out as on the site: brand yellow (400) with dark text. Gray follows Filament's zinc
     * lightness so its secondary text keeps 4.5:1, tinted with the site's sage.
     * Lightness is a decimal (0.85, not 85%): that is the form Filament's contrast maths parses.
     */
    public const CANARY = [
        50 => 'oklch(0.985 0.018 90.5)', 100 => 'oklch(0.965 0.035 90.5)', 200 => 'oklch(0.935 0.07 90.5)',
        300 => 'oklch(0.895 0.11 90.5)', 400 => 'oklch(0.85 0.146 90.5)', 500 => 'oklch(0.78 0.15 88)',
        600 => 'oklch(0.7 0.14 85)', 700 => 'oklch(0.56 0.115 80)', 800 => 'oklch(0.41 0.08 75)',
        900 => 'oklch(0.33 0.06 72)', 950 => 'oklch(0.25 0.04 70)',
    ];

    public const LEAF = [
        50 => 'oklch(0.985 0.008 156)', 100 => 'oklch(0.962 0.015 156)', 200 => 'oklch(0.92 0.03 156)',
        300 => 'oklch(0.865 0.048 156)', 400 => 'oklch(0.78 0.065 156)', 500 => 'oklch(0.68 0.07 156)',
        600 => 'oklch(0.56 0.072 156)', 700 => 'oklch(0.441 0.067 156)', 800 => 'oklch(0.37 0.055 156)',
        900 => 'oklch(0.3 0.04 156)', 950 => 'oklch(0.22 0.025 156)',
    ];

    public const SAGE = [
        50 => 'oklch(0.982 0.005 115.7)', 100 => 'oklch(0.964 0.006 124.4)', 200 => 'oklch(0.92 0.009 123.5)',
        300 => 'oklch(0.87 0.011 124.8)', 400 => 'oklch(0.705 0.014 126.1)', 500 => 'oklch(0.55 0.015 127.5)',
        600 => 'oklch(0.445 0.016 128.8)', 700 => 'oklch(0.37 0.015 131.4)', 800 => 'oklch(0.275 0.013 134.1)',
        900 => 'oklch(0.21 0.011 136.7)', 950 => 'oklch(0.145 0.009 132.7)',
    ];

    public static function apply(Panel $panel, Closure $brandName): Panel
    {
        return $panel
            ->brandName($brandName)
            ->brandLogo(fn () => view('filament.brand', ['name' => $brandName()]))
            ->brandLogoHeight('2.25rem')
            ->favicon('/images/icons/icon.svg')
            ->colors([
                'primary' => self::CANARY,
                'gray' => self::SAGE,
                'success' => self::LEAF,
            ])
            ->defaultThemeMode(ThemeMode::Light)
            ->defaultAvatarProvider(InitialAvatarProvider::class)
            ->font('Readex Pro Variable', url: fn (): string => Vite::asset('resources/css/fonts.css'), provider: LocalFontProvider::class)
            ->renderHook(PanelsRenderHook::STYLES_AFTER, fn (): string => Vite::withEntryPoints(['resources/css/panels.css'])->toHtml());
    }
}
