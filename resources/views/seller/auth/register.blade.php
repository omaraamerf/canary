<!doctype html>
<html lang="{{ app()->getLocale() }}" dir="{{ __('ui.direction') }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>{{ __('ui.pages.sell.register') }} | {{ __('ui.brand.name') }}</title>
    <link rel="stylesheet" href="{{ asset('admin-assets/css/bootstrap-rtl.min.css') }}">
    <link rel="stylesheet" href="{{ asset('admin-assets/css/app-rtl.min.css') }}">
    <link rel="stylesheet" href="{{ asset('admin-assets/css/canary-admin.css') }}?v=5">
</head>
<body>
    <main class="container py-5" style="max-width:820px">
        <div class="admin-section">
            <div class="text-center mb-4">
                <span class="admin-brand-mark mx-auto mb-3"><i data-lucide="bird"></i></span>
                <a href="{{ route('locale.switch', app()->isLocale('ar') ? 'en' : 'ar') }}" class="btn btn-sm btn-outline mb-3">{{ app()->isLocale('ar') ? 'English' : 'العربية' }}</a>
                <h1 class="h3">{{ __('ui.register.heading') }}</h1>
                <p class="text-muted">{{ __('ui.register.lead') }}</p>
            </div>

            @if($errors->any())
                <div class="form-errors mb-3">{{ $errors->first() }}</div>
            @endif

            <form class="form-grid" method="post" action="{{ route('seller.register.store') }}">
                @csrf
                <label>{{ __('ui.register.manager') }}<input name="name" required value="{{ old('name') }}"></label>
                <label>{{ __('ui.register.display_name') }}<input name="display_name" required value="{{ old('display_name') }}"></label>
                <label>{{ __('ui.common.phone') }}<input name="phone" required value="{{ old('phone') }}" dir="ltr"></label>
                <label>{{ __('ui.common.email') }}<input type="email" name="email" required value="{{ old('email') }}"></label>
                <label>
                    {{ __('ui.common.region') }}
                    <select name="region_id" required>
                        <option value="">{{ __('ui.register.choose_region') }}</option>
                        @foreach($regions as $region)
                            <option value="{{ $region->id }}" @selected(old('region_id') == $region->id)>{{ $region->name }}</option>
                        @endforeach
                    </select>
                </label>
                <label class="span-2">{{ __('ui.register.bio') }}<textarea name="bio" rows="3">{{ old('bio') }}</textarea></label>
                <label>{{ __('ui.register.password') }}<input type="password" name="password" required></label>
                <label>{{ __('ui.register.password_confirmation') }}<input type="password" name="password_confirmation" required></label>
                <div class="span-2 d-flex gap-2 justify-content-end">
                    <a class="btn btn-outline" href="{{ route('filament.seller.auth.login') }}">{{ __('ui.register.have_account') }}</a>
                    <button class="btn btn-primary btn-large" type="submit">{{ __('ui.register.submit') }}</button>
                </div>
            </form>
        </div>
    </main>
    <script src="{{ asset('assets/js/canary.js') }}" type="module"></script>
</body>
</html>
