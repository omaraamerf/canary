@extends('layouts.app')
@section('title', __('ui.catalog.title'))
@section('content')
@php
    $sorts = ['newest' => __('ui.catalog.newest'), 'price_asc' => __('ui.catalog.lowest'), 'price_desc' => __('ui.catalog.highest')];
@endphp
<section class="catalog-head">
    <div class="container">
        <nav class="breadcrumbs" aria-label="{{ __('ui.nav.home') }}"><a href="{{ route('home') }}">{{ __('ui.nav.home') }}</a><x-lucide-chevron-left class="dir-icon" /><span>{{ __('ui.catalog.title') }}</span></nav>
        <div class="catalog-head-row">
            <div>
                <h1>{{ __('ui.catalog.title') }}</h1>
                <p class="catalog-count" data-results-count>{{ __('ui.home.bird_count', ['count' => number_format($birds->total())]) }}</p>
            </div>
            <div class="catalog-tools">
                <button type="button" class="btn btn-outline filters-toggle" popovertarget="filters-panel" aria-haspopup="dialog">
                    <x-lucide-sliders-horizontal />{{ __('ui.catalog.open_filters') }}
                    <span class="filters-count" data-filters-count @if($activeFilters->isEmpty()) hidden @endif>{{ $activeFilters->count() }}</span>
                </button>
                <label class="sort-select"><span class="sr-only">{{ __('ui.catalog.sort') }}</span>
                    <select name="sort" form="filters-form" aria-label="{{ __('ui.catalog.sort') }}">
                        @foreach($sorts as $value => $label)<option value="{{ $value }}" @selected(request('sort', 'newest') === $value)>{{ $label }}</option>@endforeach
                    </select>
                </label>
            </div>
        </div>
    </div>
</section>

