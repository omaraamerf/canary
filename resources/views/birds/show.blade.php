@extends('layouts.app')
@section('title', $bird->title)
@section('body_class', $bird->status === 'available' ? 'has-action-bar' : '')
@section('content')
@php
    $images = $bird->media->where('type', 'image')->values();
    $slides = $images->isNotEmpty() ? $images->pluck('url') : collect([$bird->primary_image]);
    $videos = $bird->media->where('type', 'video');
    $profile = $bird->seller->sellerProfile;
    $isSeller = $bird->seller->hasRole(\App\Enums\UserRole::Seller->value);
    $sellerName = $isSeller ? ($profile?->display_name ?: $bird->seller->name) : __('ui.detail.canary_admin');
    $available = $bird->status === 'available';
    $specs = [
        ['venus-and-mars', __('ui.birds.sex'), __('ui.sex.'.$bird->sex)],
        ['calendar', __('ui.birds.hatch_year'), $bird->hatch_year ?: __('ui.common.unknown')],
        ['palette', __('ui.birds.color'), $bird->color],
        ['feather', __('ui.birds.molt'), $bird->molt_status ? __('ui.molt.'.$bird->molt_status) : __('ui.common.unspecified')],
        ['heart-handshake', __('ui.catalog.breeding'), is_null($bird->breeding_ready) ? __('ui.common.unknown') : ($bird->breeding_ready ? __('ui.molt.ready') : __('ui.catalog.not_ready'))],
        ['music', __('ui.birds.singing'), $bird->singing_status ? __('ui.singing.'.$bird->singing_status) : __('ui.common.unknown')],
    ];
    if ($bird->ring_number) {
        $specs[] = ['circle-dot', __('ui.birds.ring_number'), $bird->ring_number];
    }
