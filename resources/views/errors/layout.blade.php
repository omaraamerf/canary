{{-- Standalone on purpose: it must render even when the database is unavailable. --}}
<!doctype html>
<html lang="{{ app()->getLocale() }}" dir="{{ __('ui.direction') }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title }} | {{ __('ui.brand.name') }}</title>
    <link rel="stylesheet" href="{{ asset('assets/css/site.css') }}?v=10">
</head>
<body>
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
