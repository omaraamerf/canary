@if($media->count())
    <div class="post-media">
        @foreach($media as $item)
            @if($item->type === 'image')
                <a href="{{ $item->url }}" target="_blank" rel="noopener"><x-img :src="$item->url" :width="720" sizes="(max-width: 1000px) 100vw, 720px" :alt="$alt" loading="lazy" /></a>
            @else
                <video src="{{ $item->url }}" controls preload="metadata" playsinline></video>
            @endif
        @endforeach
    </div>
@endif