<section class="catalog">
    <div class="container catalog-layout">
        <div id="filters-panel" class="filters" popover aria-labelledby="filters-title">
            <div class="filter-title">
                <h2 id="filters-title">{{ __('ui.catalog.filters') }}</h2>
                <a href="{{ route('birds.index') }}">{{ __('ui.catalog.clear_all') }}</a>
                <x-ui.icon-button icon="x" :label="__('ui.layout.close')" variant="ghost" class="filters-close" popovertarget="filters-panel" popovertargetaction="hide" />
            </div>
            <form id="filters-form" action="{{ route('birds.index') }}" method="get" class="filter-form" data-live-filter>
                <label>{{ __('ui.catalog.query') }}<input type="search" name="q" value="{{ request('q') }}" placeholder="{{ __('ui.catalog.query_placeholder') }}"></label>
                <label>{{ __('ui.birds.breed') }}<select name="breed"><option value="">{{ __('ui.catalog.all_breeds') }}</option>@foreach($breeds as $breed)<option value="{{ $breed->slug }}" @selected(request('breed') === $breed->slug)>{{ $breed->localized_name }}</option>@endforeach</select></label>
                <fieldset class="filter-group">
                    <legend>{{ __('ui.birds.sex') }}</legend>
                    <div class="segmented">
                        @foreach(['' => __('ui.common.all'), 'male' => __('ui.sex.male'), 'female' => __('ui.sex.female')] as $value => $label)
                            <label><input type="radio" name="sex" value="{{ $value }}" @checked((string) request('sex', '') === (string) $value)><span>{{ $label }}</span></label>
                        @endforeach
                    </div>
                </fieldset>
                <x-location-picker :show-optional="false" :use-old="false" country-name="country" region-name="region" :country="$activeCountryId" :region="$activeRegionId" :country-placeholder="__('ui.location.all_countries')" :region-placeholder="__('ui.location.all_country_regions')" />
                <fieldset class="filter-group">
                    <legend>{{ __('ui.catalog.price') }}</legend>
                    <div class="segmented">
                        <label><input type="radio" name="currency" value="" @checked(! $currency)><span>{{ __('ui.common.all') }}</span></label>
                        @foreach(\App\Enums\Currency::cases() as $option)
                            <label><input type="radio" name="currency" value="{{ $option->value }}" @checked($currency === $option)><span>{{ __('ui.currencies.'.$option->value) }}</span></label>
                        @endforeach
                    </div>
                    <div class="two-inputs" data-price-range>
                        <label>{{ __('ui.catalog.min_price') }}<input type="number" name="min_price" min="0" inputmode="numeric" value="{{ request('min_price') }}" @disabled(! $currency)></label>
                        <label>{{ __('ui.catalog.to') }}<input type="number" name="max_price" min="0" inputmode="numeric" value="{{ request('max_price') }}" @disabled(! $currency)></label>
                    </div>
                    <p class="field-hint" data-price-hint @if($currency) hidden @endif>{{ __('ui.catalog.price_needs_currency') }}</p>
                </fieldset>
                <details class="filter-more" @if(request()->hasAny(['color', 'molt_status', 'singing_status', 'breeding_ready'])) open @endif>
                    <summary>{{ __('ui.catalog.more_filters') }}<x-lucide-chevron-down class="chevron" /></summary>
                    <div class="filter-more-body">
                        <label>{{ __('ui.birds.color') }}<input name="color" value="{{ request('color') }}" placeholder="{{ __('ui.catalog.color_example') }}"></label>
                        <label>{{ __('ui.birds.singing') }}<select name="singing_status"><option value="">{{ __('ui.common.all') }}</option>@foreach(['singing', 'not_singing', 'young', 'female'] as $status)<option value="{{ $status }}" @selected(request('singing_status') === $status)>{{ __('ui.singing.'.$status) }}</option>@endforeach</select></label>
                        <label>{{ __('ui.birds.molt') }}<select name="molt_status"><option value="">{{ __('ui.common.all') }}</option>@foreach(['ready', 'young', 'molting'] as $status)<option value="{{ $status }}" @selected(request('molt_status') === $status)>{{ __('ui.molt.'.$status) }}</option>@endforeach</select></label>
                        <label>{{ __('ui.catalog.breeding') }}<select name="breeding_ready"><option value="">{{ __('ui.common.all') }}</option><option value="1" @selected(request('breeding_ready') === '1')>{{ __('ui.molt.ready') }}</option><option value="0" @selected(request('breeding_ready') === '0')>{{ __('ui.catalog.not_ready') }}</option></select></label>
                    </div>
                </details>
                <label class="check-row"><input type="checkbox" name="delivery" value="1" @checked(request('delivery'))><span>{{ __('ui.catalog.delivery') }}</span></label>
                <label class="check-row"><input type="checkbox" name="all_regions" value="1" @checked(request('all_regions'))><span>{{ __('ui.catalog.all_regions') }}</span></label>
                <x-ui.button type="submit" block icon="list-filter" class="filters-submit">{{ __('ui.catalog.show_results') }}</x-ui.button>
            </form>
        </div>

        <div class="catalog-results" data-results aria-live="polite">
            @if($activeFilters->isNotEmpty())
                <div class="active-filters" aria-label="{{ __('ui.catalog.filters') }}">
                    @foreach($activeFilters as $filter)
                        <x-ui.chip :href="$filter['url']" icon-end="x" :aria-label="__('ui.catalog.remove_filter', ['filter' => $filter['label']])">{{ $filter['label'] }}</x-ui.chip>
                    @endforeach
                    <a class="text-link" href="{{ route('birds.index') }}">{{ __('ui.catalog.clear_all') }}</a>
                </div>
            @endif
            @if($birds->count())
                <div class="birds-grid catalog-grid">@foreach($birds as $bird)<x-bird-card :bird="$bird" />@endforeach</div>
                {{ $birds->links() }}
            @else
                <x-ui.empty-state icon="bird" :title="__('ui.catalog.empty')" :text="__('ui.catalog.empty_help')"><x-ui.button variant="dark" :href="route('birds.index')">{{ __('ui.catalog.clear_all') }}</x-ui.button></x-ui.empty-state>
            @endif
        </div>
    </div>
</section>
@endsection
