@props(['post'])
@php
    $image = $post->media->firstWhere('type', 'image');
    $replies = $post->visible_comments_count ?? 0;
@endphp
<article class="post-row">
    <div class="post-row-main">
        <div class="post-row-tags">
            <span class="post-category post-category-{{ $post->category->value }}"><x-dynamic-component :component="'lucide-'.$post->category->icon()" />{{ $post->category->label() }}</span>
            @if($post->status !== \App\Enums\PostStatus::Published)<span class="badge badge-danger"><x-lucide-eye-off />{{ __('ui.post_status.hidden') }}</span>@endif
            @if($post->isSolved())
                <span class="badge badge-success"><x-lucide-circle-check />{{ __('ui.community.solved') }}</span>
            @elseif($replies === 0)
                <span class="badge badge-warning">{{ __('ui.community.awaiting_reply') }}</span>
            @endif
        </div>
        <h3><a class="post-row-link" href="{{ route('community.show', $post) }}">{{ $post->title }}</a></h3>
        <p class="post-row-excerpt">{{ \Illuminate\Support\Str::limit($post->body, 220) }}</p>
        <p class="post-row-meta">
            <span class="post-card-author"><x-avatar :user="$post->user" size="xs" />{{ $post->user->public_name }}</span>
            <time datetime="{{ $post->created_at->toIso8601String() }}">{{ $post->created_at->diffForHumans() }}</time>
            @if($post->breed)<span>{{ $post->breed->localized_name }}</span>@endif
            @if($post->country)<span><x-lucide-map-pin />{{ $post->region ? $post->region->name.'، ' : '' }}{{ $post->country->localized_name }}</span>@endif
        </p>
    </div>
    <div class="post-row-side">
        @if($image)<img src="{{ $image->url }}" alt="" loading="lazy">@endif
        <span class="post-row-replies" title="{{ __('ui.community.comments_count', ['count' => $replies]) }}"><x-lucide-message-circle />{{ $replies }}</span>
    </div>
</article>
