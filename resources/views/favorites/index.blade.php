@extends('layouts.app')
@section('title', __('ui.favorites.title'))
@section('content')
<section class="page-head"><div class="container">
    <span class="kicker">{{ __('ui.favorites.eyebrow') }}</span>
    <h1>{{ __('ui.favorites.title') }}</h1>
    <p>{{ __('ui.favorites.lead') }}</p>
</div></section>

<section class="section-band"><div class="container">
    {{-- Filled by resources/js/favorites.js from this browser's saved list. --}}
    <div class="birds-grid" data-favorites-list data-source="{{ route('favorites.cards') }}" aria-live="polite" aria-busy="true"></div>
    <div data-favorites-empty hidden>
        <x-ui.empty-state icon="heart" :title="__('ui.favorites.empty')" :text="__('ui.favorites.empty_help')">
            <x-ui.button :href="route('birds.index')">{{ __('ui.favorites.browse') }}</x-ui.button>
        </x-ui.empty-state>
    </div>
    <noscript><x-ui.empty-state icon="heart" :title="__('ui.favorites.empty')" :text="__('ui.favorites.needs_js')" /></noscript>
</div></section>
@endsection
