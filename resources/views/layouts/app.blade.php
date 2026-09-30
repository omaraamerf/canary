<!doctype html>
<html lang="{{ app()->getLocale() }}" dir="{{ __('ui.direction') }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', __('ui.brand.name')) | {{ __('ui.layout.title') }}</title>
    <meta name="description" content="@yield('meta_description', __('ui.layout.description'))">
    <link rel="stylesheet" href="{{ asset('assets/css/canary.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/site.css') }}?v=9">
</head>
<body class="bg-[#f7f7f4] text-[#171913] antialiased">
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
                    <details class="region-switcher"><summary><i data-lucide="map-pin"></i><span>{{ $selectedRegion?->name ?: __('ui.layout.all_regions') }}</span><i data-lucide="chevron-down" class="chevron"></i></summary><form method="post" action="{{ route('region.select') }}">@csrf<button name="region" value="all" type="submit">{{ __('ui.layout.all_regions') }}</button>@foreach($navigationRegions as $region)<button name="region" value="{{ $region->id }}" type="submit">{{ $region->name }}</button>@endforeach</form></details>
                    <a class="topbar-link topbar-optional" href="{{ route('orders.track') }}"><i data-lucide="package-search"></i>{{ __('ui.nav.track') }}</a>
                </div>
                <div class="topbar-group">
                    <a class="topbar-link topbar-optional" href="{{ route('start-selling') }}"><i data-lucide="store"></i>{{ __('ui.nav.sell') }}</a>
                    @guest<a class="topbar-link topbar-optional" href="{{ route('filament.seller.auth.login') }}">{{ __('ui.nav.seller_login') }}</a>@endguest
                    <a class="topbar-link topbar-lang" href="{{ route('locale.switch', app()->isLocale('ar') ? 'en' : 'ar') }}" aria-label="{{ __('ui.language.switch') }}" hreflang="{{ app()->isLocale('ar') ? 'en' : 'ar' }}"><i data-lucide="languages"></i>{{ app()->isLocale('ar') ? __('ui.language.english') : __('ui.language.arabic') }}</a>
                </div>
            </div>
        </div>

        <div class="container header-main">
            <a href="{{ route('home') }}" class="brand" aria-label="{{ __('ui.nav.home') }}">
                <span class="brand-mark"><i data-lucide="bird"></i></span>
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
                            <span class="account-avatar">{{ mb_substr($currentUser->public_name, 0, 1) }}</span>
                            <span class="account-name">{{ $currentUser->public_name }}</span>
                            <i data-lucide="chevron-down" class="chevron"></i>
                        </summary>
                        <div class="account-panel">
                            <div class="account-panel-head"><strong>{{ $currentUser->public_name }}</strong><small dir="ltr">{{ $currentUser->email }}</small></div>
                            @if($currentUser->isSeller())<a href="{{ url('/seller') }}"><i data-lucide="layout-dashboard"></i>{{ __('ui.nav.seller_panel') }}</a>@endif
                            @if($currentUser->isAdmin())<a href="{{ url('/admin') }}"><i data-lucide="shield-check"></i>{{ __('ui.nav.admin_panel') }}</a>@endif
                            @if($communityEnabled)<a href="{{ route('community.create') }}"><i data-lucide="message-circle-plus"></i>{{ __('ui.community.new_post') }}</a>@endif
                            <form method="post" action="{{ route('logout') }}">@csrf<button type="submit"><i data-lucide="log-out"></i>{{ __('ui.nav.logout') }}</button></form>
                        </div>
                    </details>
                @else
                    <a href="{{ route('login') }}" class="header-login"><i data-lucide="circle-user-round"></i><span>{{ __('ui.nav.login') }}</span></a>
                @endif
                <a href="{{ route('birds.index') }}" class="btn btn-dark header-cta"><i data-lucide="search"></i><span>{{ __('ui.layout.find_bird') }}</span></a>
                <button type="button" class="icon-btn menu-toggle" data-menu-toggle aria-label="{{ __('ui.layout.open_menu') }}" aria-controls="mobile-navigation" aria-expanded="false"><i data-lucide="menu"></i></button>
            </div>
        </div>

        <nav id="mobile-navigation" class="mobile-menu hidden" data-mobile-menu aria-label="{{ __('ui.layout.mobile_navigation') }}">
            @foreach($mainLinks as $link)
                <a @class(['active' => request()->routeIs($link['active'])]) href="{{ route($link['route']) }}"><i data-lucide="{{ $link['icon'] }}"></i>{{ $link['label'] }}</a>
            @endforeach
            <span class="mobile-menu-divider"></span>
            <a href="{{ route('orders.track') }}"><i data-lucide="package-search"></i>{{ __('ui.nav.track') }}</a>
            <a href="{{ route('start-selling') }}"><i data-lucide="store"></i>{{ __('ui.nav.sell') }}</a>
            <a href="{{ route('policy') }}"><i data-lucide="file-text"></i>{{ __('ui.nav.policy') }}</a>
            <span class="mobile-menu-divider"></span>
            @if($currentUser)
                <form method="post" action="{{ route('logout') }}">@csrf<button type="submit"><i data-lucide="log-out"></i>{{ __('ui.nav.logout') }} ({{ $currentUser->public_name }})</button></form>
            @else
                <div class="mobile-menu-auth">
                    <a class="btn btn-primary" href="{{ route('login') }}">{{ __('ui.nav.login') }}</a>
                    <a class="btn btn-outline" href="{{ route('register') }}">{{ __('ui.nav.register') }}</a>
                </div>
                <a href="{{ route('filament.seller.auth.login') }}"><i data-lucide="store"></i>{{ __('ui.nav.seller_login') }}</a>
            @endif
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
            <div class="footer-links"><a href="{{ route('birds.index') }}">{{ __('ui.birds.plural') }}</a>@if($guideEnabled)<a href="{{ route('guide.index') }}">{{ __('ui.nav.guide') }}</a>@endif @if($communityEnabled)<a href="{{ route('community.index') }}">{{ __('ui.nav.community') }}</a>@endif<a href="{{ route('orders.track') }}">{{ __('ui.nav.track') }}</a></div>
            <div class="footer-links"><a href="{{ route('about') }}">{{ __('ui.nav.about') }}</a><a href="{{ route('policy') }}">{{ __('ui.nav.policy') }}</a><a href="{{ route('start-selling') }}">{{ __('ui.nav.sell') }}</a><a href="{{ route('filament.seller.auth.login') }}">{{ __('ui.nav.seller_login') }}</a></div>
        </div>
        <div class="container footer-bottom">© {{ date('Y') }} {{ __('ui.brand.name') }}. {{ __('ui.layout.rights') }}</div>
    </footer>
    <script src="{{ asset('assets/js/canary.js') }}" type="module"></script>
    <script src="https://unpkg.com/lucide@0.460.0/dist/umd/lucide.min.js" defer></script>
    <script src="{{ asset('assets/js/mobile-menu.js') }}?v=2" defer></script>
</body>
</html>
