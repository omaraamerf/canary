<!doctype html>
<html lang="{{ app()->getLocale() }}" dir="{{ __('ui.direction') }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', __('ui.brand.name')) | {{ __('ui.layout.title') }}</title>
    <meta name="description" content="@yield('meta_description', __('ui.layout.description'))">
    @include('partials.theme-script')
    @foreach(['arabic', 'latin'] as $subset)
        <link rel="preload" href="{{ Vite::asset("node_modules/@fontsource-variable/readex-pro/files/readex-pro-{$subset}-wght-normal.woff2") }}" as="font" type="font/woff2" crossorigin>
    @endforeach
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-bg text-fg antialiased">
    @php
        $mainLinks = array_filter([
            ['route' => 'birds.index', 'active' => 'birds.*', 'label' => __('ui.nav.birds')],
            $guideEnabled ? ['route' => 'guide.index', 'active' => 'guide.*', 'label' => __('ui.nav.guide')] : null,
            $communityEnabled ? ['route' => 'community.index', 'active' => 'community.*', 'label' => __('ui.nav.community')] : null,
            ['route' => 'start-selling', 'active' => 'start-selling', 'label' => __('ui.nav.sell')],
        ]);
        $currentUser = auth()->user();
        $bottomLinks = array_filter([
            ['route' => 'home', 'active' => 'home', 'label' => __('ui.nav.home'), 'icon' => 'house'],
            ['route' => 'birds.index', 'active' => 'birds.*', 'label' => __('ui.nav.birds_short'), 'icon' => 'bird'],
            $guideEnabled ? ['route' => 'guide.index', 'active' => 'guide.*', 'label' => __('ui.nav.guide_short'), 'icon' => 'book-open'] : null,
            $communityEnabled ? ['route' => 'community.index', 'active' => 'community.*', 'label' => __('ui.nav.community_short'), 'icon' => 'messages-square'] : null,
            $currentUser
                ? ['route' => 'account.show', 'active' => 'account.*', 'label' => __('ui.account.my_account'), 'icon' => 'user-round']
                : ['route' => 'login', 'active' => 'login', 'label' => __('ui.nav.login'), 'icon' => 'log-in'],
        ]);
        $otherLocale = app()->isLocale('ar') ? 'en' : 'ar';
    @endphp
    <a class="skip-link" href="#main">{{ __('ui.layout.skip_to_content') }}</a>

    <header class="site-header">
        <div class="container header-bar">
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
                <x-ui.chip icon="map-pin" icon-end="chevron-down" class="chip-location" data-sheet-open="region-sheet" aria-haspopup="dialog" :aria-label="__('ui.location.where_title').': '.$marketplaceLocation->label()">{{ $marketplaceLocation->label() }}</x-ui.chip>
                <div class="header-tools">
                    <a class="icon-btn header-lang" href="{{ route('locale.switch', $otherLocale) }}" hreflang="{{ $otherLocale }}" lang="{{ $otherLocale }}" aria-label="{{ __('ui.language.switch') }}">{{ $otherLocale === 'en' ? __('ui.language.english') : __('ui.language.arabic') }}</a>
                    <button type="button" class="icon-btn theme-toggle" data-theme-toggle aria-pressed="false" aria-label="{{ __('ui.theme.dark_mode') }}" title="{{ __('ui.theme.dark_mode') }}"><x-lucide-moon class="icon-dark" /><x-lucide-sun class="icon-light" /></button>
                    @if($currentUser)
                        <button type="button" class="account-trigger" popovertarget="account-menu" aria-label="{{ $currentUser->public_name }}">
                            <x-avatar :user="$currentUser" />
                            <span class="account-name">{{ $currentUser->public_name }}</span>
                            <x-lucide-chevron-down class="chevron" />
                        </button>
                        <div id="account-menu" class="menu-popover" popover>
                            @include('partials.account-links', ['user' => $currentUser])
                        </div>
                    @else
                        <x-ui.button variant="ghost" icon="circle-user-round" :href="route('login')">{{ __('ui.nav.login') }}</x-ui.button>
                    @endif
                </div>
                <x-ui.icon-button icon="menu" :label="__('ui.layout.open_menu')" class="menu-toggle" data-sheet-open="menu-sheet" aria-haspopup="dialog" aria-expanded="false" />
            </div>
        </div>
    </header>

    <x-ui.sheet id="region-sheet" :title="__('ui.location.where_title')">
        <form method="post" action="{{ route('region.select') }}" class="region-form">
            @csrf
            <x-location-picker :show-optional="false" :use-old="false" :country="$marketplaceLocation->countryId()" :region="$marketplaceLocation->regionId()" :region-placeholder="__('ui.location.all_country_regions')" />
            <div class="region-form-actions">
                <x-ui.button type="submit" block>{{ __('ui.location.apply') }}</x-ui.button>
                <x-ui.button type="submit" variant="outline" block name="scope" value="all" formnovalidate>{{ __('ui.layout.all_regions') }}</x-ui.button>
            </div>
        </form>
    </x-ui.sheet>

    <x-ui.sheet id="menu-sheet" :title="__('ui.layout.menu')">
        @if($currentUser)
            <div class="menu-sheet-section">@include('partials.account-links', ['user' => $currentUser])</div>
        @endif
        <nav class="menu-sheet-section" aria-label="{{ __('ui.layout.mobile_navigation') }}">
            <a @class(['menu-item', 'is-active' => request()->routeIs('start-selling')]) href="{{ route('start-selling') }}"><x-lucide-store />{{ __('ui.nav.sell') }}</a>
            <a @class(['menu-item', 'is-active' => request()->routeIs('orders.track*')]) href="{{ route('orders.track') }}"><x-lucide-package-search />{{ __('ui.nav.track') }}</a>
            <a @class(['menu-item', 'is-active' => request()->routeIs('about')]) href="{{ route('about') }}"><x-lucide-info />{{ __('ui.nav.about') }}</a>
            <a @class(['menu-item', 'is-active' => request()->routeIs('policy')]) href="{{ route('policy') }}"><x-lucide-file-text />{{ __('ui.nav.policy') }}</a>
            @guest<a class="menu-item" href="{{ route('filament.seller.auth.login') }}"><x-lucide-layout-dashboard />{{ __('ui.nav.seller_login') }}</a>@endguest
        </nav>
        <div class="menu-sheet-section">
            <div class="menu-sheet-prefs">
                <x-ui.button variant="outline" icon="languages" :href="route('locale.switch', $otherLocale)" hreflang="{{ $otherLocale }}" lang="{{ $otherLocale }}">{{ $otherLocale === 'en' ? __('ui.language.english') : __('ui.language.arabic') }}</x-ui.button>
                <button type="button" class="btn btn-outline theme-toggle" data-theme-toggle aria-pressed="false"><x-lucide-moon class="icon-dark" /><x-lucide-sun class="icon-light" />{{ __('ui.theme.dark_mode') }}</button>
            </div>
            @guest
                <div class="menu-sheet-auth">
                    <x-ui.button :href="route('login')">{{ __('ui.nav.login') }}</x-ui.button>
                    <x-ui.button variant="outline" :href="route('register')">{{ __('ui.nav.register') }}</x-ui.button>
                </div>
            @endguest
        </div>
    </x-ui.sheet>

    @unless($regionChosen)
        <div class="region-gate"><div class="region-gate-panel" role="dialog" aria-modal="true" aria-labelledby="region-gate-title">
            <span class="brand-mark"><x-lucide-map-pin /></span>
            <h2 id="region-gate-title">{{ __('ui.layout.choose_region') }}</h2>
            <p>{{ __('ui.layout.region_help') }}</p>
            <form method="post" action="{{ route('region.select') }}" class="region-form">
                @csrf
                <x-location-picker :show-optional="false" :use-old="false" :region-placeholder="__('ui.location.all_country_regions')" />
                <x-ui.button type="submit" block>{{ __('ui.location.apply') }}</x-ui.button>
                <div class="region-or"><span>{{ __('ui.layout.or') }}</span></div>
                <x-ui.button type="submit" variant="outline" block name="scope" value="all">{{ __('ui.layout.show_all_regions') }}</x-ui.button>
            </form>
        </div></div>
    @endunless

    @include('partials.toasts')

    <main id="main">@yield('content')</main>

    <footer class="site-footer">
        <div class="container footer-grid">
            <div class="footer-brand">
                <a href="{{ route('home') }}" class="brand brand-light"><span class="brand-mark"><x-lucide-bird /></span><span><strong>{{ __('ui.brand.name') }}</strong><small>{{ __('ui.brand.promise') }}</small></span></a>
                <p>{{ __('ui.layout.footer_about') }}</p>
            </div>
            <div class="footer-col">
                <h2>{{ __('ui.layout.footer_market') }}</h2>
                <ul>
                    <li><a href="{{ route('birds.index') }}">{{ __('ui.nav.birds') }}</a></li>
                    <li><a href="{{ route('start-selling') }}">{{ __('ui.nav.sell') }}</a></li>
                    <li><a href="{{ route('orders.track') }}">{{ __('ui.nav.track') }}</a></li>
                    <li><a href="{{ route('filament.seller.auth.login') }}">{{ __('ui.nav.seller_login') }}</a></li>
                </ul>
            </div>
            @if($guideEnabled || $communityEnabled)
                <div class="footer-col">
                    <h2>{{ __('ui.layout.footer_learn') }}</h2>
                    <ul>
                        @if($guideEnabled)<li><a href="{{ route('guide.index') }}">{{ __('ui.nav.guide') }}</a></li>@endif
                        @if($communityEnabled)<li><a href="{{ route('community.index') }}">{{ __('ui.nav.community') }}</a></li>@endif
                    </ul>
                </div>
            @endif
            <div class="footer-col">
                <h2>{{ __('ui.layout.footer_company') }}</h2>
                <ul>
                    <li><a href="{{ route('about') }}">{{ __('ui.nav.about') }}</a></li>
                    <li><a href="{{ route('policy') }}">{{ __('ui.nav.policy') }}</a></li>
                </ul>
            </div>
        </div>
        <div class="container footer-bottom">
            <span>© {{ date('Y') }} {{ __('ui.brand.name') }}. {{ __('ui.layout.rights') }}</span>
            <div class="footer-prefs">
                <x-ui.button variant="outline" size="sm" icon="languages" :href="route('locale.switch', $otherLocale)" hreflang="{{ $otherLocale }}" lang="{{ $otherLocale }}">{{ $otherLocale === 'en' ? __('ui.language.english') : __('ui.language.arabic') }}</x-ui.button>
                <button type="button" class="icon-btn theme-toggle" data-theme-toggle aria-pressed="false" aria-label="{{ __('ui.theme.dark_mode') }}" title="{{ __('ui.theme.dark_mode') }}"><x-lucide-moon class="icon-dark" /><x-lucide-sun class="icon-light" /></button>
            </div>
        </div>
    </footer>

    <nav class="bottom-nav" aria-label="{{ __('ui.layout.bottom_navigation') }}">
        @foreach($bottomLinks as $link)
            <a @class(['active' => request()->routeIs($link['active'])]) href="{{ route($link['route']) }}" @if(request()->routeIs($link['active'])) aria-current="page" @endif><x-dynamic-component :component="'lucide-'.$link['icon']" /><span>{{ $link['label'] }}</span></a>
        @endforeach
    </nav>

    @include('partials.location-data')
</body>
</html>
