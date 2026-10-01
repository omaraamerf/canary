@extends('layouts.app')
@section('title', __('ui.account.my_account'))
@section('no_region_prompt', '1')
@section('content')
@include('partials.profile-header', ['member' => $user, 'stats' => $stats, 'isOwner' => true])

@php
    $communityOn = array_key_exists('posts', $tabs);
    $missing = array_filter([
        'photo' => ! $user->avatar_url,
        'bio' => ! $user->bio,
        'location' => ! $user->region_id,
        'phone' => ! $user->phone,
    ]);
@endphp

<section class="section-band account-body"><div class="container">
    @include('account.partials.tabs', ['current' => $tab])

    <div class="profile-layout">
        <div class="account-panel">
            @if($tab === 'posts')
                <div class="account-panel-head">
                    <h2>{{ __('ui.account.tab_posts') }}</h2>
                    <x-ui.button variant="dark" icon="message-circle-plus" :href="route('community.create')">{{ __('ui.community.new_post') }}</x-ui.button>
                </div>
                @if($items->count())
                    <div class="post-list">@foreach($items as $post)<x-post-card :post="$post" />@endforeach</div>
                @else
                    <x-ui.empty-state icon="message-circle-question" :title="__('ui.account.no_posts')" :text="__('ui.account.no_posts_help')"><x-ui.button variant="dark" :href="route('community.create')">{{ __('ui.community.new_post') }}</x-ui.button></x-ui.empty-state>
                @endif
            @elseif($tab === 'replies')
                <div class="account-panel-head"><h2>{{ __('ui.account.tab_replies') }}</h2></div>
                @if($items->count())
                    <div class="reply-list">
                        @foreach($items as $comment)
                            <a class="reply-item" href="{{ route('community.show', $comment->commentable) }}#comment-{{ $comment->id }}">
                                <strong>{{ $comment->commentable->title }}</strong>
                                <p>{{ \Illuminate\Support\Str::limit($comment->body, 180) }}</p>
                                <small>
                                    {{ $comment->created_at->diffForHumans() }}
                                    @if($comment->commentable->accepted_comment_id === $comment->id)· <span class="post-solved"><x-lucide-circle-check />{{ __('ui.community.accepted') }}</span>@endif
                                    @if($comment->is_hidden)· {{ __('ui.post_status.hidden') }}@endif
                                </small>
                            </a>
                        @endforeach
                    </div>
                @else
                    <x-ui.empty-state icon="message-square-reply" :title="__('ui.account.no_replies')" :text="__('ui.account.no_replies_help')"><x-ui.button variant="outline" :href="route('community.index', ['state' => 'open'])">{{ __('ui.account.browse_open') }}</x-ui.button></x-ui.empty-state>
                @endif
            @else
                <div class="account-panel-head">
                    <h2>{{ __('ui.account.tab_orders') }}</h2>
                    <x-ui.button variant="outline" icon="package-search" :href="route('orders.track')">{{ __('ui.account.track_other') }}</x-ui.button>
                </div>
                @if($items->count())
                    <div class="order-list">
                        @foreach($items as $order)
                            @php $status = \App\Enums\OrderStatus::from($order->status); @endphp
                            <a class="order-row" href="{{ route('orders.track.show', $order) }}">
                                <x-img :src="$order->bird?->primary_image ?? '/images/birds/yellow-canary.jpg'" :width="72" alt="" width="72" height="72" loading="lazy" />
                                <div class="order-row-main">
                                    <strong>{{ $order->bird?->title ?? __('ui.account.bird_removed') }}</strong>
                                    <span class="order-row-meta"><span dir="ltr">{{ $order->reference }}</span><span>{{ $order->created_at->translatedFormat('j F Y') }}</span></span>
                                </div>
                                <div class="order-row-side">
                                    <x-ui.badge :variant="$status->badge()" :icon="$status->icon()">{{ $status->label() }}</x-ui.badge>
                                    <b>{{ number_format((float) $order->price_snapshot) }} {{ \App\Enums\Currency::labelFor($order->currency_snapshot) }}</b>
                                </div>
                            </a>
                        @endforeach
                    </div>
                @else
                    <x-ui.empty-state icon="package" :title="__('ui.account.no_orders')" :text="__('ui.account.no_orders_help')"><x-ui.button :href="route('birds.index')">{{ __('ui.account.browse_birds') }}</x-ui.button></x-ui.empty-state>
                @endif
            @endif
            {{ $items->links() }}
        </div>

        <aside class="account-side">
            @if($missing)
                <div class="side-card profile-complete">
                    <h2>{{ __('ui.account.complete_title') }}</h2>
                    <p>{{ __('ui.account.complete_text') }}</p>
                    <meter min="0" max="4" value="{{ 4 - count($missing) }}" aria-label="{{ __('ui.account.complete_title') }}">{{ 4 - count($missing) }}/4</meter>
                    <ul>
                        @foreach(array_keys($missing) as $item)
                            <li><a href="{{ route('account.edit') }}#{{ ['photo' => 'photo', 'location' => 'location'][$item] ?? 'personal' }}"><x-lucide-circle-plus />{{ __('ui.account.complete_'.$item) }}</a></li>
                        @endforeach
                    </ul>
                </div>
            @endif
            <div class="side-card">
                <h2>{{ __('ui.account.shortcuts') }}</h2>
                <nav class="side-nav">
                    @if($user->isSeller())<a href="{{ url('/seller') }}"><x-lucide-layout-dashboard />{{ __('ui.nav.seller_panel') }}</a>@endif
                    @if($user->isAdmin())<a href="{{ url('/admin') }}"><x-lucide-shield-check />{{ __('ui.nav.admin_panel') }}</a>@endif
                    @if($communityOn)<a href="{{ route('community.create') }}"><x-lucide-message-circle-plus />{{ __('ui.community.new_post') }}</a>@endif
                    <a href="{{ route('birds.index') }}"><x-lucide-bird />{{ __('ui.account.browse_birds') }}</a>
                    <a href="{{ route('orders.track') }}"><x-lucide-package-search />{{ __('ui.nav.track') }}</a>
                    <a href="{{ route('members.show', $user) }}"><x-lucide-eye />{{ __('ui.account.public_profile') }}</a>
                </nav>
            </div>
        </aside>
    </div>
</div></section>
@endsection
