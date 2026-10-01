<?php

namespace App\Filament\Auth;

use Filament\Auth\Pages\Login;

/**
 * Both panels sign in through the site's own login page, so there is one way in for
 * members, sellers and admins. The panel keeps this route (Filament redirects guests
 * here); it only forwards to /login, where the intended panel URL is still in the session.
 */
class SiteLogin extends Login
{
    public function mount(): void
    {
        session()->reflash();

        $this->redirect(route('login'));
    }
}
