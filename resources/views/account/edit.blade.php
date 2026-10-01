@extends('layouts.app')
@section('title', __('ui.account.settings_title'))
@section('no_region_prompt', '1')
@section('content')
<section class="page-head account-head"><div class="container">
    <nav class="breadcrumbs" aria-label="{{ __('ui.layout.breadcrumbs') }}"><a href="{{ route('account.show') }}">{{ __('ui.account.my_account') }}</a><x-lucide-chevron-left class="dir-icon" /><span>{{ __('ui.account.tab_settings') }}</span></nav>
    <h1>{{ __('ui.account.settings_title') }}</h1>
    <p>{{ __('ui.account.settings_lead') }}</p>
</div></section>

<section class="section-band account-body"><div class="container">
    @include('account.partials.tabs', ['current' => 'settings'])

    <div class="settings-layout">
        <nav class="side-card settings-nav" aria-label="{{ __('ui.account.settings_sections') }}">
            <div class="side-nav">
                <a href="#photo"><x-lucide-image />{{ __('ui.account.photo') }}</a>
                <a href="#personal"><x-lucide-user-round />{{ __('ui.account.personal') }}</a>
                <a href="#location"><x-lucide-map-pin />{{ __('ui.account.location') }}</a>
                <a href="#password"><x-lucide-lock-keyhole />{{ __('ui.account.change_password') }}</a>
            </div>
        </nav>

        <div class="settings-main">
            <form action="{{ route('account.update') }}" method="post" enctype="multipart/form-data" class="settings-card">
                @csrf @method('put')
                @if($errors->any())<div class="form-errors" role="alert">{{ $errors->first() }}</div>@endif

                <fieldset id="photo">
                    <legend>{{ __('ui.account.photo') }}</legend>
                    <div class="avatar-field">
                        <x-avatar :user="$user" size="xl" />
                        <div>
                            <x-image-picker name="avatar" :multiple="false" :label="__('ui.account.change_photo')" />
                            @if($user->avatar_url)<label class="check-row"><input type="checkbox" name="remove_avatar" value="1"><span>{{ __('ui.account.remove_photo') }}</span></label>@endif
                        </div>
                    </div>
                </fieldset>

                <fieldset id="personal">
                    <legend>{{ __('ui.account.personal') }}</legend>
                    <div class="settings-grid">
                        <label>{{ __('ui.common.name') }}<input name="name" value="{{ old('name', $user->name) }}" required maxlength="100" autocomplete="name"></label>
                        <label>{{ __('ui.common.email') }}<input type="email" name="email" value="{{ old('email', $user->email) }}" required dir="ltr" autocomplete="email"></label>
                        <label><span>{{ __('ui.common.phone') }} <small>{{ __('ui.auth.optional') }}</small></span><input name="phone" value="{{ old('phone', $user->phone) }}" inputmode="tel" dir="ltr" autocomplete="tel"></label>
                        <label class="full"><span>{{ __('ui.account.bio') }} <small>{{ __('ui.auth.optional') }}</small></span><textarea name="bio" rows="3" maxlength="600" placeholder="{{ __('ui.account.bio_placeholder') }}">{{ old('bio', $user->bio) }}</textarea></label>
                    </div>
                    @if($user->isSeller())<p class="community-muted">{{ __('ui.account.seller_note') }}</p>@endif
                </fieldset>

                <fieldset id="location">
                    <legend>{{ __('ui.account.location') }}</legend>
                    <x-location-picker class="settings-grid" :country="$user->country_id" :region="$user->region_id" />
                </fieldset>

                <div class="settings-actions">
                    <x-ui.button variant="outline" :href="route('account.show')">{{ __('ui.account.cancel') }}</x-ui.button>
                    <x-ui.button type="submit" icon="save">{{ __('ui.account.save') }}</x-ui.button>
                </div>
            </form>

            <form action="{{ route('account.password') }}" method="post" class="settings-card" id="password">
                @csrf @method('put')
                @if(session('password_success'))<div class="notice">{{ session('password_success') }}</div>@endif
                @if($errors->password->any())<div class="form-errors" role="alert">{{ $errors->password->first() }}</div>@endif
                <fieldset>
                    <legend>{{ __('ui.account.change_password') }}</legend>
                    <div class="settings-grid">
                        <label class="full">{{ __('ui.account.current_password') }}<x-ui.password name="current_password" required /></label>
                        <label>{{ __('ui.auth.password') }}<x-ui.password autocomplete="new-password" required minlength="8" /></label>
                        <label>{{ __('ui.auth.password_confirmation') }}<x-ui.password name="password_confirmation" autocomplete="new-password" required minlength="8" /></label>
                    </div>
                    <p class="field-hint">{{ __('ui.auth.password_hint') }}</p>
                </fieldset>
                <div class="settings-actions"><x-ui.button type="submit" variant="dark" icon="lock-keyhole">{{ __('ui.account.update_password') }}</x-ui.button></div>
            </form>
        </div>
    </div>
</div></section>
@endsection
