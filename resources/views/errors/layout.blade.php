{{-- Standalone on purpose: it must render even when the database is unavailable. --}}
@php
    $icon = [403 => 'lock-keyhole', 404 => 'map-pin-off', 419 => 'timer-reset', 429 => 'hourglass', 500 => 'server-crash', 503 => 'wrench'][$code] ?? 'circle-alert';
    // While the site itself is failing, links into it would only fail again.
    $siteWorks = ! in_array($code, [500, 503], true);
@endphp
<!doctype html>
<html lang="{{ app()->getLocale() }}" dir="{{ __('ui.direction') }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex">
    <title>{{ $title }} | {{ __('ui.brand.name') }}</title>
    @include('partials.head-icons')
    @include('partials.theme-script')
    {{-- Styles are optional here: a missing asset build must not break the error page itself. --}}
    @if(is_file(public_path('build/manifest.json')) || is_file(public_path('hot')))
        @vite('resources/css/app.css')
    @endif
</head>
<body class="bg-bg text-fg antialiased">
    <header class="error-header"><div class="container">
        <a href="{{ url('/') }}" class="brand" aria-label="{{ __('ui.nav.home') }}">
            <span class="brand-mark"><x-lucide-bird /></span>
            <span><strong>{{ __('ui.brand.name') }}</strong><small>{{ __('ui.brand.tagline') }}</small></span>
        </a>
    </div></header>
    <main class="error-page" id="main">
        <div class="error-art" aria-hidden="true">
            <span class="error-code">{{ $code }}</span>
            <span class="error-icon"><x-dynamic-component :component="'lucide-'.$icon" /></span>
        </div>
        <h1>{{ $title }}</h1>
        <p>{{ $message }}</p>

        @if($siteWorks && in_array($code, [403, 404], true))
            <form class="search-bar error-search" action="{{ url('/birds') }}" method="get" role="search">
                <x-lucide-search />
                <input type="search" name="q" placeholder="{{ __('ui.errors.search_placeholder') }}" aria-label="{{ __('ui.errors.search_placeholder') }}">
                <button type="submit">{{ __('ui.common.search') }}</button>
            </form>
        @endif

        <div class="error-actions">
            @if($siteWorks)
                <a class="btn btn-primary" href="{{ url('/') }}"><x-lucide-house />{{ __('ui.errors.home') }}</a>
                @if(in_array($code, [403, 404, 419], true))<a class="btn btn-outline" href="{{ url()->previous() }}"><x-lucide-arrow-right class="dir-icon" />{{ __('ui.errors.back') }}</a>@endif
            @else
                <a class="btn btn-primary" href="{{ url()->current() }}"><x-lucide-rotate-cw />{{ __('ui.errors.retry') }}</a>
            @endif
        </div>

        @if($siteWorks)
            <nav class="error-links" aria-label="{{ __('ui.errors.links') }}">
                <a href="{{ url('/birds') }}"><x-lucide-bird />{{ __('ui.nav.birds') }}</a>
                <a href="{{ url('/about') }}"><x-lucide-info />{{ __('ui.nav.about') }}</a>
                <a href="{{ url('/orders/track') }}"><x-lucide-package-search />{{ __('ui.nav.track') }}</a>
            </nav>
        @endif
    </main>
</body>
</html>
