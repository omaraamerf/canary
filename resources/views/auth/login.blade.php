@extends('layouts.app')

@section('title', __('ui.auth.login_title'))

@section('content')
<section class="success-page">
    <div class="success-panel auth-panel">
        <span class="success-icon"><x-lucide-log-in /></span>
        <h1>{{ __('ui.auth.login_title') }}</h1>
        <p>{{ __('ui.auth.login_lead') }}</p>

        @if($errors->any())
            <div class="form-errors">{{ $errors->first() }}</div>
        @endif

        <form class="tracking-form" method="post" action="{{ route('login.store') }}">
            @csrf
            <label>
                {{ __('ui.common.email') }}
                <input type="email" name="email" value="{{ old('email') }}" required autocomplete="email" dir="ltr">
            </label>
            <label>
                {{ __('ui.auth.password') }}
                <input type="password" name="password" required autocomplete="current-password" dir="ltr">
            </label>
            <label class="check-row"><input type="checkbox" name="remember" value="1"><span>{{ __('ui.auth.remember') }}</span></label>
            <button class="btn btn-primary btn-large" type="submit"><x-lucide-log-in /> {{ __('ui.auth.submit_login') }}</button>
        </form>

        <p class="auth-switch">{{ __('ui.auth.no_account') }} <a href="{{ route('register') }}">{{ __('ui.auth.create_account') }}</a></p>
        <p class="auth-switch">{{ __('ui.auth.seller_hint') }} <a href="{{ route('filament.seller.auth.login') }}">{{ __('ui.auth.seller_login') }}</a></p>
    </div>
</section>
@endsection
