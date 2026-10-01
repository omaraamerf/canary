@extends('layouts.app')
@section('title', __('ui.pages.policy.title'))
@section('content')
@php
    $sections = ['confirm' => 'circle-check', 'payment' => 'wallet', 'data' => 'shield-check', 'bird' => 'bird'];
@endphp
<section class="page-head"><div class="container">
    <span class="kicker">{{ __('ui.pages.policy.eyebrow') }}</span>
    <h1>{{ __('ui.pages.policy.heading') }}</h1>
    <p>{{ __('ui.pages.policy.lead') }}</p>
</div></section>

<section class="section-band"><div class="container policy-layout">
    <aside class="policy-side">
        <nav class="side-card" aria-label="{{ __('ui.pages.policy.contents') }}">
            <h2>{{ __('ui.pages.policy.contents') }}</h2>
            <div class="side-nav">
                @foreach($sections as $key => $icon)
                    <a href="#{{ $key }}"><x-dynamic-component :component="'lucide-'.$icon" />{{ __('ui.pages.policy.'.$key) }}</a>
                @endforeach
            </div>
        </nav>
        <div class="side-card side-card-cta">
            <x-lucide-package-search />
            <h2>{{ __('ui.pages.policy.track_title') }}</h2>
            <p>{{ __('ui.pages.policy.track_text') }}</p>
            <x-ui.button variant="dark" size="sm" :href="route('orders.track')">{{ __('ui.nav.track') }}</x-ui.button>
        </div>
    </aside>

    <div class="policy-sections">
        @foreach($sections as $key => $icon)
            <article class="policy-card" id="{{ $key }}">
                <span class="topic-icon"><x-dynamic-component :component="'lucide-'.$icon" /></span>
                <div>
                    <h2>{{ __('ui.pages.policy.'.$key) }}</h2>
                    <p>{{ __('ui.pages.policy.'.$key.'_text') }}</p>
                </div>
            </article>
        @endforeach
    </div>
</div></section>
@endsection
