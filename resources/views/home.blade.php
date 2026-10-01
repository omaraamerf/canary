@extends('layouts.app')
@section('title', __('ui.home.title'))
@section('content')
@php
    $mosaic = $featuredBirds->take(3);
    // Whole rows only: the grid is four columns wide (two on phones), so a part-filled row would leave a gap.
    $gridBirds = $featuredBirds->count() >= 4 ? $featuredBirds->take(intdiv($featuredBirds->count(), 4) * 4) : $featuredBirds;
@endphp
<section class="home-hero">
    <div class="container home-hero-grid">
        <div class="hero-copy">
            <span class="eyebrow"><x-lucide-shield-check /> {{ __('ui.home.eyebrow') }}</span>
            <h1>{{ __('ui.home.heading') }}</h1>
            <p class="hero-lead">{{ __('ui.home.lead') }}</p>
            <form action="{{ route('birds.index') }}" class="search-bar" role="search">
                <x-lucide-search />
                <input type="search" name="q" placeholder="{{ __('ui.home.search_placeholder') }}" aria-label="{{ __('ui.home.search_label') }}">
                <button type="submit">{{ __('ui.common.search') }}</button>
            </form>
            @if($breeds->isNotEmpty())
                <div class="quick-search">
                    <span>{{ __('ui.home.quick_search') }}</span>
                    @foreach($breeds->take(4) as $breed)
                        <x-ui.chip :href="route('birds.index', ['breed' => $breed->slug])">{{ $breed->localized_name }}</x-ui.chip>
                    @endforeach
                </div>
            @endif
            <dl class="hero-stats">
                <div><dt>{{ __('ui.home.stats_birds') }}</dt><dd>{{ number_format($stats['birds']) }}</dd></div>
                <div><dt>{{ __('ui.home.stats_sellers') }}</dt><dd>{{ number_format($stats['sellers']) }}</dd></div>
                <div><dt>{{ __('ui.home.stats_regions') }}</dt><dd>{{ number_format($stats['regions']) }}</dd></div>
            </dl>
        </div>
        @if($mosaic->isNotEmpty())
            <div @class(['hero-mosaic', 'is-single' => $mosaic->count() === 1])>
                @foreach($mosaic as $bird)
                    <a @class(['mosaic-tile', 'is-main' => $loop->first]) href="{{ route('birds.show', $bird) }}">
                        <x-img :src="$bird->primary_image" :width="640" sizes="(max-width: 1000px) 100vw, 560px" alt="" :fetchpriority="$loop->first ? 'high' : null" :loading="$loop->first ? null : 'lazy'" />
                        <span class="mosaic-caption">
                            @if($loop->first && $bird->featured)<small>{{ __('ui.home.featured') }}</small>@endif
                            <strong>{{ $bird->title }}</strong>
                            <span>{{ number_format($bird->price) }} {{ $bird->currency_label }}</span>
                        </span>
                    </a>
                @endforeach
            </div>
        @endif
    </div>
</section>

@if($featuredBirds->isNotEmpty())
    <section class="section-band">
        <div class="container">
            <div class="section-heading"><div><span class="kicker">{{ __('ui.home.available_now') }}</span><h2>{{ __('ui.home.ready') }}</h2></div><a class="text-link" href="{{ route('birds.index') }}">{{ __('ui.common.view_all') }} <x-lucide-arrow-left class="dir-icon" /></a></div>
            <div class="birds-grid home-birds">@foreach($gridBirds as $bird)<x-bird-card :bird="$bird" />@endforeach</div>
        </div>
    </section>
@endif

@if($breeds->isNotEmpty())
    <section class="section-band section-muted">
        <div class="container">
            <div class="section-heading"><div><span class="kicker">{{ __('ui.home.start_breed') }}</span><h2>{{ __('ui.home.popular_breeds') }}</h2></div></div>
            <div class="breed-grid">
                @foreach($breeds as $breed)
                    <a class="breed-tile" href="{{ route('birds.index', ['breed' => $breed->slug]) }}">
                        <span class="breed-tile-image">@if($breed->birds->first())<x-img :src="$breed->birds->first()->primary_image" :width="160" alt="" loading="lazy" />@else<x-lucide-bird />@endif</span>
                        <strong>{{ $breed->localized_name }}</strong>
                        <small>{{ __('ui.home.bird_count', ['count' => $breed->birds_count]) }}</small>
                    </a>
                @endforeach
            </div>
        </div>
    </section>
@endif

@if($regions->count() > 1)
    <section class="section-band">
        <div class="container">
            <div class="section-heading"><div><span class="kicker">{{ __('ui.home.by_region_kicker') }}</span><h2>{{ __('ui.home.by_region') }}</h2></div></div>
            <div class="region-grid">
                @foreach($regions as $region)
                    <a class="region-tile" href="{{ route('birds.index', ['country' => $region->country_id, 'region' => $region->id]) }}">
                        <x-lucide-map-pin />
                        <span><strong>{{ $region->name }}</strong><small>{{ $region->country?->localized_name }}</small></span>
                        <b>{{ $region->birds_count }}</b>
                    </a>
                @endforeach
            </div>
        </div>
    </section>
@endif

<section class="steps-band">
    <div class="container steps-layout">
        <div class="section-heading"><div><span class="kicker">{{ __('ui.home.simple_request') }}</span><h2>{{ __('ui.home.steps_title') }}</h2></div></div>
        <ol class="steps-grid">
            <li><x-lucide-scan-search /><div><h3>{{ __('ui.home.step1.title') }}</h3><p>{{ __('ui.home.step1.text') }}</p></div></li>
            <li><x-lucide-clipboard-pen-line /><div><h3>{{ __('ui.home.step2.title') }}</h3><p>{{ __('ui.home.step2.text') }}</p></div></li>
            <li><x-lucide-circle-check-big /><div><h3>{{ __('ui.home.step3.title') }}</h3><p>{{ __('ui.home.step3.text') }}</p></div></li>
        </ol>
    </div>
</section>

<section class="section-band">
    <div @class(['container', 'home-closing' => $articles->isNotEmpty()])>
        <div class="sell-card">
            <span class="sell-card-icon"><x-lucide-store /></span>
            <span class="kicker">{{ __('ui.home.sell_kicker') }}</span>
            <h2>{{ __('ui.home.sell_title') }}</h2>
            <p>{{ __('ui.home.sell_text') }}</p>
            <x-ui.button :href="route('start-selling')" size="lg" icon-end="arrow-left" class="dir-icon-end">{{ __('ui.home.sell_cta') }}</x-ui.button>
        </div>
        @if($articles->isNotEmpty())
            <div class="home-guide">
                <div class="section-heading"><div><span class="kicker">{{ __('ui.home.guide_kicker') }}</span><h2>{{ __('ui.home.guide_title') }}</h2></div><a class="text-link" href="{{ route('guide.index') }}">{{ __('ui.common.view_all') }} <x-lucide-arrow-left class="dir-icon" /></a></div>
                <div class="guide-list">
                    @foreach($articles as $article)
                        <a class="guide-list-item" href="{{ route('guide.show', [$article->category, $article]) }}">
                            <x-img :src="$article->featured_image ?: '/images/birds/yellow-canary.jpg'" :width="320" alt="" loading="lazy" />
                            <span><small>{{ $article->category->name }}</small><strong>{{ $article->title }}</strong></span>
                        </a>
                    @endforeach
                </div>
            </div>
        @endif
    </div>
</section>
@endsection
