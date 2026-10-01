@props(['bird', 'eager' => false])
@php
    $images = $bird->media->where('type', 'image')->values();
    $profile = $bird->seller?->sellerProfile;
@endphp
<article class="bird-card">
    <div class="bird-card-media">
        <x-img :src="$bird->primary_image" :width="320" sizes="(max-width: 767px) 50vw, 280px" alt="" :loading="$eager ? null : 'lazy'" :fetchpriority="$eager ? 'high' : null" />
        @if($images->count() > 1)<x-img class="bird-card-alt" :src="$images[1]->url" :width="320" sizes="(max-width: 767px) 50vw, 280px" alt="" loading="lazy" />@endif
        <span class="status-pill status-{{ $bird->status }}">{{ __('ui.bird_status.'.$bird->status) }}</span>
        <x-favorite-button :bird="$bird" class="bird-card-fav" />
        @if($bird->media->contains('type', 'video'))
            <span class="media-pill"><x-lucide-play />{{ __('ui.card.video') }}</span>
        @endif
    </div>
    <div class="bird-card-body">
        <p class="bird-card-meta"><span>{{ $bird->breed->localized_name }}</span><span><x-lucide-map-pin />{{ $bird->location_label }}</span></p>
        <h3><a class="bird-card-link" href="{{ route('birds.show', $bird) }}">{{ $bird->title }}</a></h3>
        <ul class="bird-traits">
            <li>{{ __('ui.sex.'.$bird->sex) }}</li>
            <li>{{ $bird->color }}</li>
            @if($bird->singing_status === 'singing')<li class="is-highlight"><x-lucide-music />{{ __('ui.card.singing') }}</li>@endif
            @if($bird->breeding_ready)<li class="is-highlight"><x-lucide-heart-handshake />{{ __('ui.card.breeding_ready') }}</li>@endif
            @if($bird->ring_number)<li><x-lucide-circle-dot />{{ __('ui.card.ringed') }}</li>@endif
        </ul>
        <div class="bird-card-footer">
            <p class="bird-price"><strong>{{ number_format($bird->price) }}</strong> <span>{{ $bird->currency_label }}</span></p>
            @if($profile?->approval_status === 'approved')
                <span class="bird-card-seller" title="{{ __('ui.card.verified') }}"><x-lucide-badge-check />{{ $profile->display_name }}</span>
            @endif
        </div>
    </div>
</article>
