@extends('layouts.app')
@section('title', __('ui.community.title'))
@section('meta_description', __('ui.community.description'))
@section('content')
@php
    $states = ['' => __('ui.community.all_states'), 'open' => __('ui.community.open'), 'solved' => __('ui.community.solved')];
    $activeCategory = \App\Enums\PostCategory::tryFrom((string) request('category'));
@endphp
<section class="guide-head community-head">
    <div class="container guide-head-grid">
        <div>
            <span class="kicker">{{ __('ui.community.eyebrow') }}</span>
            <h1>{{ __('ui.community.title') }}</h1>
            <p>{{ __('ui.community.lead') }}</p>
        </div>
        <div class="community-head-side">
            <dl class="hero-stats">
                <div><dt>{{ __('ui.community.stats_posts') }}</dt><dd>{{ number_format($stats['posts']) }}</dd></div>
                <div><dt>{{ __('ui.community.stats_solved') }}</dt><dd>{{ number_format($stats['solved']) }}</dd></div>
            </dl>
            <x-ui.button :href="route('community.create')" size="lg" icon="message-circle-question" id="community-ask">{{ __('ui.community.new_post') }}</x-ui.button>
        </div>
    </div>
</section>

<section class="section-band">
    <div class="container community-layout">
        <div class="community-main">
            <form action="{{ route('community.index') }}" method="get" class="search-bar community-search" role="search">
                @foreach(request()->only(['category', 'state', 'breed', 'country', 'region']) as $key => $value)<input type="hidden" name="{{ $key }}" value="{{ $value }}">@endforeach
                <x-lucide-search />
                <input type="search" name="q" value="{{ request('q') }}" placeholder="{{ __('ui.community.search_placeholder') }}" aria-label="{{ __('ui.community.query') }}">
                <button type="submit">{{ __('ui.common.search') }}</button>
            </form>
            <nav class="chip-row" aria-label="{{ __('ui.community.category') }}">
                <x-ui.chip :href="request()->fullUrlWithoutQuery(['category', 'page'])" :active="! $activeCategory">{{ __('ui.community.all_categories') }}</x-ui.chip>
                @foreach(\App\Enums\PostCategory::cases() as $category)
                    <x-ui.chip :href="request()->fullUrlWithQuery(['category' => $category->value, 'page' => null])" :active="$activeCategory === $category" :icon="$category->icon()">{{ $category->label() }}</x-ui.chip>
                @endforeach
            </nav>
            <div class="community-toolbar">
                <nav class="tabs" aria-label="{{ __('ui.community.state') }}">
                    @foreach($states as $value => $label)
                        <a @class(['tab', 'is-active' => (string) request('state', '') === (string) $value]) href="{{ $value === '' ? request()->fullUrlWithoutQuery(['state', 'page']) : request()->fullUrlWithQuery(['state' => $value, 'page' => null]) }}" @if((string) request('state', '') === (string) $value) aria-current="page" @endif>{{ $label }}</a>
                    @endforeach
                </nav>
                <span class="catalog-count">{{ $posts->total() }} {{ __('ui.common.results') }}</span>
            </div>
            @if($posts->count())
                <h2 class="sr-only">{{ __('ui.community.list_heading') }}</h2>
                <div class="post-list">@foreach($posts as $post)<x-post-card :post="$post" />@endforeach</div>
                {{ $posts->links() }}
            @else
                <x-ui.empty-state icon="message-circle-question" :title="__('ui.community.empty')" :text="__('ui.community.empty_help')"><x-ui.button variant="dark" :href="route('community.create')">{{ __('ui.community.new_post') }}</x-ui.button></x-ui.empty-state>
            @endif
        </div>

        <aside class="community-aside">
            <details class="side-card filter-details" @if(request()->hasAny(['breed', 'country', 'region'])) open @endif>
                <summary><span>{{ __('ui.community.advanced_filters') }}</span><x-lucide-chevron-down class="chevron" /></summary>
                <form action="{{ route('community.index') }}" method="get" class="filter-form">
                    @foreach(request()->only(['q', 'category', 'state']) as $key => $value)<input type="hidden" name="{{ $key }}" value="{{ $value }}">@endforeach
                    <label>{{ __('ui.birds.breed') }}<select name="breed"><option value="">{{ __('ui.community.all_breeds') }}</option>@foreach($breeds as $breed)<option value="{{ $breed->slug }}" @selected(request('breed') === $breed->slug)>{{ $breed->localized_name }}</option>@endforeach</select></label>
                    <x-location-picker :show-optional="false" :use-old="false" country-name="country" region-name="region" :country="request('country')" :region="request('region')" :country-placeholder="__('ui.location.all_countries')" :region-placeholder="__('ui.location.all_country_regions')" />
                    <x-ui.button type="submit" block icon="list-filter">{{ __('ui.community.apply') }}</x-ui.button>
                </form>
            </details>
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
</section>
<a class="fab" href="{{ route('community.create') }}" aria-label="{{ __('ui.community.new_post') }}" data-fab-after="community-ask"><x-lucide-message-circle-plus /><span>{{ __('ui.community.new_post') }}</span></a>
@endsection
