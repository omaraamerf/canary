<?php

namespace App\Filament\Support;

use App\Models\User;
use Filament\AvatarProviders\Contracts\AvatarProvider;
use Filament\Facades\Filament;
use Illuminate\Database\Eloquent\Model;

/**
 * The site's initial-on-yellow avatar, drawn locally. Filament's default fetches avatars
 * from ui-avatars.com, which would send every user's name to a third party.
 */
class InitialAvatarProvider implements AvatarProvider
{
    public function get(Model $record): string
    {
        $initial = $record instanceof User
            ? $record->initial
            : mb_strtoupper(mb_substr(trim(Filament::getNameForDefaultAvatar($record)), 0, 1));

        $svg = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 64 64">'
            .'<rect width="64" height="64" fill="#f2c94c"/>'
            .'<text x="32" y="32" dy=".35em" text-anchor="middle" font-family="Readex Pro Variable, Tahoma, sans-serif" font-size="30" font-weight="700" fill="#20251e">'
            .e($initial ?: '?')
            .'</text></svg>';

        return 'data:image/svg+xml;base64,'.base64_encode($svg);
    }
}
