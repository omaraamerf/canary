<!doctype html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'الإدارة') | كناري</title>
    <link rel="stylesheet" href="{{ asset('admin-assets/css/bootstrap-rtl.min.css') }}">
    <link rel="stylesheet" href="{{ asset('admin-assets/css/icons-rtl.min.css') }}">
    <link rel="stylesheet" href="{{ asset('admin-assets/css/app-rtl.min.css') }}">
    <link rel="stylesheet" href="{{ asset('admin-assets/css/canary-admin.css') }}?v=4">
</head>
<body data-sidebar="dark">
<div id="layout-wrapper">
    <header id="page-topbar">
        <div class="navbar-header">
            <div class="d-flex align-items-center">
                <div class="navbar-brand-box">
                    <a href="{{ route('admin.dashboard') }}" class="logo admin-brand">
                        <span class="admin-brand-mark"><i data-lucide="bird"></i></span>
                        <span class="logo-txt">كناري</span>
                    </a>
                </div>
                <button type="button" class="btn btn-sm px-3 font-size-16 header-item" id="vertical-menu-btn" aria-label="فتح القائمة">
                    <i data-lucide="panel-right"></i>
                </button>
                <div class="d-none d-md-block ms-3">
                    <span class="text-muted small">@yield('eyebrow', 'لوحة الإدارة')</span>
                    <h5 class="mb-0 mt-1">@yield('heading')</h5>
                </div>
            </div>
            <div class="d-flex align-items-center gap-2">
                <a href="{{ route('home') }}" target="_blank" class="btn btn-light header-item d-none d-sm-inline-flex align-items-center gap-2">
                    <i data-lucide="external-link"></i><span>عرض الموقع</span>
                </a>
                <div class="header-item d-flex align-items-center gap-2 px-3">
                    <span class="admin-avatar">{{ \Illuminate\Support\Str::substr(auth()->user()->name, 0, 1) }}</span>
                    <span class="d-none d-md-inline">{{ auth()->user()->name }}</span>
                </div>
                <form action="{{ route('admin.logout') }}" method="post" class="m-0">
                    @csrf
                    <button class="btn header-item" type="submit" aria-label="تسجيل الخروج"><i data-lucide="log-out"></i></button>
                </form>
            </div>
        </div>
    </header>

    <aside class="vertical-menu">
        <div class="mobile-sidebar-head">
            <span><span class="admin-brand-mark"><i data-lucide="bird"></i></span> كناري</span>
            <button type="button" data-sidebar-close aria-label="إغلاق القائمة"><i data-lucide="x"></i></button>
        </div>
        <div data-simplebar class="h-100">
            <div id="sidebar-menu">
                <ul class="metismenu list-unstyled" id="side-menu">
                    <li class="menu-title">الإدارة</li>
                    <li class="{{ request()->routeIs('admin.dashboard') ? 'mm-active' : '' }}"><a class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" href="{{ route('admin.dashboard') }}"><i data-lucide="layout-dashboard"></i><span>نظرة عامة</span></a></li>
                    <li class="{{ request()->routeIs('admin.birds.*') ? 'mm-active' : '' }}"><a class="{{ request()->routeIs('admin.birds.*') ? 'active' : '' }}" href="{{ route('admin.birds.index') }}"><i data-lucide="bird"></i><span>الطيور</span></a></li>
                    <li class="{{ request()->routeIs('admin.orders.*') ? 'mm-active' : '' }}"><a class="{{ request()->routeIs('admin.orders.*') ? 'active' : '' }}" href="{{ route('admin.orders.index') }}"><i data-lucide="clipboard-list"></i><span>الطلبات</span></a></li>
                    <li class="menu-title mt-3">روابط</li>
                    <li><a href="{{ route('home') }}" target="_blank"><i data-lucide="globe"></i><span>الموقع العام</span></a></li>
                </ul>
            </div>
        </div>
    </aside>
    <button type="button" class="sidebar-backdrop" data-sidebar-close aria-label="إغلاق القائمة"></button>

    <main class="main-content">
        <div class="page-content">
            <div class="container-fluid">
                <div class="d-md-none mb-4"><span class="text-muted small">@yield('eyebrow', 'لوحة الإدارة')</span><h4 class="mt-1">@yield('heading')</h4></div>
                @if(session('success'))
                    <div class="alert alert-success d-flex align-items-center gap-2"><i data-lucide="circle-check"></i>{{ session('success') }}</div>
                @endif
                @yield('content')
            </div>
        </div>
        <footer class="footer"><div class="container-fluid"><div class="row"><div class="col">{{ date('Y') }} © كناري</div><div class="col text-end d-none d-sm-block">إدارة الطيور والطلبات</div></div></div></footer>
    </main>
</div>
<script>
document.getElementById('vertical-menu-btn')?.addEventListener('click', function () {
    if (window.innerWidth < 992) {
        document.body.classList.toggle('sidebar-enable');
        return;
    }
    document.body.setAttribute('data-sidebar-size', document.body.getAttribute('data-sidebar-size') === 'sm' ? 'lg' : 'sm');
});
document.querySelectorAll('[data-sidebar-close]').forEach(function (button) {
    button.addEventListener('click', function () {
        document.body.classList.remove('sidebar-enable');
    });
});
</script>
<script src="{{ asset('assets/js/canary.js') }}?v=3" type="module"></script>
</body>
</html>
