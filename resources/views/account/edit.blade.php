@extends('layouts.app')
@section('title', __('ui.account.edit'))
@section('content')
<section class="detail-section"><div class="container narrow">
    <nav class="breadcrumbs"><a href="{{ route('account.show') }}">{{ __('ui.account.my_account') }}</a><i data-lucide="chevron-left"></i><span>{{ __('ui.account.edit') }}</span></nav>
    <div class="section-heading"><div><span class="kicker">{{ __('ui.account.my_account') }}</span><h2>{{ __('ui.account.edit') }}</h2></div></div>

    <form action="{{ route('account.update') }}" method="post" enctype="multipart/form-data" class="settings-card">
        @csrf @method('put')
        @if($errors->any())<div class="form-errors">{{ $errors->first() }}</div>@endif

        <fieldset>
            <legend>{{ __('ui.account.photo') }}</legend>
            <div class="avatar-field">
                <x-avatar :user="$user" size="xl" />
                <div>
                    <x-image-picker name="avatar" :multiple="false" :label="__('ui.account.change_photo')" />
                    @if($user->avatar_url)<label class="check-row"><input type="checkbox" name="remove_avatar" value="1"><span>{{ __('ui.account.remove_photo') }}</span></label>@endif
                </div>
            </div>
        </fieldset>

        <fieldset>
            <legend>{{ __('ui.account.personal') }}</legend>
            <div class="settings-grid">
                <label>{{ __('ui.common.name') }}<input name="name" value="{{ old('name', $user->name) }}" required maxlength="100" autocomplete="name"></label>
                <label>{{ __('ui.common.email') }}<input type="email" name="email" value="{{ old('email', $user->email) }}" required dir="ltr" autocomplete="email"></label>
                <label>{{ __('ui.common.phone') }} <small>{{ __('ui.auth.optional') }}</small><input name="phone" value="{{ old('phone', $user->phone) }}" inputmode="tel" dir="ltr" autocomplete="tel"></label>
                <label class="full">{{ __('ui.account.bio') }} <small>{{ __('ui.auth.optional') }}</small><textarea name="bio" rows="3" maxlength="600" placeholder="{{ __('ui.account.bio_placeholder') }}">{{ old('bio', $user->bio) }}</textarea></label>
            </div>
            @if($user->isSeller())<p class="community-muted">{{ __('ui.account.seller_note') }}</p>@endif
        </fieldset>

        <fieldset>
            <legend>{{ __('ui.account.location') }}</legend>
            <x-location-picker class="settings-grid" :country="$user->country_id" :region="$user->region_id" />
        </fieldset>

        <div class="settings-actions">
            <a class="btn btn-outline" href="{{ route('account.show') }}">{{ __('ui.account.cancel') }}</a>
            <button class="btn btn-primary" type="submit"><i data-lucide="save"></i>{{ __('ui.account.save') }}</button>
        </div>
    </form>

    <form action="{{ route('account.password') }}" method="post" class="settings-card" id="password">
        @csrf @method('put')
        @if(session('password_success'))<div class="notice">{{ session('password_success') }}</div>@endif
        @if($errors->password->any())<div class="form-errors">{{ $errors->password->first() }}</div>@endif
        <fieldset>
            <legend>{{ __('ui.account.change_password') }}</legend>
            <div class="settings-grid">
                <label class="full">{{ __('ui.account.current_password') }}<input type="password" name="current_password" required autocomplete="current-password" dir="ltr"></label>
                <label>{{ __('ui.auth.password') }}<input type="password" name="password" required autocomplete="new-password" dir="ltr"></label>
                <label>{{ __('ui.auth.password_confirmation') }}<input type="password" name="password_confirmation" required autocomplete="new-password" dir="ltr"></label>
            </div>
        </fieldset>
        <div class="settings-actions"><button class="btn btn-dark" type="submit"><i data-lucide="lock-keyhole"></i>{{ __('ui.account.update_password') }}</button></div>
    </form>
</div></section>
@endsection
