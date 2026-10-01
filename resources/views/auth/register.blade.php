@extends('layouts.app')

@section('title', __('ui.auth.register_title'))
@section('no_region_prompt', '1')

@section('content')
<x-auth-shell :title="__('ui.auth.register_title')" :lead="__('ui.auth.register_lead')" icon="user-round-plus" image="/images/birds/classic-canary.jpg">
    @if($errors->any())<div class="form-errors" role="alert">{{ $errors->first() }}</div>@endif

    <form class="auth-form" method="post" action="{{ route('register.store') }}">
        @csrf
        <label>
            {{ __('ui.common.name') }}
            <input name="name" value="{{ old('name') }}" required autocomplete="name" maxlength="100">
        </label>
        <div class="auth-form-row">
            <label>
                {{ __('ui.common.email') }}
                <input type="email" name="email" value="{{ old('email') }}" required autocomplete="email" dir="ltr">
            </label>
            <label>
                <span>{{ __('ui.common.phone') }} <small>{{ __('ui.auth.optional') }}</small></span>
                <input name="phone" value="{{ old('phone') }}" inputmode="tel" autocomplete="tel" dir="ltr">
            </label>
        </div>
        <x-location-picker class="auth-form-row" />
        <div class="auth-form-row">
            <label>
                {{ __('ui.auth.password') }}
                <x-ui.password autocomplete="new-password" required minlength="8" />
            </label>
            <label>
                {{ __('ui.auth.password_confirmation') }}
                <x-ui.password name="password_confirmation" autocomplete="new-password" required minlength="8" />
            </label>
        </div>
        <p class="field-hint">{{ __('ui.auth.password_hint') }}</p>
        <x-ui.button type="submit" size="lg" icon="user-round-plus" block>{{ __('ui.auth.submit_register') }}</x-ui.button>
    </form>

    <div class="auth-alt">
        <p>{{ __('ui.auth.have_account') }} <a href="{{ route('login') }}">{{ __('ui.auth.submit_login') }}</a></p>
        <p>{{ __('ui.auth.seller_hint') }} <a href="{{ route('seller.register') }}">{{ __('ui.auth.seller_register') }}</a></p>
    </div>

    <x-slot:aside>
        <h2>{{ __('ui.auth.register_aside_title') }}</h2>
        <ul class="auth-points">
            <li><span><x-lucide-badge-check /></span><div><strong>{{ __('ui.auth.point_free') }}</strong><p>{{ __('ui.auth.point_free_text') }}</p></div></li>
            <li><span><x-lucide-messages-square /></span><div><strong>{{ __('ui.auth.point_community') }}</strong><p>{{ __('ui.auth.point_community_text') }}</p></div></li>
            <li><span><x-lucide-package-search /></span><div><strong>{{ __('ui.auth.point_orders') }}</strong><p>{{ __('ui.auth.point_orders_text') }}</p></div></li>
        </ul>
    </x-slot:aside>
</x-auth-shell>
@endsection
