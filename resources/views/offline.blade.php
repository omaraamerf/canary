{{--
    Shown by the service worker (public/sw.js) when a page cannot load and was never saved.
    Standalone like the error pages; the worker stores it, with its styles, when it installs.
--}}
<!doctype html>
<html lang="{{ app()->getLocale() }}" dir="{{ __('ui.direction') }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex">
    <title>{{ __('ui.pwa.offline_title') }} | {{ __('ui.brand.name') }}</title>
    @include('partials.head-icons')
    @include('partials.theme-script')
    {{-- Linked here so the service worker stores the font files along with this page. --}}
    @foreach(['arabic', 'latin'] as $subset)
        <link rel="preload" href="{{ Vite::asset("node_modules/@fontsource-variable/readex-pro/files/readex-pro-{$subset}-wght-normal.woff2") }}" as="font" type="font/woff2" crossorigin>
    @endforeach
    @vite('resources/css/app.css')
</head>
<body class="bg-bg text-fg antialiased">
    <header class="error-header"><div class="container">
        <a href="{{ url('/') }}" class="brand" aria-label="{{ __('ui.nav.home') }}">
            <img class="brand-mark" src="/images/brand/mark.svg" alt="" width="44" height="44">
            <span><strong>{{ __('ui.brand.name') }}</strong><small>{{ __('ui.brand.tagline') }}</small></span>
        </a>
    </div></header>
    <main class="error-page" id="main">
        <div class="error-art" aria-hidden="true"><span class="error-icon"><x-lucide-wifi-off /></span></div>
        <h1>{{ __('ui.pwa.offline_title') }}</h1>
        <p>{{ __('ui.pwa.offline_text') }}</p>
        <div class="error-actions">
            <button type="button" class="btn btn-primary" onclick="location.reload()"><x-lucide-rotate-cw />{{ __('ui.errors.retry') }}</button>
        </div>
        <nav class="offline-saved" aria-labelledby="saved-heading" hidden>
            <h2 id="saved-heading">{{ __('ui.pwa.saved_pages') }}</h2>
            <ul data-saved-pages></ul>
        </nav>
    </main>
    <script>
        // Pages the service worker saved on earlier visits, by their titles.
        (async () => {
            if (! ('caches' in window)) return;
            const list = document.querySelector('[data-saved-pages]');
            const names = (await caches.keys()).filter((name) => name.startsWith('pages-'));
            for (const name of names) {
                const cache = await caches.open(name);
                for (const request of (await cache.keys()).slice(-12).reverse()) {
                    const url = new URL(request.url);
                    if (url.pathname === '/offline') continue;
                    const html = await (await cache.match(request)).text();
                    const title = (html.match(/<title>([^<|]*)/) || [])[1]?.trim() || url.pathname;
                    const link = document.createElement('a');
                    link.href = url.pathname + url.search;
                    link.textContent = title;
                    const item = document.createElement('li');
                    item.append(link);
                    list.append(item);
                }
            }
            list.closest('nav').hidden = ! list.children.length;
        })();
    </script>
</body>
</html>
