@extends('layouts.app')
@section('title', $member->public_name)
@section('content')
@include('partials.profile-header', ['member' => $member, 'stats' => $stats, 'isOwner' => auth()->id() === $member->id])

<section class="section-band pt-8"><div class="container narrow">
    <div class="section-heading"><div><span class="kicker">{{ __('ui.nav.community') }}</span><h2>{{ __('ui.account.member_posts', ['name' => $member->public_name]) }}</h2></div></div>
    @if($posts->count())
        <div class="post-list">@foreach($posts as $post)<x-post-card :post="$post" />@endforeach</div>
        {{ $posts->links() }}
    @else
        <div class="empty-state"><i data-lucide="message-circle-question"></i><h2>{{ __('ui.account.member_no_posts') }}</h2></div>
    @endif
</div></section>
@endsection
