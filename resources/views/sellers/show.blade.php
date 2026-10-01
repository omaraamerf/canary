@extends('layouts.app')
@php
    $profile = $seller->sellerProfile;
    $name = $profile?->display_name ?: $seller->name;
@endphp
@section('title', $name)
@section('content')
<section class="seller-hero">
    <div class="container">
        <nav class="breadcrumbs" aria-label="{{ __('ui.nav.home') }}"><a href="{{ route('home') }}">{{ __('ui.nav.home') }}</a><x-lucide-chevron-left class="dir-icon" /><a href="{{ route('birds.index') }}">{{ __('ui.birds.plural') }}</a><x-lucide-chevron-left class="dir-icon" /><span>{{ $name }}</span></nav>
        <div class="seller-hero-card">
            <span class="seller-hero-icon"><x-lucide-store /></span>
            <div class="seller-hero-body">
                <span class="kicker">{{ __('ui.seller.public_label') }}</span>
                <h1>{{ $name }} @if($profile?->approval_status === 'approved')<x-ui.badge variant="success" icon="badge-check">{{ __('ui.card.verified') }}</x-ui.badge>@endif</h1>
                <p class="seller-meta">
                    @if($profile?->region)<span><x-lucide-map-pin />{{ $profile->region->full_name }}</span>@endif
                    <span><x-lucide-calendar />{{ __('ui.detail.seller_since', ['date' => $seller->created_at->translatedFormat('F Y')]) }}</span>
                    <span><x-lucide-bird />{{ __('ui.detail.seller_birds', ['count' => $birds->total()]) }}</span>
                </p>
                @if($profile?->bio)<p class="seller-bio">{{ $profile->bio }}</p>@endif
            </div>
            @if($whatsappUrl)
                <div class="seller-hero-actions"><a class="btn btn-whatsapp btn-large" href="{{ $whatsappUrl }}" target="_blank" rel="noopener"><x-lucide-message-circle />{{ __('ui.detail.whatsapp_long') }}</a></div>
            @endif
        </div>
    </div>
</section>
<section class="section-band">
    <div class="container">
        <div class="section-heading"><div><span class="kicker">{{ __('ui.home.available_now') }}</span><h2>{{ __('ui.seller.birds') }}</h2></div></div>
        @if($birds->count())
            <div class="birds-grid">@foreach($birds as $bird)<x-bird-card :bird="$bird" />@endforeach</div>
            {{ $birds->links() }}
        @else
            <x-ui.empty-state icon="bird" :title="__('ui.seller.no_birds')" />
        @endif
    </div>
</section>
@endsection
