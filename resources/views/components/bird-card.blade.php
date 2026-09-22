@props(['bird'])
<article class="bird-card group">
    <a href="{{ route('birds.show', $bird) }}" class="bird-card-image">
        <img src="{{ $bird->primary_image }}" alt="{{ $bird->title }}" loading="lazy">
        <span class="status-pill status-{{ $bird->status }}">{{ ['available' => 'متاح', 'reserved' => 'محجوز', 'sold' => 'مباع'][$bird->status] }}</span>
        @if($bird->media->contains('type', 'video'))
            <span class="media-pill"><i data-lucide="play"></i> فيديو</span>
        @endif
    </a>
    <div class="bird-card-body">
        <div class="flex items-center justify-between gap-3 text-sm text-[#6d7167]"><span>{{ $bird->breed->name }}</span><span class="inline-flex items-center gap-1"><i data-lucide="map-pin" class="size-4"></i>{{ $bird->city }}@if($bird->region)، {{ $bird->region->name }}@endif</span></div>
        <h3><a href="{{ route('birds.show', $bird) }}">{{ $bird->title }}</a></h3>
        <div class="bird-tags"><span>{{ ['male' => 'ذكر', 'female' => 'أنثى', 'unknown' => 'غير محدد'][$bird->sex] }}</span><span>{{ $bird->color }}</span></div>
        <div class="bird-card-footer"><strong>{{ number_format($bird->price) }} <small>ر.س</small></strong><a href="{{ route('birds.show', $bird) }}" aria-label="عرض {{ $bird->title }}"><i data-lucide="arrow-left"></i></a></div>
    </div>
</article>
