@extends('layouts.app')
@section('title', __('ui.account.my_account'))
@section('content')
@include('partials.profile-header', ['member' => $user, 'stats' => $stats, 'isOwner' => true])

<section class="section-band pt-8"><div class="container">
    @if(session('success'))<div class="notice profile-flash">{{ session('success') }}</div>@endif
    <div class="profile-layout">
        <div>
            <div class="section-heading"><div><span class="kicker">{{ __('ui.nav.community') }}</span><h2>{{ __('ui.account.my_posts') }}</h2></div>
                @if($communityEnabled)<a class="btn btn-dark" href="{{ route('community.create') }}"><x-lucide-message-circle-plus />{{ __('ui.community.new_post') }}</a>@endif
            </div>
            @if($posts->count())
                <div class="post-list">@foreach($posts as $post)<x-post-card :post="$post" />@endforeach</div>
                {{ $posts->links() }}
            @else
                <div class="empty-state"><x-lucide-message-circle-question /><h2>{{ __('ui.account.no_posts') }}</h2><p>{{ __('ui.account.no_posts_help') }}</p></div>
            @endif
        </div>
        <aside class="profile-side">
            <h2>{{ __('ui.account.my_replies') }}</h2>
            @forelse($comments as $comment)
                <a class="reply-item" href="{{ route('community.show', $comment->commentable) }}#comment-{{ $comment->id }}">
                    <strong>{{ $comment->commentable->title }}</strong>
                    <p>{{ \Illuminate\Support\Str::limit($comment->body, 110) }}</p>
                    <small>
                        {{ $comment->created_at->diffForHumans() }}
                        @if($comment->commentable->accepted_comment_id === $comment->id)· <span class="post-solved"><x-lucide-circle-check />{{ __('ui.community.accepted') }}</span>@endif
                        @if($comment->is_hidden)· {{ __('ui.post_status.hidden') }}@endif
                    </small>
                </a>
            @empty
                <p class="community-muted">{{ __('ui.account.no_replies') }}</p>
            @endforelse
            {{ $comments->links() }}
        </aside>
    </div>
</div></section>
@endsection
