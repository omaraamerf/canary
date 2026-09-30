@extends('layouts.app')
@section('title', __('ui.home.title'))
@section('content')
<section class="home-intro">
    <div class="container home-intro-grid">
        <div class="intro-copy">
            <span class="eyebrow"><i data-lucide="shield-check"></i> {{ __('ui.home.eyebrow') }}</span>
            <h1>{{ __('ui.home.heading') }}</h1>
            <p>{{ __('ui.home.lead') }}</p>
            <form action="{{ route('birds.index') }}" class="search-bar">
                <i data-lucide="search"></i>
                <input type="search" name="q" placeholder="{{ __('ui.home.search_placeholder') }}" aria-label="{{ __('ui.home.search_label') }}">
                <button type="submit">{{ __('ui.common.search') }}</button>
            </form>
            <div class="trust-row"><span><i data-lucide="badge-check"></i> {{ __('ui.home.clear_data') }}</span><span><i data-lucide="phone"></i> {{ __('ui.home.direct_confirmation') }}</span></div>
        </div>
        @if($featuredBirds->first())
            <a class="featured-visual" href="{{ route('birds.show', $featuredBirds->first()) }}">
                <img src="{{ $featuredBirds->first()->primary_image }}" alt="{{ $featuredBirds->first()->title }}">
                <div><span>{{ __('ui.home.featured') }}</span><strong>{{ $featuredBirds->first()->title }}</strong><small>{{ number_format($featuredBirds->first()->price) }} {{ __('ui.common.currency_sar') }}</small></div>
            </a>
        @endif
    </div>
</section>

<section class="section-band">
    <div class="container">
        <div class="section-heading"><div><span class="kicker">{{ __('ui.home.available_now') }}</span><h2>{{ __('ui.home.ready') }}</h2></div><a class="text-link" href="{{ route('birds.index') }}">{{ __('ui.common.view_all') }} <i data-lucide="arrow-left"></i></a></div>
        <div class="birds-grid">@foreach($featuredBirds as $bird)<x-bird-card :bird="$bird" />@endforeach</div>
    </div>
</section>

<section class="section-band section-muted">
    <div class="container">
        <div class="section-heading"><div><span class="kicker">{{ __('ui.home.start_breed') }}</span><h2>{{ __('ui.home.popular_breeds') }}</h2></div></div>
        <div class="breed-list">
            @foreach($breeds as $breed)
                <a href="{{ route('birds.index', ['breed' => $breed->slug]) }}"><span>{{ sprintf('%02d', $loop->iteration) }}</span><div><strong>{{ $breed->localized_name }}</strong><small>{{ $breed->localized_description }}</small></div><b>{{ __('ui.home.bird_count', ['count' => $breed->birds_count]) }}</b><i data-lucide="arrow-up-left"></i></a>
            @endforeach
        </div>
    </div>
</section>

<section class="steps-band"><div class="container"><div class="section-heading"><div><span class="kicker">{{ __('ui.home.simple_request') }}</span><h2>{{ __('ui.home.steps_title') }}</h2></div></div><div class="steps-grid"><div><span>1</span><i data-lucide="scan-search"></i><h3>{{ __('ui.home.step1.title') }}</h3><p>{{ __('ui.home.step1.text') }}</p></div><div><span>2</span><i data-lucide="clipboard-pen-line"></i><h3>{{ __('ui.home.step2.title') }}</h3><p>{{ __('ui.home.step2.text') }}</p></div><div><span>3</span><i data-lucide="circle-check-big"></i><h3>{{ __('ui.home.step3.title') }}</h3><p>{{ __('ui.home.step3.text') }}</p></div></div></div></section>
@endsection
