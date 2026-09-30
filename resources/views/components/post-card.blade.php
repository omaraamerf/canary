@props(['post'])
<a class="post-card" href="{{ route('community.show', $post) }}">
    @if($image = $post->media->firstWhere('type', 'image'))
        <img src="{{ $image->url }}" alt="{{ $post->title }}" loading="lazy">
    @endif
    <div>
        <div class="post-card-meta">
            <span class="post-category post-category-{{ $post->category->value }}">{{ $post->category->label() }}</span>
            @if($post->status !== \App\Enums\PostStatus::Published)<span class="post-hidden"><i data-lucide="eye-off"></i>{{ __('ui.post_status.hidden') }}</span>@endif
            @if($post->isSolved())<span class="post-solved"><i data-lucide="circle-check"></i>{{ __('ui.community.solved') }}</span>@endif
            @if($post->breed)<span>{{ $post->breed->localized_name }}</span>@endif
            @if($post->country)<span><i data-lucide="map-pin"></i>{{ $post->region ? $post->region->name.'، ' : '' }}{{ $post->country->localized_name }}</span>@endif
            @if($post->media->count() > 1)<span><i data-lucide="images"></i>{{ $post->media->count() }}</span>@endif
        </div>
        <h2>{{ $post->title }}</h2>
        <p>{{ \Illuminate\Support\Str::limit($post->body, 160) }}</p>
        <div class="post-card-foot">
            <span class="post-card-author"><x-avatar :user="$post->user" size="xs" />{{ $post->user->public_name }} · {{ $post->created_at->diffForHumans() }}</span>
            <span><i data-lucide="message-circle"></i>{{ __('ui.community.comments_count', ['count' => $post->visible_comments_count ?? 0]) }}</span>
        </div>
    </div>
</a>
