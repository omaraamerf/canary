@extends('layouts.app')

@section('title', __('ui.pages.sell.register'))
@section('no_region_prompt', '1')

@section('content')
<x-auth-shell :title="__('ui.register.heading')" :lead="__('ui.register.lead')" icon="store" wide>
    @if($errors->any())<div class="form-errors" role="alert">{{ $errors->first() }}</div>@endif

    <form class="auth-form" method="post" action="{{ route('seller.register.store') }}">
        @csrf
        <fieldset class="auth-fieldset">
            <legend>{{ __('ui.register.store_section') }}</legend>
            <label>
                {{ __('ui.register.display_name') }}
                <input name="display_name" value="{{ old('display_name') }}" required maxlength="140" autocomplete="organization">
            </label>
            <x-location-picker class="auth-form-row" :required="true" :region-label="__('ui.register.region')" />
            <label>
                <span>{{ __('ui.register.bio') }} <small>{{ __('ui.auth.optional') }}</small></span>
                <textarea name="bio" rows="3" maxlength="1200" placeholder="{{ __('ui.register.bio_placeholder') }}">{{ old('bio') }}</textarea>
            </label>
        </fieldset>

        <fieldset class="auth-fieldset">
            <legend>{{ __('ui.register.account_section') }}</legend>
            <div class="auth-form-row">
                <label>
                    {{ __('ui.register.manager') }}
                    <input name="name" value="{{ old('name') }}" required maxlength="100" autocomplete="name">
                </label>
                <label>
                    {{ __('ui.common.phone') }}
                    <input name="phone" value="{{ old('phone') }}" required maxlength="30" inputmode="tel" autocomplete="tel" dir="ltr">
                </label>
            </div>
            <label>
                {{ __('ui.common.email') }}
                <input type="email" name="email" value="{{ old('email') }}" required maxlength="190" autocomplete="email" dir="ltr">
            </label>
            <div class="auth-form-row">
                <label>
                    {{ __('ui.register.password') }}
                    <x-ui.password autocomplete="new-password" required minlength="8" />
                </label>
                <label>
                    {{ __('ui.register.password_confirmation') }}
                    <x-ui.password name="password_confirmation" autocomplete="new-password" required minlength="8" />
                </label>
            </div>
            <p class="field-hint">{{ __('ui.auth.password_hint') }}</p>
        </fieldset>

        @if($approvalRequired)<p class="auth-note"><x-lucide-shield-check />{{ __('ui.register.approval_note') }}</p>@endif
        <x-ui.button type="submit" size="lg" icon="store" block>{{ __('ui.register.submit') }}</x-ui.button>
    </form>

    <div class="auth-alt">
        <p>{{ __('ui.register.have_account') }} <a href="{{ route('login') }}">{{ __('ui.auth.submit_login') }}</a></p>
    </div>

    <x-slot:aside>
        <h2>{{ __('ui.register.aside_title') }}</h2>
        <ul class="auth-points">
            <li><span><x-lucide-layout-dashboard /></span><div><strong>{{ __('ui.register.point_panel') }}</strong><p>{{ __('ui.register.point_panel_text') }}</p></div></li>
            <li><span><x-lucide-banknote /></span><div><strong>{{ __('ui.register.point_currency') }}</strong><p>{{ __('ui.register.point_currency_text') }}</p></div></li>
            <li><span><x-lucide-message-circle /></span><div><strong>{{ __('ui.register.point_whatsapp') }}</strong><p>{{ __('ui.register.point_whatsapp_text') }}</p></div></li>
        </ul>
    </x-slot:aside>
</x-auth-shell>
@endsection
