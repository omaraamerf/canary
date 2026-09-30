@extends('layouts.app')
@section('title', __('ui.catalog.title'))
@section('content')
<section class="page-head"><div class="container"><span class="kicker">{{ __('ui.catalog.eyebrow') }}</span><h1>{{ __('ui.catalog.title') }}</h1><p>{{ __('ui.catalog.lead') }}</p></div></section>
<section class="section-band pt-8"><div class="container catalog-layout">
    <aside class="filters">
        <div class="filter-title"><h2>{{ __('ui.catalog.filters') }}</h2><a href="{{ route('birds.index') }}">{{ __('ui.common.clear') }}</a></div>
        <form action="{{ route('birds.index') }}" method="get" class="filter-form">
            <label>{{ __('ui.catalog.query') }}<input type="search" name="q" value="{{ request('q') }}" placeholder="{{ __('ui.catalog.query_placeholder') }}"></label>
            <label>{{ __('ui.birds.breed') }}<select name="breed"><option value="">{{ __('ui.catalog.all_breeds') }}</option>@foreach($breeds as $breed)<option value="{{ $breed->slug }}" @selected(request('breed') === $breed->slug)>{{ $breed->localized_name }}</option>@endforeach</select></label>
            <label>{{ __('ui.birds.sex') }}<select name="sex"><option value="">{{ __('ui.common.all') }}</option>@foreach(['male', 'female', 'unknown'] as $sex)<option value="{{ $sex }}" @selected(request('sex') === $sex)>{{ __('ui.sex.'.$sex) }}</option>@endforeach</select></label>
<x-location-picker :show-optional="false" :use-old="false" country-name="country" region-name="region" :country="$activeCountryId" :region="$activeRegionId" :country-placeholder="__('ui.location.all_countries')" :region-placeholder="__('ui.location.all_country_regions')" />
            <label>{{ __('ui.birds.color') }}<input name="color" value="{{ request('color') }}" placeholder="{{ __('ui.catalog.color_example') }}"></label>
            <label>{{ __('ui.birds.molt') }}<select name="molt_status"><option value="">{{ __('ui.common.all') }}</option>@foreach(['ready', 'young', 'molting'] as $status)<option value="{{ $status }}" @selected(request('molt_status')===$status)>{{ __('ui.molt.'.$status) }}</option>@endforeach</select></label>
            <label>{{ __('ui.birds.singing') }}<select name="singing_status"><option value="">{{ __('ui.common.all') }}</option>@foreach(['singing', 'not_singing', 'young', 'female'] as $status)<option value="{{ $status }}" @selected(request('singing_status')===$status)>{{ __('ui.singing.'.$status) }}</option>@endforeach</select></label>
            <label>{{ __('ui.catalog.breeding') }}<select name="breeding_ready"><option value="">{{ __('ui.common.all') }}</option><option value="1" @selected(request('breeding_ready')==='1')>{{ __('ui.molt.ready') }}</option><option value="0" @selected(request('breeding_ready')==='0')>{{ __('ui.catalog.not_ready') }}</option></select></label>
            <div class="two-inputs"><label>{{ __('ui.catalog.min_price') }}<input type="number" name="min_price" value="{{ request('min_price') }}"></label><label>{{ __('ui.catalog.to') }}<input type="number" name="max_price" value="{{ request('max_price') }}"></label></div>
            <label class="check-row"><input type="checkbox" name="delivery" value="1" @checked(request('delivery'))><span>{{ __('ui.catalog.delivery') }}</span></label>
            <label class="check-row"><input type="checkbox" name="all_regions" value="1" @checked(request('all_regions'))><span>{{ __('ui.catalog.all_regions') }}</span></label>
            <input type="hidden" name="sort" value="{{ request('sort') }}"><button class="btn btn-primary w-full" type="submit"><i data-lucide="list-filter"></i> {{ __('ui.catalog.apply') }}</button>
        </form>
    </aside>
    <div>
        <div class="results-toolbar"><span><strong>{{ $birds->total() }}</strong> {{ __('ui.common.results') }}</span><form>@foreach(request()->except('sort','page') as $key=>$value)@if(is_scalar($value))<input type="hidden" name="{{ $key }}" value="{{ $value }}">@endif @endforeach<select name="sort" onchange="this.form.submit()" aria-label="{{ __('ui.catalog.sort') }}"><option value="newest">{{ __('ui.catalog.newest') }}</option><option value="price_asc" @selected(request('sort') === 'price_asc')>{{ __('ui.catalog.lowest') }}</option><option value="price_desc" @selected(request('sort') === 'price_desc')>{{ __('ui.catalog.highest') }}</option></select></form></div>
        @if($birds->count())<div class="birds-grid catalog-grid">@foreach($birds as $bird)<x-bird-card :bird="$bird" />@endforeach</div>{{ $birds->links() }}@else<div class="empty-state"><i data-lucide="bird"></i><h2>{{ __('ui.catalog.empty') }}</h2><p>{{ __('ui.catalog.empty_help') }}</p><a class="btn btn-dark" href="{{ route('birds.index') }}">{{ __('ui.nav.birds') }}</a></div>@endif
    </div>
</div></section>
@endsection
