@extends('layouts.app')
@section('title', __('ui.pages.about.title'))
@section('content')
<section class="static-hero"><div class="container static-hero-grid">
    <div>
        <span class="kicker">{{ __('ui.pages.about.eyebrow') }}</span>
        <h1>{{ __('ui.pages.about.heading') }}</h1>
        <p class="lead">{{ __('ui.pages.about.lead') }}</p>
        <div class="static-hero-actions">
            <x-ui.button :href="route('birds.index')" size="lg" icon="bird">{{ __('ui.pages.about.cta_browse') }}</x-ui.button>
            <x-ui.button variant="outline" :href="route('start-selling')" size="lg" icon="store">{{ __('ui.pages.about.cta_sell') }}</x-ui.button>
        </div>
    </div>
    <figure class="static-hero-media"><img src="/images/birds/yellow-canary.jpg" alt="" width="640" height="640" decoding="async"></figure>
</div></section>

<section class="section-band"><div class="container">
    <div class="section-heading"><div><span class="kicker">{{ __('ui.pages.about.what') }}</span><h2>{{ __('ui.pages.about.what_heading') }}</h2></div></div>
    <p class="static-intro">{{ __('ui.pages.about.what_text') }}</p>
    <div class="feature-grid feature-grid-3">
        <a class="feature-card" href="{{ route('birds.index') }}">
            <span class="topic-icon"><x-lucide-bird /></span>
            <h3>{{ __('ui.pages.about.market') }}</h3>
            <p>{{ __('ui.pages.about.market_text') }}</p>
        </a>
        @if($guideEnabled)
            <a class="feature-card" href="{{ route('guide.index') }}">
                <span class="topic-icon"><x-lucide-book-open /></span>
                <h3>{{ __('ui.pages.about.guide') }}</h3>
                <p>{{ __('ui.pages.about.guide_text') }}</p>
            </a>
        @endif
        @if($communityEnabled)
            <a class="feature-card" href="{{ route('community.index') }}">
                <span class="topic-icon"><x-lucide-messages-square /></span>
                <h3>{{ __('ui.pages.about.community') }}</h3>
                <p>{{ __('ui.pages.about.community_text') }}</p>
            </a>
        @endif
    </div>
</div></section>

<section class="section-band section-sunken"><div class="container split-section">
    <div>
        <span class="kicker">{{ __('ui.pages.about.trust') }}</span>
        <h2>{{ __('ui.pages.about.how') }}</h2>
        <p>{{ __('ui.pages.about.how_text') }}</p>
    </div>
    <ul class="check-list">
        @foreach(['breed', 'condition', 'price', 'media'] as $item)
            <li><x-lucide-circle-check /><div><strong>{{ __('ui.pages.about.check_'.$item) }}</strong><p>{{ __('ui.pages.about.check_'.$item.'_text') }}</p></div></li>
        @endforeach
    </ul>
</div></section>

<section class="section-band"><div class="container">
    <div class="cta-pair">
        <div class="cta-card">
            <x-lucide-search />
            <h2>{{ __('ui.pages.about.buyer_title') }}</h2>
            <p>{{ __('ui.pages.about.buyer_text') }}</p>
            <x-ui.button :href="route('birds.index')">{{ __('ui.pages.about.cta_browse') }}</x-ui.button>
        </div>
        <div class="cta-card cta-card-strong">
            <x-lucide-store />
            <h2>{{ __('ui.pages.about.seller_title') }}</h2>
            <p>{{ __('ui.pages.about.seller_text') }}</p>
            <x-ui.button variant="outline" :href="route('start-selling')">{{ __('ui.pages.about.cta_sell') }}</x-ui.button>
        </div>
    </div>
</div></section>
@endsection
