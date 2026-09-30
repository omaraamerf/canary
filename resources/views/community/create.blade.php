@extends('layouts.app')
@section('title', __('ui.community.create_title'))
@section('content')
<section class="detail-section"><div class="container narrow">
    <nav class="breadcrumbs"><a href="{{ route('community.index') }}">{{ __('ui.community.title') }}</a><i data-lucide="chevron-left"></i><span>{{ __('ui.community.create_title') }}</span></nav>
    <div class="section-heading"><div><span class="kicker">{{ __('ui.community.eyebrow') }}</span><h2>{{ __('ui.community.create_title') }}</h2><p class="community-muted">{{ __('ui.community.create_lead') }}</p></div></div>

    <div class="notice community-disclaimer"><i data-lucide="stethoscope"></i> {{ __('ui.community.disclaimer') }}</div>

    <form action="{{ route('community.store') }}" method="post" enctype="multipart/form-data" class="reserve-form community-form">
        @csrf
        @if($errors->any())<div class="form-errors full">{{ $errors->first() }}</div>@endif
        <label>{{ __('ui.community.category') }}
            <select name="category" required>
                @foreach(\App\Enums\PostCategory::options() as $value => $label)
                    <option value="{{ $value }}" @selected(old('category', request('category')) === $value)>{{ $label }}</option>
                @endforeach
            </select>
        </label>
        <label>{{ __('ui.community.breed') }}
            <select name="breed_id">
                <option value="">{{ __('ui.common.unspecified') }}</option>
                @foreach($breeds as $breed)<option value="{{ $breed->id }}" @selected((string) old('breed_id') === (string) $breed->id)>{{ $breed->localized_name }}</option>@endforeach
            </select>
        </label>
        <label class="full">{{ __('ui.community.post_title') }}<input name="title" value="{{ old('title') }}" required minlength="5" maxlength="150" placeholder="{{ __('ui.community.title_placeholder') }}"></label>
        <label class="full">{{ __('ui.community.body') }}<textarea name="body" rows="7" required minlength="10" maxlength="5000" placeholder="{{ __('ui.community.body_placeholder') }}">{{ old('body') }}</textarea></label>
        <x-location-picker class="full location-picker-inline" :country="auth()->user()->country_id" :region="auth()->user()->region_id" />
        <div class="full">
            <span class="field-label">{{ __('ui.community.images') }}</span>
            <x-image-picker name="images" :max="4" :label="__('ui.uploads.add_bird_photos')" />
        </div>
        <label class="full">{{ __('ui.community.video') }} <small>{{ __('ui.auth.optional') }}</small><input type="file" name="videos[]" accept="video/mp4,video/quicktime,video/webm"></label>
        <button class="btn btn-primary btn-large full" type="submit"><i data-lucide="send"></i> {{ __('ui.community.submit') }}</button>
    </form>
</div></section>
@endsection
