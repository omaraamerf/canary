@extends('layouts.app')
@section('title', __('ui.community.title'))
@section('meta_description', __('ui.community.description'))
@section('content')
<section class="guide-hero community-hero"><div class="container">
    <span class="kicker">{{ __('ui.community.eyebrow') }}</span>
    <h1>{{ __('ui.community.title') }}</h1>
    <p>{{ __('ui.community.lead') }}</p>
    <a class="btn btn-primary btn-large" href="{{ route('community.create') }}"><x-lucide-message-circle-question /> {{ __('ui.community.new_post') }}</a>
</div></section>

<section class="section-band pt-8"><div class="container catalog-layout">
    <aside class="filters">
        <div class="filter-title"><h2>{{ __('ui.community.filters') }}</h2><a href="{{ route('community.index') }}">{{ __('ui.common.clear') }}</a></div>
        <form action="{{ route('community.index') }}" method="get" class="filter-form">
            <label>{{ __('ui.community.query') }}<input type="search" name="q" value="{{ request('q') }}"></label>
            <label>{{ __('ui.community.category') }}<select name="category"><option value="">{{ __('ui.community.all_categories') }}</option>@foreach(\App\Enums\PostCategory::options() as $value => $label)<option value="{{ $value }}" @selected(request('category') === $value)>{{ $label }}</option>@endforeach</select></label>
            <label>{{ __('ui.birds.breed') }}<select name="breed"><option value="">{{ __('ui.community.all_breeds') }}</option>@foreach($breeds as $breed)<option value="{{ $breed->slug }}" @selected(request('breed') === $breed->slug)>{{ $breed->localized_name }}</option>@endforeach</select></label>
            <x-location-picker :show-optional="false" :use-old="false" country-name="country" region-name="region" :country="request('country')" :region="request('region')" :country-placeholder="__('ui.location.all_countries')" :region-placeholder="__('ui.location.all_country_regions')" />
            <label>{{ __('ui.community.state') }}<select name="state"><option value="">{{ __('ui.common.all') }}</option><option value="open" @selected(request('state') === 'open')>{{ __('ui.community.open') }}</option><option value="solved" @selected(request('state') === 'solved')>{{ __('ui.community.solved') }}</option></select></label>
            <button class="btn btn-primary w-full" type="submit"><x-lucide-list-filter /> {{ __('ui.community.apply') }}</button>
        </form>
    </aside>
    <div>
        <div class="results-toolbar"><span><strong>{{ $posts->total() }}</strong> {{ __('ui.common.results') }}</span></div>
        @if($posts->count())
            <div class="post-list">@foreach($posts as $post)<x-post-card :post="$post" />@endforeach</div>
            {{ $posts->links() }}
        @else
            <x-ui.empty-state icon="message-circle-question" :title="__('ui.community.empty')" :text="__('ui.community.empty_help')"><x-ui.button variant="dark" :href="route('community.create')">{{ __('ui.community.new_post') }}</x-ui.button></x-ui.empty-state>
        @endif
    </div>
</div></section>
@endsection
