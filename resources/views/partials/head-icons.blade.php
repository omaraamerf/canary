{{-- Favicon, app icons and the web app manifest (installable site, see public/sw.js). --}}
<link rel="icon" href="/images/icons/icon.svg" type="image/svg+xml">
<link rel="icon" href="/images/icons/favicon-32.png" sizes="32x32" type="image/png">
<link rel="apple-touch-icon" href="/images/icons/apple-touch-icon.png">
<link rel="manifest" href="{{ route('manifest') }}">
<meta name="theme-color" content="#ffffff" media="(prefers-color-scheme: light)">
<meta name="theme-color" content="#181b16" media="(prefers-color-scheme: dark)">
<meta name="apple-mobile-web-app-title" content="{{ __('ui.brand.name') }}">
