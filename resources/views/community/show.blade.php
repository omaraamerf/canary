@extends('layouts.app')
@section('title', $post->title)
@section('meta_description', \Illuminate\Support\Str::limit($post->body, 155))
@section('content')
<section class="detail-section"><div class="container narrow">
    <nav class="breadcrumbs"><a href="{{ route('community.index') }}">{{ __('ui.community.title') }}</a><i data-lucide="chevron-left"></i><span>{{ $post->category->label() }}</span></nav>

    @if(session('success'))<div class="notice mb-4">{{ session('success') }}</div>@endif
    @if($post->status !== \App\Enums\PostStatus::Published)<div class="form-errors mb-4">{{ __('ui.community.hidden_notice') }}</div>@endif

    <article class="post-detail">
        <div class="post-card-meta">
            <span class="post-category post-category-{{ $post->category->value }}">{{ $post->category->label() }}</span>
            @if($post->isSolved())<span class="post-solved"><i data-lucide="circle-check"></i>{{ __('ui.community.solved') }}</span>@endif
            @if($post->breed)<span>{{ $post->breed->localized_name }}</span>@endif
            @if($post->region)<span><i data-lucide="map-pin"></i>{{ $post->region->name }}</span>@endif
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
                <button type="submit" class="link-danger"><i data-lucide="trash-2"></i> {{ __('ui.community.delete') }}</button>
            </form>
        @endcan
    </article>

    <div class="notice community-disclaimer"><i data-lucide="stethoscope"></i> {{ __('ui.community.disclaimer') }}</div>

    <section id="comments" class="post-comments">
        <h2>{{ __('ui.community.comments') }} <small>({{ $comments->count() }})</small></h2>

        @forelse($comments as $comment)
            @php
                $isAccepted = $comment->id === $post->accepted_comment_id;
                $isStaff = $comment->user->isSeller() || $comment->user->isAdmin();
            @endphp
            <div id="comment-{{ $comment->id }}" @class(['comment', 'comment-staff' => $isStaff, 'comment-accepted' => $isAccepted])>
                @if($isAccepted)<div class="comment-accepted-label"><i data-lucide="circle-check"></i> {{ __('ui.community.accepted') }}</div>@endif
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
                                <button type="submit"><i data-lucide="circle-check"></i> {{ $isAccepted ? __('ui.community.unaccept') : __('ui.community.accept') }}</button>
                            </form>
                        @endunless
                    @endcan
                    @can('delete', $comment)
                        <form method="post" action="{{ route('community.comments.destroy', $comment) }}" onsubmit="return confirm(@js(__('ui.community.delete_confirm')))">@csrf @method('delete')
                            <button type="submit" class="link-danger"><i data-lucide="trash-2"></i> {{ __('ui.community.delete') }}</button>
                        </form>
                    @endcan
                </div>
            </div>
        @empty
            <p class="community-muted">{{ __('ui.community.no_comments') }}</p>
        @endforelse

        @auth
            @can('comment', $post)
                <form action="{{ route('community.comments.store', $post) }}" method="post" enctype="multipart/form-data" class="reserve-form community-form comment-form">
                    @csrf
                    <h3 class="full">{{ __('ui.community.add_comment') }}</h3>
                    @if($errors->any())<div class="form-errors full">{{ $errors->first() }}</div>@endif
                    <label class="full"><textarea name="body" rows="4" required minlength="2" maxlength="3000" placeholder="{{ __('ui.community.comment_placeholder') }}">{{ old('body') }}</textarea></label>
                    <label class="full">{{ __('ui.community.comment_images') }}<input type="file" name="images[]" accept="image/*" multiple></label>
                    <button class="btn btn-primary full" type="submit"><i data-lucide="send"></i> {{ __('ui.community.send_comment') }}</button>
                </form>
            @endcan
        @else
            <div class="login-prompt">
                <p>{{ __('ui.community.login_to_comment') }}</p>
                <div><a class="btn btn-primary" href="{{ route('login') }}">{{ __('ui.nav.login') }}</a><a class="btn btn-outline" href="{{ route('register') }}">{{ __('ui.nav.register') }}</a></div>
            </div>
        @endauth
    </section>
</div></section>
@endsection