@endphp
<div class="container detail-page">
    <nav class="breadcrumbs" aria-label="{{ __('ui.nav.home') }}"><a href="{{ route('home') }}">{{ __('ui.nav.home') }}</a><x-lucide-chevron-left class="dir-icon" /><a href="{{ route('birds.index') }}">{{ __('ui.birds.plural') }}</a><x-lucide-chevron-left class="dir-icon" /><span>{{ $bird->title }}</span></nav>

    <div class="detail-layout">
        <section class="detail-gallery" data-gallery aria-label="{{ $bird->title }}">
            <div class="gallery-stage">
                <div class="gallery-track" data-gallery-track tabindex="0">
                    @foreach($slides as $url)
                        <figure class="gallery-slide"><x-img :src="$url" :width="760" sizes="(max-width: 1000px) 100vw, 720px" :style="$loop->first ? 'view-transition-name: bird-photo' : null" :alt="__('ui.detail.photo', ['current' => $loop->iteration, 'total' => $slides->count()]).' — '.$bird->title" :fetchpriority="$loop->first ? 'high' : null" :loading="$loop->first ? null : 'lazy'" /></figure>
                    @endforeach
                </div>
                <span class="status-pill status-{{ $bird->status }}">{{ __('ui.bird_status.'.$bird->status) }}</span>
                <button type="button" class="gallery-enlarge icon-btn" data-gallery-enlarge aria-label="{{ __('ui.detail.enlarge') }}" title="{{ __('ui.detail.enlarge') }}"><x-lucide-maximize-2 /></button>
                @if($slides->count() > 1)
                    <button type="button" class="gallery-nav is-prev icon-btn" data-gallery-prev aria-label="{{ __('ui.detail.previous_photo') }}"><x-lucide-chevron-right class="dir-icon" /></button>
                    <button type="button" class="gallery-nav is-next icon-btn" data-gallery-next aria-label="{{ __('ui.detail.next_photo') }}"><x-lucide-chevron-left class="dir-icon" /></button>
                    <span class="gallery-counter" data-gallery-counter aria-live="polite" dir="ltr">1 / {{ $slides->count() }}</span>
                @endif
            </div>
            @if($slides->count() > 1)
                <div class="gallery-thumbs">
                    @foreach($slides as $url)
                        <button type="button" data-gallery-thumb="{{ $loop->index }}" @if($loop->first) aria-current="true" @endif aria-label="{{ __('ui.detail.photo', ['current' => $loop->iteration, 'total' => $slides->count()]) }}"><x-img :src="$url" :width="96" alt="" loading="lazy" /></button>
                    @endforeach
                </div>
            @endif
        </section>

        <aside class="detail-aside">
            <div class="summary-card">
                <span class="kicker">{{ $bird->breed->localized_name }}</span>
                <h1>{{ $bird->title }}</h1>
                <p class="detail-location"><span><x-lucide-map-pin />{{ $bird->location_label }}</span><span><x-lucide-truck />{{ $bird->delivery_type === 'delivery' ? __('ui.delivery.available') : __('ui.delivery.'.$bird->delivery_type) }}</span></p>
                <ul class="bird-traits">
                    @if($bird->singing_status === 'singing')<li class="is-highlight"><x-lucide-music />{{ __('ui.card.singing') }}</li>@endif
                    @if($bird->breeding_ready)<li class="is-highlight"><x-lucide-heart-handshake />{{ __('ui.card.breeding_ready') }}</li>@endif
                    @if($bird->ring_number)<li><x-lucide-circle-dot />{{ __('ui.card.ringed') }}</li>@endif
                    @if($videos->isNotEmpty())<li><x-lucide-play />{{ __('ui.card.video') }}</li>@endif
                </ul>
                <p class="detail-price"><span>{{ __('ui.detail.price') }}</span><strong>{{ number_format($bird->price) }}</strong> <span>{{ $bird->currency_label }}</span></p>
                @if($available)
                    <div class="summary-actions">
                        <x-ui.button :href="'#reserve'" size="lg" block icon="bookmark-check">{{ __('ui.detail.reserve') }}</x-ui.button>
                        @if($whatsappUrl)
                            <a class="btn btn-whatsapp btn-large btn-block" href="{{ $whatsappUrl }}" target="_blank" rel="noopener"><x-lucide-message-circle />{{ __('ui.detail.whatsapp_long') }}</a>
                        @endif
                    </div>
                @else
                    <div class="alert alert-warning">{{ __('ui.detail.unavailable') }}</div>
                @endif
                <div class="summary-tools">
                    <x-favorite-button :bird="$bird" with-label class="btn btn-ghost btn-sm" />
                    <button type="button" class="btn btn-ghost btn-sm" data-share data-share-title="{{ $bird->title }}" data-share-copied="{{ __('ui.detail.link_copied') }}"><x-lucide-share-2 />{{ __('ui.detail.share') }}</button>
                </div>
            </div>

            <div class="seller-card">
                <span class="seller-card-icon"><x-lucide-store /></span>
                <div>
                    <small>{{ __('ui.birds.seller') }}</small>
                    <strong>{{ $sellerName }} @if($profile?->approval_status === 'approved')<x-lucide-badge-check class="verified-icon" aria-label="{{ __('ui.card.verified') }}" />@endif</strong>
                    @if($isSeller)
                        <em>{{ __('ui.detail.seller_since', ['date' => $bird->seller->created_at->translatedFormat('F Y')]) }} · {{ __('ui.detail.seller_birds', ['count' => $sellerBirdsCount]) }}</em>
                    @endif
                </div>
                @if($isSeller)<a class="seller-card-link" href="{{ route('sellers.show', $bird->seller) }}" aria-label="{{ $sellerName }}"><x-lucide-arrow-left class="dir-icon" /></a>@endif
            </div>

            <div class="trust-card">
                <h2>{{ __('ui.detail.trust_title') }}</h2>
                <ul>
                    <li><x-lucide-check />{{ __('ui.detail.no_online_payment') }}</li>
                    <li><x-lucide-check />{{ __('ui.detail.saved_price') }}</li>
                    <li><x-lucide-check />{{ __('ui.detail.direct_confirmation') }}</li>
                </ul>
            </div>
        </aside>

        <div class="detail-main">
            @if($bird->description)
                <section class="detail-block">
                    <h2>{{ __('ui.detail.about') }}</h2>
                    <p class="detail-description">{{ $bird->description }}</p>
                </section>
            @endif

            <section class="detail-block">
                <h2>{{ __('ui.detail.specs') }}</h2>
                <dl class="spec-grid">
                    @foreach($specs as [$icon, $label, $value])
                        <div><x-dynamic-component :component="'lucide-'.$icon" /><dt>{{ $label }}</dt><dd>{{ $value }}</dd></div>
                    @endforeach
                </dl>
            </section>

            @if($videos->isNotEmpty())
                <section class="detail-block">
                    <h2>{{ __('ui.detail.video') }}</h2>
                    <div class="video-grid">
                        @foreach($videos as $video)
                            <div class="video-frame">@if($video->isCloudinary())<video src="{{ $video->url }}" controls preload="metadata" playsinline title="{{ __('ui.detail.video_title', ['title' => $bird->title]) }}"></video>@else<iframe src="{{ $video->embed_url }}" allow="autoplay" allowfullscreen title="{{ __('ui.detail.video_title', ['title' => $bird->title]) }}"></iframe>@endif<span class="video-brand-cover" aria-hidden="true"><x-lucide-bird /></span></div>
                        @endforeach
                    </div>
                </section>
            @endif

            @if($available)
                <section id="reserve" class="detail-block reserve-block">
                    <span class="kicker">{{ __('ui.detail.request_note') }}</span>
                    <h2>{{ __('ui.detail.request_title') }}</h2>
                    <p class="field-hint">{{ __('ui.detail.request_help') }}</p>
                    <form action="{{ route('orders.store', $bird) }}" method="post" class="reserve-form">@csrf
                        @if($errors->any())<div class="form-errors full">{{ $errors->first() }}</div>@endif
                        <label>{{ __('ui.detail.full_name') }}<input required name="buyer_name" value="{{ old('buyer_name', auth()->user()?->name) }}" autocomplete="name"></label>
                        <label>{{ __('ui.common.phone') }}<input required name="phone" value="{{ old('phone', auth()->user()?->phone) }}" inputmode="tel" autocomplete="tel" dir="ltr"></label>
                        <x-location-picker class="full location-picker-inline" :required="true" country-name="buyer_country_id" region-name="buyer_region_id" :country="auth()->user()?->country_id ?? app(\App\Support\MarketplaceLocation::class)->countryId()" :region="auth()->user()?->region_id ?? app(\App\Support\MarketplaceLocation::class)->regionId()" />
                        <div class="delivery-summary full"><span>{{ __('ui.detail.seller_delivery') }}</span><strong>{{ __('ui.delivery.'.$bird->delivery_type) }}</strong></div>
                        <label class="full">{{ __('ui.detail.notes') }}<textarea name="notes" rows="3" placeholder="{{ __('ui.detail.notes_placeholder') }}">{{ old('notes') }}</textarea></label>
                        <x-ui.button type="submit" size="lg" icon="send" class="full">{{ __('ui.detail.send') }}</x-ui.button>
                        <small class="full">{{ __('ui.detail.agreement') }}</small>
                    </form>
                </section>
            @endif
        </div>
    </div>
