<!doctype html>
<html lang="{{ app()->getLocale() }}" dir="{{ __('ui.direction') }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', __('ui.brand.name')) | {{ __('ui.layout.title') }}</title>
    <meta name="description" content="@yield('meta_description', __('ui.layout.description'))">
    @foreach(['arabic', 'latin'] as $subset)
        <link rel="preload" href="{{ Vite::asset("node_modules/@fontsource-variable/readex-pro/files/readex-pro-{$subset}-wght-normal.woff2") }}" as="font" type="font/woff2" crossorigin>
    @endforeach
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-bg text-fg antialiased">
    @php
        $mainLinks = array_filter([
            ['route' => 'home', 'active' => 'home', 'label' => __('ui.nav.home'), 'icon' => 'house'],
            ['route' => 'birds.index', 'active' => 'birds.*', 'label' => __('ui.nav.birds'), 'icon' => 'bird'],
            $guideEnabled ? ['route' => 'guide.index', 'active' => 'guide.*', 'label' => __('ui.nav.guide'), 'icon' => 'book-open'] : null,
            $communityEnabled ? ['route' => 'community.index', 'active' => 'community.*', 'label' => __('ui.nav.community'), 'icon' => 'messages-square'] : null,
            ['route' => 'about', 'active' => 'about', 'label' => __('ui.nav.about'), 'icon' => 'info'],
        ]);
        $currentUser = auth()->user();
    @endphp
    <header class="site-header">
        <div class="topbar">
            <div class="container topbar-inner">
                <div class="topbar-group">
                    <details class="region-switcher">
                        <summary><x-lucide-map-pin /><span>{{ $marketplaceLocation->label() }}</span><x-lucide-chevron-down class="chevron" /></summary>
                        <form method="post" action="{{ route('region.select') }}" class="region-switcher-panel">
                            @csrf
                            <strong>{{ __('ui.location.where_title') }}</strong>
                            <x-location-picker :show-optional="false" :use-old="false" :country="$marketplaceLocation->countryId()" :region="$marketplaceLocation->regionId()" :region-placeholder="__('ui.location.all_country_regions')" />
                            <div class="region-switcher-actions">
                                <button type="submit" class="btn btn-primary">{{ __('ui.location.apply') }}</button>
                                <button type="submit" class="btn btn-outline" name="scope" value="all" formnovalidate>{{ __('ui.layout.all_regions') }}</button>
                            </div>
                        </form>
                    </details>
                    <a class="topbar-link topbar-optional" href="{{ route('orders.track') }}"><x-lucide-package-search />{{ __('ui.nav.track') }}</a>
                </div>
                <div class="topbar-group">
                    <a class="topbar-link topbar-optional" href="{{ route('start-selling') }}"><x-lucide-store />{{ __('ui.nav.sell') }}</a>
                    @guest<a class="topbar-link topbar-optional" href="{{ route('filament.seller.auth.login') }}">{{ __('ui.nav.seller_login') }}</a>@endguest
                    <a class="topbar-link topbar-lang" href="{{ route('locale.switch', app()->isLocale('ar') ? 'en' : 'ar') }}" aria-label="{{ __('ui.language.switch') }}" hreflang="{{ app()->isLocale('ar') ? 'en' : 'ar' }}"><x-lucide-languages />{{ app()->isLocale('ar') ? __('ui.language.english') : __('ui.language.arabic') }}</a>
                </div>
            </div>
        </div>

        <div class="container header-main">
            <a href="{{ route('home') }}" class="brand" aria-label="{{ __('ui.nav.home') }}">
                <span class="brand-mark"><x-lucide-bird /></span>
                <span><strong>{{ __('ui.brand.name') }}</strong><small>{{ __('ui.brand.tagline') }}</small></span>
            </a>

            <nav class="main-nav" aria-label="{{ __('ui.layout.main_navigation') }}">
                @foreach($mainLinks as $link)
                    <a @class(['nav-link', 'active' => request()->routeIs($link['active'])]) href="{{ route($link['route']) }}" @if(request()->routeIs($link['active'])) aria-current="page" @endif>{{ $link['label'] }}</a>
                @endforeach
            </nav>

            <div class="header-actions">
                @if($currentUser)
                    <details class="account-menu">
                        <summary aria-label="{{ $currentUser->public_name }}">
                            <x-avatar :user="$currentUser" class="account-avatar" />
                            <span class="account-name">{{ $currentUser->public_name }}</span>
                            <x-lucide-chevron-down class="chevron" />
                        </summary>
                        <div class="account-panel">
                            <div class="account-panel-head"><strong>{{ $currentUser->public_name }}</strong><small dir="ltr">{{ $currentUser->email }}</small></div>
                            <a href="{{ route('account.show') }}"><x-lucide-user-round />{{ __('ui.account.my_account') }}</a>
                            <a href="{{ route('account.edit') }}"><x-lucide-user-round-pen />{{ __('ui.account.edit') }}</a>
                            @if($currentUser->isSeller())<a href="{{ url('/seller') }}"><x-lucide-layout-dashboard />{{ __('ui.nav.seller_panel') }}</a>@endif
                            @if($currentUser->isAdmin())<a href="{{ url('/admin') }}"><x-lucide-shield-check />{{ __('ui.nav.admin_panel') }}</a>@endif
                            @if($communityEnabled)<a href="{{ route('community.create') }}"><x-lucide-message-circle-plus />{{ __('ui.community.new_post') }}</a>@endif
                            <form method="post" action="{{ route('logout') }}">@csrf<button type="submit"><x-lucide-log-out />{{ __('ui.nav.logout') }}</button></form>
                        </div>
                    </details>
                @else
                    <a href="{{ route('login') }}" class="header-login"><x-lucide-circle-user-round /><span>{{ __('ui.nav.login') }}</span></a>
                @endif
                <a href="{{ route('birds.index') }}" class="btn btn-dark header-cta"><x-lucide-search /><span>{{ __('ui.layout.find_bird') }}</span></a>
                <button type="button" class="icon-btn menu-toggle" data-menu-toggle aria-label="{{ __('ui.layout.open_menu') }}" aria-controls="mobile-navigation" aria-expanded="false"><x-lucide-menu /></button>
            </div>
        </div>

        <nav id="mobile-navigation" class="mobile-menu hidden" data-mobile-menu aria-label="{{ __('ui.layout.mobile_navigation') }}">
            @foreach($mainLinks as $link)
                <a @class(['active' => request()->routeIs($link['active'])]) href="{{ route($link['route']) }}"><x-dynamic-component :component="'lucide-'.$link['icon']" />{{ $link['label'] }}</a>
            @endforeach
            <span class="mobile-menu-divider"></span>
            <a href="{{ route('orders.track') }}"><x-lucide-package-search />{{ __('ui.nav.track') }}</a>
            <a href="{{ route('start-selling') }}"><x-lucide-store />{{ __('ui.nav.sell') }}</a>
            <a href="{{ route('policy') }}"><x-lucide-file-text />{{ __('ui.nav.policy') }}</a>
            <span class="mobile-menu-divider"></span>
            @if($currentUser)
                <a href="{{ route('account.show') }}"><x-lucide-user-round />{{ __('ui.account.my_account') }}</a>
                <form method="post" action="{{ route('logout') }}">@csrf<button type="submit"><x-lucide-log-out />{{ __('ui.nav.logout') }} ({{ $currentUser->public_name }})</button></form>
            @else
                <div class="mobile-menu-auth">
                    <a class="btn btn-primary" href="{{ route('login') }}">{{ __('ui.nav.login') }}</a>
                    <a class="btn btn-outline" href="{{ route('register') }}">{{ __('ui.nav.register') }}</a>
                </div>
                <a href="{{ route('filament.seller.auth.login') }}"><x-lucide-store />{{ __('ui.nav.seller_login') }}</a>
            @endif
        </nav>
    </header>

    @unless($regionChosen)
        <div class="region-gate"><div class="region-gate-panel">
            <span class="brand-mark"><x-lucide-map-pin /></span>
            <h2>{{ __('ui.layout.choose_region') }}</h2>
            <p>{{ __('ui.layout.region_help') }}</p>
            <form method="post" action="{{ route('region.select') }}" class="region-gate-form">
                @csrf
                <x-location-picker :show-optional="false" :use-old="false" :region-placeholder="__('ui.location.all_country_regions')" />
                <button class="btn btn-primary w-full" type="submit">{{ __('ui.location.apply') }}</button>
                <div class="region-or"><span>{{ __('ui.layout.or') }}</span></div>
                <button class="btn btn-outline w-full" name="scope" value="all" type="submit">{{ __('ui.layout.show_all_regions') }}</button>
            </form>
        </div></div>
    @endunless

    <main>@yield('content')</main>

    <footer class="site-footer">
        <div class="container footer-grid">
            <div><a href="{{ route('home') }}" class="brand brand-light"><span class="brand-mark"><x-lucide-bird /></span><span><strong>{{ __('ui.brand.name') }}</strong><small>{{ __('ui.brand.promise') }}</small></span></a></div>
            <p>{{ __('ui.layout.footer_about') }}</p>
            <div class="footer-links"><a href="{{ route('birds.index') }}">{{ __('ui.birds.plural') }}</a>@if($guideEnabled)<a href="{{ route('guide.index') }}">{{ __('ui.nav.guide') }}</a>@endif @if($communityEnabled)<a href="{{ route('community.index') }}">{{ __('ui.nav.community') }}</a>@endif<a href="{{ route('orders.track') }}">{{ __('ui.nav.track') }}</a></div>
            <div class="footer-links"><a href="{{ route('about') }}">{{ __('ui.nav.about') }}</a><a href="{{ route('policy') }}">{{ __('ui.nav.policy') }}</a><a href="{{ route('start-selling') }}">{{ __('ui.nav.sell') }}</a><a href="{{ route('filament.seller.auth.login') }}">{{ __('ui.nav.seller_login') }}</a></div>
        </div>
        <div class="container footer-bottom">© {{ date('Y') }} {{ __('ui.brand.name') }}. {{ __('ui.layout.rights') }}</div>
    </footer>
    @include('partials.location-data')
</body>
</html>
