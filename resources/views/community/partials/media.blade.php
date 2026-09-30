@if($media->count())
    <div class="post-media">
        @foreach($media as $item)
            @if($item->type === 'image')
                <a href="{{ $item->url }}" target="_blank" rel="noopener"><img src="{{ $item->url }}" alt="{{ $alt }}" loading="lazy"></a>
            @else
                <video src="{{ $item->url }}" controls preload="metadata" playsinline></video>
            @endif
        @endforeach
    </div>
@endif
