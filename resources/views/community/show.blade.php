@extends('layouts.app')
@section('title', $post->title)
@section('meta_description', \Illuminate\Support\Str::limit($post->body, 155))
@section('content')
<div class="container post-page">
    <nav class="breadcrumbs" aria-label="{{ __('ui.nav.home') }}"><a href="{{ route('community.index') }}">{{ __('ui.community.title') }}</a><x-lucide-chevron-left class="dir-icon" /><a href="{{ route('community.index', ['category' => $post->category->value]) }}">{{ $post->category->label() }}</a><x-lucide-chevron-left class="dir-icon" /><span>{{ \Illuminate\Support\Str::limit($post->title, 60) }}</span></nav>

    @if($post->status !== \App\Enums\PostStatus::Published)<div class="alert alert-danger post-page-alert"><x-lucide-eye-off />{{ __('ui.community.hidden_notice') }}</div>@endif

    <div class="post-layout">
        <div class="post-main">
            <article class="post-detail">
                <div class="post-row-tags">
                    <span class="post-category post-category-{{ $post->category->value }}"><x-dynamic-component :component="'lucide-'.$post->category->icon()" />{{ $post->category->label() }}</span>
                    @if($post->isSolved())<span class="badge badge-success"><x-lucide-circle-check />{{ __('ui.community.solved') }}</span>@elseif($comments->isEmpty())<span class="badge badge-warning">{{ __('ui.community.awaiting_reply') }}</span>@endif
                </div>
                <h1>{{ $post->title }}</h1>
                <div class="post-author">
                    @include('community.partials.author', ['user' => $post->user, 'post' => $post])
                    <time datetime="{{ $post->created_at->toIso8601String() }}">{{ $post->created_at->diffForHumans() }}</time>
                </div>
                <div class="post-body">{{ $post->body }}</div>
                @include('community.partials.media', ['media' => $post->media, 'alt' => $post->title])

                @can('delete', $post)
                    <form method="post" action="{{ route('community.destroy', $post) }}" class="post-actions" onsubmit="return confirm(@js(__('ui.community.delete_confirm')))">
                        @csrf @method('delete')
                        <button type="submit" class="link-danger"><x-lucide-trash-2 /> {{ __('ui.community.delete') }}</button>
                    </form>
                @endcan
            </article>

            <section id="comments" class="post-comments">
                <h2>{{ __('ui.community.comments') }} <small>({{ $comments->count() }})</small></h2>

                @forelse($comments as $comment)
                    @php
                        $isAccepted = $comment->id === $post->accepted_comment_id;
                        $isStaff = $comment->user->isSeller() || $comment->user->isAdmin();
                    @endphp
                    <div id="comment-{{ $comment->id }}" @class(['comment', 'comment-staff' => $isStaff, 'comment-accepted' => $isAccepted])>
                        @if($isAccepted)<div class="comment-accepted-label"><x-lucide-circle-check /> {{ __('ui.community.accepted') }}</div>@endif
                        <div class="post-author">
                            @include('community.partials.author', ['user' => $comment->user, 'post' => $post])
                            <time datetime="{{ $comment->created_at->toIso8601String() }}">{{ $comment->created_at->diffForHumans() }}</time>
                        </div>
                        <div class="post-body">{{ $comment->body }}</div>
                        @include('community.partials.media', ['media' => $comment->media, 'alt' => $post->title])
                        <div class="post-actions">
                            @can('acceptComment', $post)
                                @unless($comment->isOwnedBy(auth()->user()))
                                    <form method="post" action="{{ route('community.comments.accept', [$post, $comment]) }}">@csrf
                                        <button type="submit"><x-lucide-circle-check /> {{ $isAccepted ? __('ui.community.unaccept') : __('ui.community.accept') }}</button>
                                    </form>
                                @endunless
                            @endcan
                            @can('delete', $comment)
                                <form method="post" action="{{ route('community.comments.destroy', $comment) }}" onsubmit="return confirm(@js(__('ui.community.delete_confirm')))">@csrf @method('delete')
                                    <button type="submit" class="link-danger"><x-lucide-trash-2 /> {{ __('ui.community.delete') }}</button>
                                </form>
                            @endcan
                        </div>
                    </div>
                @empty
                    <x-ui.empty-state icon="message-circle" :title="__('ui.community.awaiting_reply')" :text="__('ui.community.no_comments')" />
                @endforelse

                @auth
                    @can('comment', $post)
                        <form action="{{ route('community.comments.store', $post) }}" method="post" enctype="multipart/form-data" class="reply-form">
                            @csrf
                            <h3>{{ __('ui.community.add_comment') }}</h3>
                            @if($errors->any())<div class="form-errors">{{ $errors->first() }}</div>@endif
                            <label><span class="sr-only">{{ __('ui.community.add_comment') }}</span><textarea name="body" rows="4" required minlength="2" maxlength="3000" placeholder="{{ __('ui.community.comment_placeholder') }}">{{ old('body') }}</textarea></label>
                            <x-image-picker class="image-picker-compact" name="images" :max="3" :label="__('ui.uploads.add_images')" />
                            <x-ui.button type="submit" icon="send">{{ __('ui.community.send_comment') }}</x-ui.button>
                        </form>
                    @endcan
                @else
                    <div class="login-prompt">
                        <p>{{ __('ui.community.login_to_comment') }}</p>
                        <div><x-ui.button :href="route('login')">{{ __('ui.nav.login') }}</x-ui.button><x-ui.button variant="outline" :href="route('register')">{{ __('ui.nav.register') }}</x-ui.button></div>
                    </div>
                @endauth
            </section>
        </div>

        <aside class="post-aside">
            <div class="side-card">
                <h2>{{ __('ui.community.about_title') }}</h2>
                <dl class="order-facts">
                    <div><dt>{{ __('ui.community.category') }}</dt><dd>{{ $post->category->label() }}</dd></div>
                    <div><dt>{{ __('ui.community.state') }}</dt><dd>{{ $post->isSolved() ? __('ui.community.solved') : __('ui.community.open') }}</dd></div>
                    @if($post->breed)<div><dt>{{ __('ui.birds.breed') }}</dt><dd>{{ $post->breed->localized_name }}</dd></div>@endif
                    @if($post->country)<div><dt>{{ __('ui.common.region') }}</dt><dd>{{ $post->region ? $post->region->name.'، ' : '' }}{{ $post->country->localized_name }}</dd></div>@endif
                    <div><dt>{{ __('ui.community.replies') }}</dt><dd>{{ $comments->count() }}</dd></div>
                </dl>
            </div>
            <div class="alert alert-warning"><x-lucide-stethoscope />{{ __('ui.community.disclaimer') }}</div>
            @if($relatedPosts->isNotEmpty())
                <nav class="side-card" aria-label="{{ __('ui.community.related') }}">
                    <h2>{{ __('ui.community.related') }}</h2>
                    <ul class="side-links">
                        @foreach($relatedPosts as $related)
                            <li><a href="{{ route('community.show', $related) }}"><strong>{{ $related->title }}</strong><small><x-lucide-message-circle />{{ __('ui.community.comments_count', ['count' => $related->visible_comments_count]) }} · {{ $related->created_at->diffForHumans() }}</small></a></li>
                        @endforeach
                    </ul>
                </nav>
            @endif
            <div class="side-card side-card-cta">
                <x-lucide-message-circle-question />
                <h2>{{ __('ui.community.cta_title') }}</h2>
                <p>{{ __('ui.community.cta_text') }}</p>
                <x-ui.button variant="dark" size="sm" :href="route('community.create', ['category' => $post->category->value])">{{ __('ui.community.new_post') }}</x-ui.button>
            </div>
        </aside>
    </div>
</div>
@endsection
