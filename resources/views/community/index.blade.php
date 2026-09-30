@extends('layouts.app')
@section('title', __('ui.community.title'))
@section('meta_description', __('ui.community.description'))
@section('content')
<section class="guide-hero community-hero"><div class="container">
    <span class="kicker">{{ __('ui.community.eyebrow') }}</span>
    <h1>{{ __('ui.community.title') }}</h1>
    <p>{{ __('ui.community.lead') }}</p>
    <a class="btn btn-primary btn-large" href="{{ route('community.create') }}"><i data-lucide="message-circle-question"></i> {{ __('ui.community.new_post') }}</a>
</div></section>

<section class="section-band pt-8"><div class="container catalog-layout">
    <aside class="filters">
        <div class="filter-title"><h2>{{ __('ui.community.filters') }}</h2><a href="{{ route('community.index') }}">{{ __('ui.common.clear') }}</a></div>
        <form action="{{ route('community.index') }}" method="get" class="filter-form">
            <label>{{ __('ui.community.query') }}<input type="search" name="q" value="{{ request('q') }}"></label>
            <label>{{ __('ui.community.category') }}<select name="category"><option value="">{{ __('ui.community.all_categories') }}</option>@foreach(\App\Enums\PostCategory::options() as $value => $label)<option value="{{ $value }}" @selected(request('category') === $value)>{{ $label }}</option>@endforeach</select></label>
            <label>{{ __('ui.birds.breed') }}<select name="breed"><option value="">{{ __('ui.community.all_breeds') }}</option>@foreach($breeds as $breed)<option value="{{ $breed->slug }}" @selected(request('breed') === $breed->slug)>{{ $breed->localized_name }}</option>@endforeach</select></label>
            <label>{{ __('ui.community.state') }}<select name="state"><option value="">{{ __('ui.common.all') }}</option><option value="open" @selected(request('state') === 'open')>{{ __('ui.community.open') }}</option><option value="solved" @selected(request('state') === 'solved')>{{ __('ui.community.solved') }}</option></select></label>
            <button class="btn btn-primary w-full" type="submit"><i data-lucide="list-filter"></i> {{ __('ui.community.apply') }}</button>
        </form>
    </aside>
    <div>
        @if(session('success'))<div class="notice mb-4">{{ session('success') }}</div>@endif
        <div class="results-toolbar"><span><strong>{{ $posts->total() }}</strong> {{ __('ui.common.results') }}</span></div>
        @if($posts->count())
            <div class="post-list">@foreach($posts as $post)<x-post-card :post="$post" />@endforeach</div>
            {{ $posts->links() }}
        @else
            <div class="empty-state"><i data-lucide="message-circle-question"></i><h2>{{ __('ui.community.empty') }}</h2><p>{{ __('ui.community.empty_help') }}</p><a class="btn btn-dark" href="{{ route('community.create') }}">{{ __('ui.community.new_post') }}</a></div>
        @endif
    </div>
</div></section>
@endsection
