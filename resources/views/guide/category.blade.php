@extends('layouts.app')
@section('title', $category->name.' | '.__('ui.guide.title'))
@section('meta_description', $category->description ?: __('ui.guide.description'))
@section('content')
<section class="guide-head">
    <div class="container">
        <nav class="breadcrumbs" aria-label="{{ __('ui.nav.home') }}"><a href="{{ route('home') }}">{{ __('ui.nav.home') }}</a><x-lucide-chevron-left class="dir-icon" /><a href="{{ route('guide.index') }}">{{ __('ui.guide.title') }}</a><x-lucide-chevron-left class="dir-icon" /><span>{{ $category->name }}</span></nav>
        <div class="guide-head-title">
            <span class="topic-icon topic-icon-lg"><x-dynamic-component :component="'lucide-'.$category->icon" /></span>
            <div>
                <span class="kicker">{{ __('ui.guide.category') }} · {{ __('ui.guide.articles_count', ['count' => $articles->total()]) }}</span>
                <h1>{{ $category->name }}</h1>
                @if($category->description)<p>{{ $category->description }}</p>@endif
            </div>
        </div>
    </div>
</section>
<section class="section-band">
    <div class="container guide-layout">
        <div>
            @if($articles->count())
                <div class="article-list">@foreach($articles as $article)<x-article-row :article="$article" :show-category="false" />@endforeach</div>
                {{ $articles->links() }}
            @else
                <x-ui.empty-state icon="book-open" :title="__('ui.guide.empty')" :text="__('ui.guide.empty_help')"><x-ui.button variant="dark" :href="route('guide.index')">{{ __('ui.guide.back') }}</x-ui.button></x-ui.empty-state>
            @endif
        </div>
        @include('guide.partials.sidebar', ['current' => $category])
    </div>
</section>
@endsection
