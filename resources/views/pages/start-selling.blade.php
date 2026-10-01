@extends('layouts.app')
@section('title', __('ui.pages.sell.title'))
@section('content')
<section class="static-hero"><div class="container static-hero-grid">
    <div>
        <span class="kicker">{{ __('ui.pages.sell.eyebrow') }}</span>
        <h1>{{ __('ui.pages.sell.heading') }}</h1>
        <p class="lead">{{ __('ui.pages.sell.lead') }}</p>
        <div class="static-hero-actions">
            @auth
                @if(auth()->user()->isSeller())
                    <x-ui.button :href="url('/seller')" size="lg" icon="layout-dashboard">{{ __('ui.nav.seller_panel') }}</x-ui.button>
                @endif
            @else
                <x-ui.button :href="route('seller.register')" size="lg" icon="store">{{ __('ui.pages.sell.register') }}</x-ui.button>
                <x-ui.button variant="outline" :href="route('login')" size="lg" icon="log-in">{{ __('ui.pages.sell.login') }}</x-ui.button>
            @endauth
        </div>
    </div>
    <figure class="static-hero-media">
        <img src="/images/birds/classic-canary.jpg" alt="" width="640" height="640" decoding="async">
        <figcaption class="hero-tags">
            <span><x-lucide-banknote />{{ __('ui.pages.sell.tag_currency') }}</span>
            <span><x-lucide-message-circle />{{ __('ui.pages.sell.tag_whatsapp') }}</span>
            <span><x-lucide-map-pin />{{ __('ui.pages.sell.tag_region') }}</span>
        </figcaption>
    </figure>
</div></section>

<section class="section-band"><div class="container">
    <div class="section-heading"><div><span class="kicker">{{ __('ui.pages.sell.steps_kicker') }}</span><h2>{{ __('ui.pages.sell.steps_title') }}</h2></div></div>
    <ol class="step-cards">
        @foreach(['create' => 'user-round-plus', 'add' => 'image-plus', 'follow' => 'package-check'] as $step => $icon)
            <li>
                <span class="step-cards-num">{{ $loop->iteration }}</span>
                <span class="topic-icon"><x-dynamic-component :component="'lucide-'.$icon" /></span>
                <h3>{{ __('ui.pages.sell.'.$step) }}</h3>
                <p>{{ __('ui.pages.sell.'.$step.'_text') }}</p>
            </li>
        @endforeach
    </ol>
</div></section>

<section class="section-band section-sunken"><div class="container">
    <div class="section-heading"><div><span class="kicker">{{ __('ui.pages.sell.features_kicker') }}</span><h2>{{ __('ui.pages.sell.features_title') }}</h2></div></div>
    <div class="feature-grid">
        @foreach(['panel' => 'layout-dashboard', 'currency' => 'banknote', 'whatsapp' => 'message-circle', 'media' => 'images', 'region' => 'map-pin', 'orders' => 'list-checks'] as $feature => $icon)
            <div class="feature-card">
                <span class="topic-icon"><x-dynamic-component :component="'lucide-'.$icon" /></span>
                <h3>{{ __('ui.pages.sell.feature_'.$feature) }}</h3>
                <p>{{ __('ui.pages.sell.feature_'.$feature.'_text') }}</p>
            </div>
        @endforeach
    </div>
</div></section>

<section class="section-band"><div class="container split-section">
    <div>
        <span class="kicker">{{ __('ui.pages.sell.faq_kicker') }}</span>
        <h2>{{ __('ui.pages.sell.faq_title') }}</h2>
        <p>{{ __('ui.pages.sell.faq_lead') }}</p>
        @guest<x-ui.button :href="route('seller.register')" icon="store">{{ __('ui.pages.sell.register') }}</x-ui.button>@endguest
    </div>
    <div class="faq-list">
        @foreach(['payment', 'contact', 'review'] as $question)
            <details class="faq-item" @if($loop->first) open @endif>
                <summary>{{ __('ui.pages.sell.faq_'.$question) }}<x-lucide-chevron-down class="chevron" /></summary>
                <p>{{ __('ui.pages.sell.faq_'.$question.'_text') }}</p>
            </details>
        @endforeach
    </div>
</div></section>
@endsection
