@extends('layouts.app')
@section('title', __('ui.community.create_title'))
@section('content')
@php
    $selected = \App\Enums\PostCategory::tryFrom((string) old('category', request('category'))) ?? \App\Enums\PostCategory::Health;
    $steps = [__('ui.community.step_type'), __('ui.community.step_details'), __('ui.community.step_media')];
@endphp
<div class="container post-page">
    <nav class="breadcrumbs" aria-label="{{ __('ui.nav.home') }}"><a href="{{ route('community.index') }}">{{ __('ui.community.title') }}</a><x-lucide-chevron-left class="dir-icon" /><span>{{ __('ui.community.create_title') }}</span></nav>

    <div class="post-layout">
        <div class="post-main">
            <header class="create-head">
                <span class="kicker">{{ __('ui.community.eyebrow') }}</span>
                <h1>{{ __('ui.community.create_title') }}</h1>
                <p>{{ __('ui.community.create_lead') }}</p>
            </header>

            <ol class="form-steps" data-step-indicator>
                @foreach($steps as $label)<li @class(['is-current' => $loop->first])><span class="form-step-num">{{ $loop->iteration }}</span><span class="form-step-label">{{ $label }}</span></li>@endforeach
            </ol>

            <form action="{{ route('community.store') }}" method="post" enctype="multipart/form-data" class="create-form" data-steps @if($errors->any()) data-error-field="{{ $errors->keys()[0] }}" @endif>
                @csrf
                @if($errors->any())<div class="form-errors">{{ $errors->first() }}</div>@endif

                <fieldset class="form-step" data-step>
                    <legend>{{ __('ui.community.step_type') }}</legend>
                    <div class="choice-grid">
                        @foreach(\App\Enums\PostCategory::cases() as $category)
                            <label class="choice-card">
                                <input type="radio" name="category" value="{{ $category->value }}" required @checked($selected === $category) data-preview-source="category" data-preview-label="{{ $category->label() }}" data-preview-class="post-category-{{ $category->value }}">
                                <span class="choice-icon post-category-{{ $category->value }}"><x-dynamic-component :component="'lucide-'.$category->icon()" /></span>
                                <strong>{{ $category->label() }}</strong>
                                <small>{{ $category->hint() }}</small>
                            </label>
                        @endforeach
                    </div>
                    <label>{{ __('ui.community.breed') }}
                        <select name="breed_id">
                            <option value="">{{ __('ui.common.unspecified') }}</option>
                            @foreach($breeds as $breed)<option value="{{ $breed->id }}" @selected((string) old('breed_id') === (string) $breed->id)>{{ $breed->localized_name }}</option>@endforeach
                        </select>
                    </label>
                </fieldset>

                <fieldset class="form-step" data-step>
                    <legend>{{ __('ui.community.step_details') }}</legend>
                    <label>{{ __('ui.community.post_title') }}<input name="title" value="{{ old('title') }}" required minlength="5" maxlength="150" placeholder="{{ __('ui.community.title_placeholder') }}" data-preview-source="title"></label>
                    <label>{{ __('ui.community.body') }}<textarea name="body" rows="8" required minlength="10" maxlength="5000" placeholder="{{ __('ui.community.body_placeholder') }}" data-preview-source="body">{{ old('body') }}</textarea></label>
                </fieldset>

                <fieldset class="form-step" data-step>
                    <legend>{{ __('ui.community.step_media') }}</legend>
                    <div>
                        <span class="field-label">{{ __('ui.community.images') }}</span>
                        <x-image-picker name="images" :max="4" :label="__('ui.uploads.add_bird_photos')" />
                    </div>
                    <label>{{ __('ui.community.video') }} <small>{{ __('ui.auth.optional') }}</small><input type="file" name="videos[]" accept="video/mp4,video/quicktime,video/webm"></label>
                    <x-location-picker class="location-picker-inline" :country="auth()->user()->country_id" :region="auth()->user()->region_id" />
                </fieldset>

                <div class="form-steps-nav">
                    <x-ui.button variant="outline" icon="chevron-right" class="dir-icon-start" data-step-prev hidden>{{ __('ui.community.back') }}</x-ui.button>
                    <span class="form-steps-count" data-step-count data-template="{{ __('ui.community.step_of', ['current' => ':current', 'total' => count($steps)]) }}" aria-live="polite"></span>
                    <x-ui.button variant="dark" icon-end="chevron-left" class="dir-icon-end" data-step-next hidden>{{ __('ui.community.next') }}</x-ui.button>
                    <x-ui.button type="submit" size="lg" icon="send" data-step-submit>{{ __('ui.community.submit') }}</x-ui.button>
                </div>
            </form>
        </div>

        <aside class="post-aside">
            <div class="side-card post-preview" aria-live="polite">
                <h2>{{ __('ui.community.preview') }}</h2>
                <span class="post-category post-category-{{ $selected->value }}" data-preview="category">{{ $selected->label() }}</span>
                <strong data-preview="title" data-placeholder="{{ __('ui.community.preview_title') }}">{{ old('title') ?: __('ui.community.preview_title') }}</strong>
                <p data-preview="body" data-placeholder="{{ __('ui.community.preview_body') }}">{{ old('body') ?: __('ui.community.preview_body') }}</p>
                <p class="post-row-meta"><span class="post-card-author"><x-avatar :user="auth()->user()" size="xs" />{{ auth()->user()->public_name }}</span></p>
            </div>
            <div class="side-card">
                <h2>{{ __('ui.community.how_title') }}</h2>
                <ul class="check-list">
                    <li><x-lucide-check />{{ __('ui.community.how_1') }}</li>
                    <li><x-lucide-check />{{ __('ui.community.how_2') }}</li>
                    <li><x-lucide-check />{{ __('ui.community.how_3') }}</li>
                </ul>
            </div>
            <div class="alert alert-warning"><x-lucide-stethoscope />{{ __('ui.community.disclaimer') }}</div>
        </aside>
    </div>
</div>
@endsection
