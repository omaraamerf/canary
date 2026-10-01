<?php

namespace App\Providers\Filament;

use App\Filament\Seller\Pages\EditProfile;
use App\Filament\Seller\Resources\Birds\BirdResource;
use App\Http\Middleware\SetLocale;
use Filament\Actions\Action;
use Filament\FontProviders\LocalFontProvider;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages\Dashboard;
use Filament\Navigation\NavigationItem;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Widgets\AccountWidget;
use Filament\View\PanelsRenderHook;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Support\Facades\Vite;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class SellerPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('seller')
            ->path('seller')
            ->login()
            ->profile(EditProfile::class)
            ->brandName(fn (): string => __('ui.seller.brand'))
            ->userMenuItems([
                Action::make('switch-language')
                    ->label(fn (): string => app()->isLocale('ar') ? 'English' : 'العربية')
                    ->icon('heroicon-o-language')
                    ->url(fn (): string => route('locale.switch', app()->isLocale('ar') ? 'en' : 'ar')),
            ])
            ->renderHook(PanelsRenderHook::AUTH_LOGIN_FORM_AFTER, fn (): string => view('filament.locale-switcher')->render())
            ->colors([
                'primary' => Color::Amber,
            ])
            ->font('Readex Pro Variable', url: fn (): string => Vite::asset('resources/css/fonts.css'), provider: LocalFontProvider::class)
            ->discoverResources(in: app_path('Filament/Seller/Resources'), for: 'App\Filament\Seller\Resources')
            ->discoverPages(in: app_path('Filament/Seller/Pages'), for: 'App\Filament\Seller\Pages')
            ->pages([
                Dashboard::class,
            ])
            ->navigationItems([
                NavigationItem::make(fn (): string => __('ui.birds.add'))
                    ->icon('heroicon-o-plus-circle')
                    ->url(fn (): string => BirdResource::getUrl('create'))
                    ->isActiveWhen(fn (): bool => request()->routeIs('filament.seller.resources.birds.create'))
                    ->sort(20),
                NavigationItem::make(fn (): string => __('ui.account.profile'))
                    ->icon('heroicon-o-user-circle')
                    ->url(fn (): string => route('filament.seller.auth.profile'))
                    ->isActiveWhen(fn (): bool => request()->routeIs('filament.seller.auth.profile'))
                    ->sort(90),
            ])
            ->discoverWidgets(in: app_path('Filament/Seller/Widgets'), for: 'App\Filament\Seller\Widgets')
            ->widgets([
                AccountWidget::class,
            ])
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