</div>

@if($relatedBirds->isNotEmpty())
    <section class="section-band section-muted">
        <div class="container">
            <div class="section-heading"><div><span class="kicker">{{ __('ui.detail.other_options') }}</span><h2>{{ __('ui.detail.similar') }}</h2></div></div>
            <div class="birds-rail">@foreach($relatedBirds as $related)<x-bird-card :bird="$related" />@endforeach</div>
        </div>
    </section>
@endif

@if($available)
    <div class="action-bar">
        <p class="detail-price"><strong>{{ number_format($bird->price) }}</strong> <span>{{ $bird->currency_label }}</span></p>
        @if($whatsappUrl)<a class="icon-btn btn-whatsapp" href="{{ $whatsappUrl }}" target="_blank" rel="noopener" aria-label="{{ __('ui.detail.whatsapp_long') }}"><x-lucide-message-circle /></a>@endif
        <x-ui.button :href="'#reserve'" icon="bookmark-check">{{ __('ui.detail.reserve') }}</x-ui.button>
    </div>
@endif

<dialog class="lightbox" id="gallery-lightbox" aria-label="{{ $bird->title }}">
    <form method="dialog"><x-ui.icon-button type="submit" icon="x" :label="__('ui.layout.close')" class="lightbox-close" /></form>
    <img alt="" data-lightbox-image>
</dialog>
@endsection
