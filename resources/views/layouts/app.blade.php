<!doctype html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'كناري') | سوق الكناري الموثوق</title>
    <meta name="description" content="طيور كناري مختارة ببيانات واضحة وحجز مباشر.">
    <link rel="stylesheet" href="{{ asset('assets/css/canary.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/site.css') }}?v=3">
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
                <a class="nav-link {{ request()->routeIs('about') ? 'active' : '' }}" href="{{ route('about') }}">من نحن</a>
                <a class="nav-link {{ request()->routeIs('policy') ? 'active' : '' }}" href="{{ route('policy') }}">سياسة الحجز</a>
            </nav>
            <div class="flex items-center gap-2">
                <a href="{{ route('birds.index') }}" class="btn btn-dark hidden sm:inline-flex"><i data-lucide="search"></i> ابحث عن طائر</a>
                <button class="icon-btn md:hidden" data-menu-toggle aria-label="فتح القائمة" aria-expanded="false"><i data-lucide="menu"></i></button>
            </div>
        </div>
        <nav class="mobile-menu hidden" data-mobile-menu>
            <a href="{{ route('home') }}">الرئيسية</a>
            <a href="{{ route('birds.index') }}">كل الطيور</a>
            <a href="{{ route('about') }}">من نحن</a>
            <a href="{{ route('policy') }}">سياسة الحجز</a>
        </nav>
    </header>

    <main>@yield('content')</main>

    <footer class="site-footer">
        <div class="container footer-grid">
            <div><a href="{{ route('home') }}" class="brand brand-light"><span class="brand-mark"><i data-lucide="bird"></i></span><span><strong>كناري</strong><small>اختيار أوضح، وحجز أبسط</small></span></a></div>
            <p>نرتب بيانات الطائر وصوره وفيديوه في مكان واحد، ثم نؤكد الحجز معك مباشرة.</p>
            <div class="footer-links"><a href="{{ route('birds.index') }}">الطيور</a><a href="{{ route('policy') }}">السياسة</a><a href="{{ route('admin.login') }}">الإدارة</a></div>
        </div>
        <div class="container footer-bottom">© {{ date('Y') }} كناري. جميع الحقوق محفوظة.</div>
    </footer>
    <script src="{{ asset('assets/js/canary.js') }}" type="module"></script>
</body>
</html>
