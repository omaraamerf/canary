@extends('layouts.app')
@section('title', $bird->title)
@section('content')
<section class="detail-section"><div class="container">
    <nav class="breadcrumbs"><a href="{{ route('home') }}">{{ __('ui.nav.home') }}</a><i data-lucide="chevron-left"></i><a href="{{ route('birds.index') }}">{{ __('ui.birds.plural') }}</a><i data-lucide="chevron-left"></i><span>{{ $bird->title }}</span></nav>
    <div class="detail-grid">
        <div class="gallery">
            <div class="gallery-main"><img data-gallery-main src="{{ $bird->primary_image }}" alt="{{ $bird->title }}"><span class="status-pill status-{{ $bird->status }}">{{ __('ui.bird_status.'.$bird->status) }}</span></div>
            @if($bird->media->where('type', 'image')->count() > 1)<div class="gallery-thumbs">@foreach($bird->media->where('type', 'image') as $media)<button data-gallery-image="{{ $media->url }}"><img src="{{ $media->url }}" alt="{{ __('ui.detail.extra_image') }}"></button>@endforeach</div>@endif
        </div>
        <div class="detail-info">
            <span class="kicker">{{ $bird->breed->localized_name }}</span><h1>{{ $bird->title }}</h1>
            <div class="detail-location"><span><i data-lucide="map-pin"></i>{{ $bird->city }}@if($bird->region)، {{ $bird->region->name }}@endif</span><span><i data-lucide="truck"></i>{{ $bird->delivery_type === 'delivery' ? __('ui.delivery.available') : __('ui.delivery.'.$bird->delivery_type) }}</span></div>
            <div class="price-line"><strong>{{ number_format($bird->price) }}</strong><span>{{ __('ui.common.currency_sar') }}</span></div>
            <p class="detail-description">{{ $bird->description }}</p>
            <dl class="spec-grid">
                <div><dt>{{ __('ui.birds.sex') }}</dt><dd>{{ __('ui.sex.'.$bird->sex) }}</dd></div>
                <div><dt>{{ __('ui.birds.hatch_year') }}</dt><dd>{{ $bird->hatch_year ?: __('ui.common.unknown') }}</dd></div>
                <div><dt>{{ __('ui.birds.color') }}</dt><dd>{{ $bird->color }}</dd></div>
                <div><dt>{{ __('ui.birds.molt') }}</dt><dd>{{ $bird->molt_status ? __('ui.molt.'.$bird->molt_status) : __('ui.common.unspecified') }}</dd></div>
                <div><dt>{{ __('ui.catalog.breeding') }}</dt><dd>{{ is_null($bird->breeding_ready) ? __('ui.common.unknown') : ($bird->breeding_ready ? __('ui.molt.ready') : __('ui.catalog.not_ready')) }}</dd></div>
                <div><dt>{{ __('ui.birds.singing') }}</dt><dd>{{ $bird->singing_status ? __('ui.singing.'.$bird->singing_status) : __('ui.common.unknown') }}</dd></div>
            </dl>
            @if($bird->status === 'available')<a href="#reserve" class="btn btn-primary btn-large"><i data-lucide="bookmark-check"></i> {{ __('ui.detail.reserve') }}</a>@else<div class="notice">{{ __('ui.detail.unavailable') }}</div>@endif
            @if($bird->seller->hasRole(\App\Enums\UserRole::Seller->value))<a class="seller-card" href="{{ route('sellers.show',$bird->seller) }}"><span class="seller-card-icon"><i data-lucide="store"></i></span><span><small>{{ __('ui.birds.seller') }}</small><strong>{{ $bird->seller->sellerProfile?->display_name ?: $bird->seller->name }}</strong><em>{{ $bird->city }}@if($bird->region)، {{ $bird->region->name }}@endif</em></span><i data-lucide="arrow-left"></i></a>@else<div class="seller-card"><span class="seller-card-icon"><i data-lucide="store"></i></span><span><small>{{ __('ui.birds.seller') }}</small><strong>{{ __('ui.detail.canary_admin') }}</strong><em>{{ $bird->city }}@if($bird->region)، {{ $bird->region->name }}@endif</em></span><i data-lucide="badge-check"></i></div>@endif
        </div>
    </div>

    @if($bird->media->where('type', 'video')->count())
        <section class="video-section"><div class="section-heading"><div><span class="kicker">{{ __('ui.detail.watch') }}</span><h2>{{ __('ui.detail.video') }}</h2></div></div><div class="video-grid">@foreach($bird->media->where('type', 'video') as $video)<div class="video-frame">@if($video->isCloudinary())<video src="{{ $video->url }}" controls preload="metadata" playsinline title="{{ __('ui.detail.video_title', ['title' => $bird->title]) }}"></video>@else<iframe src="{{ $video->embed_url }}" allow="autoplay" allowfullscreen title="{{ __('ui.detail.video_title', ['title' => $bird->title]) }}"></iframe>@endif<span class="video-brand-cover" aria-hidden="true"><i data-lucide="bird"></i></span></div>@endforeach</div></section>
    @endif

    @if($bird->status === 'available')
    <section id="reserve" class="reserve-section"><div><span class="kicker">{{ __('ui.detail.request_note') }}</span><h2>{{ __('ui.detail.request_title') }}</h2><p>{{ __('ui.detail.request_help') }}</p><ul><li><i data-lucide="check"></i> {{ __('ui.detail.no_online_payment') }}</li><li><i data-lucide="check"></i> {{ __('ui.detail.saved_price') }}</li><li><i data-lucide="check"></i> {{ __('ui.detail.direct_confirmation') }}</li></ul></div>
        <form action="{{ route('orders.store', $bird) }}" method="post" class="reserve-form">@csrf
            @if($errors->any())<div class="form-errors">{{ $errors->first() }}</div>@endif
            <label>{{ __('ui.detail.full_name') }}<input required name="buyer_name" value="{{ old('buyer_name') }}" autocomplete="name"></label>
            <label>{{ __('ui.common.phone') }}<input required name="phone" value="{{ old('phone') }}" inputmode="tel" autocomplete="tel" placeholder="05xxxxxxxx"></label>
            <label>{{ __('ui.common.region') }}<select required name="buyer_region_id"><option value="">{{ __('ui.detail.choose_region') }}</option>@foreach($navigationRegions as $region)<option value="{{ $region->id }}" @selected((string)old('buyer_region_id',$selectedRegion?->id)===(string)$region->id)>{{ $region->name }}</option>@endforeach</select></label>
            <div class="delivery-summary full"><span>{{ __('ui.detail.seller_delivery') }}</span><strong>{{ __('ui.delivery.'.$bird->delivery_type) }}</strong></div>
            <label class="full">{{ __('ui.detail.notes') }}<textarea name="notes" rows="3" placeholder="{{ __('ui.detail.notes_placeholder') }}">{{ old('notes') }}</textarea></label>
            <button class="btn btn-primary btn-large full" type="submit"><i data-lucide="send"></i> {{ __('ui.detail.send') }}</button>
            <small class="full">{{ __('ui.detail.agreement') }}</small>
        </form>
    </section>
    @endif
</div></section>
@if($relatedBirds->count())<section class="section-band section-muted"><div class="container"><div class="section-heading"><div><span class="kicker">{{ __('ui.detail.other_options') }}</span><h2>{{ __('ui.detail.same_breed') }}</h2></div></div><div class="birds-grid">@foreach($relatedBirds as $related)<x-bird-card :bird="$related" />@endforeach</div></div></section>@endif
@endsection
