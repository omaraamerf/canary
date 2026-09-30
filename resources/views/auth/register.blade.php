@extends('layouts.app')

@section('title', __('ui.auth.register_title'))

@section('content')
<section class="success-page">
    <div class="success-panel auth-panel">
        <span class="success-icon"><i data-lucide="user-round-plus"></i></span>
        <h1>{{ __('ui.auth.register_title') }}</h1>
        <p>{{ __('ui.auth.register_lead') }}</p>

        @if($errors->any())
            <div class="form-errors">{{ $errors->first() }}</div>
        @endif

        <form class="tracking-form" method="post" action="{{ route('register.store') }}">
            @csrf
            <label>
                {{ __('ui.common.name') }}
                <input name="name" value="{{ old('name') }}" required autocomplete="name">
            </label>
            <label>
                {{ __('ui.common.email') }}
                <input type="email" name="email" value="{{ old('email') }}" required autocomplete="email" dir="ltr">
            </label>
            <label>
                {{ __('ui.common.phone') }} {{ __('ui.auth.optional') }}
                <input name="phone" value="{{ old('phone') }}" inputmode="tel" autocomplete="tel" dir="ltr">
            </label>
            <label>
                {{ __('ui.common.region') }} {{ __('ui.auth.optional') }}
                <select name="region_id">
                    <option value="">{{ __('ui.layout.all_regions') }}</option>
                    @foreach($regions as $region)
                        <option value="{{ $region->id }}" @selected((string) old('region_id') === (string) $region->id)>{{ $region->name }}</option>
                    @endforeach
                </select>
            </label>
            <label>
                {{ __('ui.auth.password') }}
                <input type="password" name="password" required autocomplete="new-password" dir="ltr">
            </label>
            <label>
                {{ __('ui.auth.password_confirmation') }}
                <input type="password" name="password_confirmation" required autocomplete="new-password" dir="ltr">
            </label>
            <button class="btn btn-primary btn-large" type="submit"><i data-lucide="user-round-plus"></i> {{ __('ui.auth.submit_register') }}</button>
        </form>

        <p class="auth-switch">{{ __('ui.auth.have_account') }} <a href="{{ route('login') }}">{{ __('ui.auth.submit_login') }}</a></p>
    </div>
</section>
@endsection
