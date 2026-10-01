@props(['bird'])
<article class="bird-card group">
    <a href="{{ route('birds.show', $bird) }}" class="bird-card-image">
        <img src="{{ $bird->primary_image }}" alt="{{ $bird->title }}" loading="lazy">
        <span class="status-pill status-{{ $bird->status }}">{{ __('ui.bird_status.'.$bird->status) }}</span>
        @if($bird->media->contains('type', 'video'))
            <span class="media-pill"><x-lucide-play /> {{ __('ui.common.video') }}</span>
        @endif
    </a>
    <div class="bird-card-body">
        <div class="flex items-center justify-between gap-3 text-sm text-[#6d7167]"><span>{{ $bird->breed->localized_name }}</span><span class="inline-flex items-center gap-1"><x-lucide-map-pin class="size-4" />{{ $bird->location_label }}</span></div>
        <h3><a href="{{ route('birds.show', $bird) }}">{{ $bird->title }}</a></h3>
        <div class="bird-tags"><span>{{ __('ui.sex.'.$bird->sex) }}</span><span>{{ $bird->color }}</span></div>
        <div class="bird-card-footer"><strong>{{ number_format($bird->price) }} <small>{{ $bird->currency_label }}</small></strong><a href="{{ route('birds.show', $bird) }}" aria-label="{{ __('ui.common.view') }} {{ $bird->title }}"><x-lucide-arrow-left /></a></div>
    </div>
</article>
