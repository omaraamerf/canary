@extends('layouts.app')

@section('title', __('ui.auth.login_title'))
@section('no_region_prompt', '1')

@section('content')
<x-auth-shell :title="__('ui.auth.login_title')" :lead="__('ui.auth.login_lead')" icon="log-in">
    @if(session('status'))<div class="alert alert-info" role="status"><x-lucide-clock />{{ session('status') }}</div>@endif
    @if($errors->any())<div class="form-errors" role="alert">{{ $errors->first() }}</div>@endif

    <form class="auth-form" method="post" action="{{ route('login.store') }}">
        @csrf
        <label>
            {{ __('ui.common.email') }}
            <input type="email" name="email" value="{{ old('email') }}" required autocomplete="email" dir="ltr" @if(! old('email')) autofocus @endif>
        </label>
        <label>
            {{ __('ui.auth.password') }}
            <x-ui.password required />
        </label>
        <label class="check-row"><input type="checkbox" name="remember" value="1"><span>{{ __('ui.auth.remember') }}</span></label>
        <x-ui.button type="submit" size="lg" icon="log-in" block>{{ __('ui.auth.submit_login') }}</x-ui.button>
    </form>

    <div class="auth-alt">
        <p>{{ __('ui.auth.no_account') }} <a href="{{ route('register') }}">{{ __('ui.auth.create_account') }}</a></p>
        <p>{{ __('ui.auth.seller_hint') }} <a href="{{ route('seller.register') }}">{{ __('ui.auth.seller_register') }}</a></p>
    </div>

    <x-slot:aside>
        <h2>{{ __('ui.auth.aside_title') }}</h2>
        <ul class="auth-points">
            <li><span><x-lucide-messages-square /></span><div><strong>{{ __('ui.auth.point_community') }}</strong><p>{{ __('ui.auth.point_community_text') }}</p></div></li>
            <li><span><x-lucide-package-search /></span><div><strong>{{ __('ui.auth.point_orders') }}</strong><p>{{ __('ui.auth.point_orders_text') }}</p></div></li>
            <li><span><x-lucide-layout-dashboard /></span><div><strong>{{ __('ui.auth.point_panels') }}</strong><p>{{ __('ui.auth.point_panels_text') }}</p></div></li>
        </ul>
    </x-slot:aside>
</x-auth-shell>
@endsection
