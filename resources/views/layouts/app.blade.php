<!doctype html>
<html lang="{{ app()->getLocale() }}" dir="{{ __('ui.direction') }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', __('ui.brand.name')) | {{ __('ui.layout.title') }}</title>
    <meta name="description" content="@yield('meta_description', __('ui.layout.description'))">
    <link rel="stylesheet" href="{{ asset('assets/css/canary.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/site.css') }}?v=7">
</head>
<body class="bg-[#f7f7f4] text-[#171913] antialiased">
    <header class="site-header">
        <div class="container flex h-18 items-center justify-between gap-5">
            <a href="{{ route('home') }}" class="brand" aria-label="{{ __('ui.nav.home') }}">
                <span class="brand-mark"><i data-lucide="bird"></i></span>
                <span><strong>{{ __('ui.brand.name') }}</strong><small>{{ __('ui.brand.tagline') }}</small></span>
            </a>
            <nav class="hidden items-center gap-7 md:flex" aria-label="{{ __('ui.layout.main_navigation') }}">
                <a class="nav-link {{ request()->routeIs('home') ? 'active' : '' }}" href="{{ route('home') }}">{{ __('ui.nav.home') }}</a>
                <a class="nav-link {{ request()->routeIs('birds.*') ? 'active' : '' }}" href="{{ route('birds.index') }}">{{ __('ui.nav.birds') }}</a>
                @if($guideEnabled)<a class="nav-link {{ request()->routeIs('guide.*') ? 'active' : '' }}" href="{{ route('guide.index') }}">{{ __('ui.nav.guide') }}</a>@endif
                <a class="nav-link {{ request()->routeIs('about') ? 'active' : '' }}" href="{{ route('about') }}">{{ __('ui.nav.about') }}</a>
                <a class="nav-link {{ request()->routeIs('policy') ? 'active' : '' }}" href="{{ route('policy') }}">{{ __('ui.nav.policy') }}</a>
                <a class="nav-link {{ request()->routeIs('start-selling') ? 'active' : '' }}" href="{{ route('start-selling') }}">{{ __('ui.nav.sell') }}</a>
                <a class="nav-link {{ request()->routeIs('orders.track*') ? 'active' : '' }}" href="{{ route('orders.track') }}">{{ __('ui.nav.track') }}</a>
            </nav>
            <div class="flex items-center gap-2">
                <a class="language-switch" href="{{ route('locale.switch', app()->isLocale('ar') ? 'en' : 'ar') }}" aria-label="{{ __('ui.language.switch') }}">{{ app()->isLocale('ar') ? 'EN' : 'ع' }}</a>
                <details class="region-switcher"><summary><i data-lucide="map-pin"></i><span>{{ $selectedRegion?->name ?: __('ui.layout.all_regions') }}</span></summary><form method="post" action="{{ route('region.select') }}">@csrf<button name="region" value="all" type="submit">{{ __('ui.layout.all_regions') }}</button>@foreach($navigationRegions as $region)<button name="region" value="{{ $region->id }}" type="submit">{{ $region->name }}</button>@endforeach</form></details>
                <a href="{{ route('birds.index') }}" class="btn btn-dark hidden sm:inline-flex"><i data-lucide="search"></i> {{ __('ui.layout.find_bird') }}</a>
                <button type="button" class="icon-btn md:hidden" data-menu-toggle aria-label="{{ __('ui.layout.open_menu') }}" aria-controls="mobile-navigation" aria-expanded="false"><i data-lucide="menu"></i></button>
            </div>
        </div>
        <nav id="mobile-navigation" class="mobile-menu hidden" data-mobile-menu aria-label="{{ __('ui.layout.mobile_navigation') }}">
            <a href="{{ route('home') }}">{{ __('ui.nav.home') }}</a>
            <a href="{{ route('birds.index') }}">{{ __('ui.nav.birds') }}</a>
            @if($guideEnabled)<a href="{{ route('guide.index') }}">{{ __('ui.nav.guide') }}</a>@endif
            <a href="{{ route('about') }}">{{ __('ui.nav.about') }}</a>
            <a href="{{ route('policy') }}">{{ __('ui.nav.policy') }}</a>
            <a href="{{ route('start-selling') }}">{{ __('ui.nav.sell') }}</a>
            <a href="{{ route('orders.track') }}">{{ __('ui.nav.track') }}</a>
            <a href="{{ route('filament.seller.auth.login') }}">{{ __('ui.nav.seller_login') }}</a>
        </nav>
    </header>

    @unless($regionChosen)
        <div class="region-gate"><div class="region-gate-panel"><span class="brand-mark"><i data-lucide="map-pin"></i></span><h2>{{ __('ui.layout.choose_region') }}</h2><p>{{ __('ui.layout.region_help') }}</p><form method="post" action="{{ route('region.select') }}">@csrf<div class="region-options">@foreach($navigationRegions as $region)<button name="region" value="{{ $region->id }}" type="submit">{{ $region->name }}</button>@endforeach</div><div class="region-or"><span>{{ __('ui.layout.or') }}</span></div><button class="btn btn-outline w-full" name="region" value="all" type="submit">{{ __('ui.layout.show_all_regions') }}</button></form></div></div>
    @endunless

    <main>@yield('content')</main>

    <footer class="site-footer">
        <div class="container footer-grid">
            <div><a href="{{ route('home') }}" class="brand brand-light"><span class="brand-mark"><i data-lucide="bird"></i></span><span><strong>{{ __('ui.brand.name') }}</strong><small>{{ __('ui.brand.promise') }}</small></span></a></div>
            <p>{{ __('ui.layout.footer_about') }}</p>
            <div class="footer-links"><a href="{{ route('birds.index') }}">{{ __('ui.birds.plural') }}</a>@if($guideEnabled)<a href="{{ route('guide.index') }}">{{ __('ui.nav.guide') }}</a>@endif<a href="{{ route('filament.seller.auth.login') }}">{{ __('ui.nav.seller_login') }}</a></div>
        </div>
        <div class="container footer-bottom">© {{ date('Y') }} {{ __('ui.brand.name') }}. {{ __('ui.layout.rights') }}</div>
    </footer>
    <script src="{{ asset('assets/js/canary.js') }}" type="module"></script>
    <script src="{{ asset('assets/js/mobile-menu.js') }}?v=1" defer></script>
</body>
</html>
