<?php

namespace App\Providers\Filament;

use App\Filament\Auth\SiteLogin;
use App\Filament\Support\SiteTheme;
use App\Http\Middleware\SetLocale;
use Filament\Actions\Action;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Panel;
use Filament\PanelProvider;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return SiteTheme::apply($panel, fn (): string => __('ui.admin.brand'))
            ->default()
            ->id('admin')
            ->path('admin')
            ->login(SiteLogin::class)
            ->profile()
            ->userMenuItems([
                Action::make('view-site')
                    ->label(fn (): string => __('عرض الموقع'))
                    ->icon('lucide-external-link')
                    ->url(fn (): string => route('home'), shouldOpenInNewTab: true),
                Action::make('switch-language')
                    ->label(fn (): string => app()->isLocale('ar') ? 'English' : 'العربية')
                    ->icon('lucide-languages')
                    ->url(fn (): string => route('locale.switch', app()->isLocale('ar') ? 'en' : 'ar')),
            ])
            // Sidebar groups come from App\Filament\Navigation\AdminGroup on each resource, ordered
            // by the enum. They are not registered here: that would fix their labels at boot, before
            // the request's language is set.
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\Filament\Resources')
            // The dashboard (App\Filament\Pages\Dashboard) and its widgets are discovered.
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\Filament\Pages')
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\Filament\Widgets')
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->middleware([
                SetLocale::class,
            ], isPersistent: true)
            ->authMiddleware([
                Authenticate::class,
            ]);
    }
}
