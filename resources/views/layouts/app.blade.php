<!doctype html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'كناري') | سوق الكناري الموثوق</title>
    <meta name="description" content="@yield('meta_description', 'طيور كناري مختارة ببيانات واضحة وحجز مباشر.')">
    <link rel="stylesheet" href="{{ asset('assets/css/canary.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/site.css') }}?v=7">
</head>
<body class="bg-[#f7f7f4] text-[#171913] antialiased">
    <header class="site-header">
        <div class="container flex h-18 items-center justify-between gap-5">
            <a href="{{ route('home') }}" class="brand" aria-label="الرئيسية">
                <span class="brand-mark"><i data-lucide="bird"></i></span>
                <span><strong>كناري</strong><small>سوق الطيور المختارة</small></span>
            </a>
            <nav class="hidden items-center gap-7 md:flex" aria-label="التنقل الرئيسي">
                <a class="nav-link {{ request()->routeIs('home') ? 'active' : '' }}" href="{{ route('home') }}">الرئيسية</a>
                <a class="nav-link {{ request()->routeIs('birds.*') ? 'active' : '' }}" href="{{ route('birds.index') }}">كل الطيور</a>
                @if($guideEnabled)<a class="nav-link {{ request()->routeIs('guide.*') ? 'active' : '' }}" href="{{ route('guide.index') }}">دليل الكناري</a>@endif
                <a class="nav-link {{ request()->routeIs('about') ? 'active' : '' }}" href="{{ route('about') }}">من نحن</a>
                <a class="nav-link {{ request()->routeIs('policy') ? 'active' : '' }}" href="{{ route('policy') }}">سياسة الحجز</a>
                <a class="nav-link {{ request()->routeIs('start-selling') ? 'active' : '' }}" href="{{ route('start-selling') }}">اعرض طائرك</a>
                <a class="nav-link {{ request()->routeIs('orders.track*') ? 'active' : '' }}" href="{{ route('orders.track') }}">تتبع طلبك</a>
            </nav>
            <div class="flex items-center gap-2">
                <details class="region-switcher"><summary><i data-lucide="map-pin"></i><span>{{ $selectedRegion?->name ?: 'كل المناطق' }}</span></summary><form method="post" action="{{ route('region.select') }}">@csrf<button name="region" value="all" type="submit">كل المناطق</button>@foreach($navigationRegions as $region)<button name="region" value="{{ $region->id }}" type="submit">{{ $region->name }}</button>@endforeach</form></details>
                <a href="{{ route('birds.index') }}" class="btn btn-dark hidden sm:inline-flex"><i data-lucide="search"></i> ابحث عن طائر</a>
                <button type="button" class="icon-btn md:hidden" data-menu-toggle aria-label="فتح القائمة" aria-controls="mobile-navigation" aria-expanded="false"><i data-lucide="menu"></i></button>
            </div>
        </div>
        <nav id="mobile-navigation" class="mobile-menu hidden" data-mobile-menu aria-label="التنقل للجوال">
            <a href="{{ route('home') }}">الرئيسية</a>
            <a href="{{ route('birds.index') }}">كل الطيور</a>
            @if($guideEnabled)<a href="{{ route('guide.index') }}">دليل الكناري</a>@endif
            <a href="{{ route('about') }}">من نحن</a>
            <a href="{{ route('policy') }}">سياسة الحجز</a>
            <a href="{{ route('start-selling') }}">اعرض طائرك</a>
            <a href="{{ route('orders.track') }}">تتبع طلبك</a>
            <a href="{{ route('filament.seller.auth.login') }}">دخول البائع</a>
        </nav>
    </header>

    @unless($regionChosen)
        <div class="region-gate"><div class="region-gate-panel"><span class="brand-mark"><i data-lucide="map-pin"></i></span><h2>اختر منطقتك</h2><p>سنُظهر لك الطيور الأقرب أولًا، ويمكنك تغيير الاختيار في أي وقت.</p><form method="post" action="{{ route('region.select') }}">@csrf<div class="region-options">@foreach($navigationRegions as $region)<button name="region" value="{{ $region->id }}" type="submit">{{ $region->name }}</button>@endforeach</div><div class="region-or"><span>أو</span></div><button class="btn btn-outline w-full" name="region" value="all" type="submit">عرض كل المناطق</button></form></div></div>
    @endunless

    <main>@yield('content')</main>

    <footer class="site-footer">
        <div class="container footer-grid">
            <div><a href="{{ route('home') }}" class="brand brand-light"><span class="brand-mark"><i data-lucide="bird"></i></span><span><strong>كناري</strong><small>اختيار أوضح، وحجز أبسط</small></span></a></div>
            <p>نرتب بيانات الطائر وصوره وفيديوه في مكان واحد، ثم نؤكد الحجز معك مباشرة.</p>
            <div class="footer-links"><a href="{{ route('birds.index') }}">الطيور</a>@if($guideEnabled)<a href="{{ route('guide.index') }}">دليل الكناري</a>@endif<a href="{{ route('filament.seller.auth.login') }}">دخول البائع</a></div>
        </div>
        <div class="container footer-bottom">© {{ date('Y') }} كناري. جميع الحقوق محفوظة.</div>
    </footer>
    <script src="{{ asset('assets/js/canary.js') }}" type="module"></script>
    <script src="{{ asset('assets/js/mobile-menu.js') }}?v=1" defer></script>
</body>
</html>
