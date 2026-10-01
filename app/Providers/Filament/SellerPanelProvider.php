<?php

namespace App\Providers\Filament;

use App\Filament\Auth\SiteLogin;
use App\Filament\Seller\Pages\EditProfile;
use App\Filament\Seller\Resources\Birds\BirdResource;
use App\Filament\Support\SiteTheme;
use App\Http\Middleware\SetLocale;
use Filament\Actions\Action;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\NavigationItem;
use Filament\Panel;
use Filament\PanelProvider;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class SellerPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return SiteTheme::apply($panel, fn (): string => __('ui.seller.brand'))
            ->id('seller')
            ->path('seller')
            ->login(SiteLogin::class)
            ->profile(EditProfile::class)
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
            ->discoverResources(in: app_path('Filament/Seller/Resources'), for: 'App\Filament\Seller\Resources')
            // The dashboard (App\Filament\Seller\Pages\Dashboard) and its widgets are discovered.
            ->discoverPages(in: app_path('Filament/Seller/Pages'), for: 'App\Filament\Seller\Pages')
            ->navigationItems([
                NavigationItem::make(fn (): string => __('ui.birds.add'))
                    ->icon('lucide-circle-plus')
                    ->url(fn (): string => BirdResource::getUrl('create'))
                    ->isActiveWhen(fn (): bool => request()->routeIs('filament.seller.resources.birds.create'))
                    ->sort(2),
                NavigationItem::make(fn (): string => __('ui.account.profile'))
                    ->icon('lucide-circle-user-round')
                    ->url(fn (): string => route('filament.seller.auth.profile'))
                    ->isActiveWhen(fn (): bool => request()->routeIs('filament.seller.auth.profile'))
                    ->sort(90),
            ])
            ->discoverWidgets(in: app_path('Filament/Seller/Widgets'), for: 'App\Filament\Seller\Widgets')
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
