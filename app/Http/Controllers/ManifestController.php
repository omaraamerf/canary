<?php

namespace App\Http\Controllers;

use App\Enums\SettingKey;
use App\Models\Setting;
use Illuminate\Http\JsonResponse;

/** The web app manifest, so the site can be installed on a phone like an app. */
class ManifestController extends Controller
{
    public function __invoke(): JsonResponse
    {
        $shortcuts = array_values(array_filter([
            ['name' => __('ui.nav.birds'), 'url' => route('birds.index', absolute: false)],
            Setting::boolean('guide_enabled', true) ? ['name' => __('ui.nav.guide'), 'url' => route('guide.index', absolute: false)] : null,
            Setting::boolean(SettingKey::CommunityEnabled->value, true) ? ['name' => __('ui.nav.community'), 'url' => route('community.index', absolute: false)] : null,
            ['name' => __('ui.favorites.title'), 'url' => route('favorites.index', absolute: false)],
        ]));

        return response()->json([
            'name' => __('ui.brand.name').' — '.__('ui.brand.tagline'),
            'short_name' => __('ui.brand.name'),
            'description' => __('ui.layout.description'),
            'lang' => app()->getLocale(),
            'dir' => __('ui.direction'),
            'id' => '/',
            'start_url' => '/',
            'scope' => '/',
            'display' => 'standalone',
            'background_color' => '#f6f7f2',
            'theme_color' => '#ffffff',
            'icons' => [
                ['src' => '/images/icons/icon.svg', 'sizes' => 'any', 'type' => 'image/svg+xml'],
                ['src' => '/images/icons/icon-192.png', 'sizes' => '192x192', 'type' => 'image/png'],
                ['src' => '/images/icons/icon-512.png', 'sizes' => '512x512', 'type' => 'image/png'],
                ['src' => '/images/icons/icon-maskable-512.png', 'sizes' => '512x512', 'type' => 'image/png', 'purpose' => 'maskable'],
            ],
            'shortcuts' => $shortcuts,
        ], 200, ['Content-Type' => 'application/manifest+json'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }
}
