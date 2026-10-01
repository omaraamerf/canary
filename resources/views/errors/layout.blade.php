{{-- Standalone on purpose: it must render even when the database is unavailable. --}}
<!doctype html>
<html lang="{{ app()->getLocale() }}" dir="{{ __('ui.direction') }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title }} | {{ __('ui.brand.name') }}</title>
    @include('partials.theme-script')
    {{-- Styles are optional here: a missing asset build must not break the error page itself. --}}
    @if(is_file(public_path('build/manifest.json')) || is_file(public_path('hot')))
        @vite('resources/css/app.css')
    @endif
</head>
<body class="bg-bg text-fg antialiased">
    <main class="success-page error-page">
        <div class="success-panel">
            <span class="error-code">{{ $code }}</span>
            <h1>{{ $title }}</h1>
            <p>{{ $message }}</p>
            <div class="tracking-actions">
                <a class="btn btn-primary" href="{{ url('/') }}">{{ __('ui.errors.home') }}</a>
                @if(in_array($code, [403, 404, 419], true))<a class="btn btn-outline" href="{{ url()->previous() }}">{{ __('ui.errors.back') }}</a>@endif
            </div>
        </div>
    </main>
</body>
</html>
