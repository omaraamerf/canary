@extends('layouts.app')
@section('title', __('ui.guide.title'))
@section('meta_description', __('ui.guide.description'))
@section('content')
<section class="guide-hero"><div class="container"><span class="kicker">{{ __('ui.guide.for_all') }}</span><h1>{{ __('ui.guide.title') }}</h1><p>{{ __('ui.guide.lead') }}</p></div></section>
<section class="section-band"><div class="container">
    <div class="section-heading"><div><span class="kicker">{{ __('ui.guide.start') }}</span><h2>{{ __('ui.guide.categories') }}</h2></div></div>
    <div class="guide-category-grid">
        @foreach($categories as $category)
            <a class="guide-category-card" href="{{ route('guide.category',$category) }}">
                <img src="{{ $category->image ?: '/images/birds/classic-canary.jpg' }}" alt="{{ $category->name }}" loading="lazy">
                <span></span><div><small>{{ $category->published_articles_count }} {{ __('ui.common.articles') }}</small><h2>{{ $category->name }}</h2><p>{{ $category->description }}</p><i data-lucide="arrow-up-left"></i></div>
            </a>
        @endforeach
    </div>
</div></section>
@if($latestArticles->count())
<section class="section-band section-muted"><div class="container"><div class="section-heading"><div><span class="kicker">{{ __('ui.guide.content') }}</span><h2>{{ __('ui.guide.latest') }}</h2></div></div><div class="article-grid">@foreach($latestArticles as $article)<x-article-card :article="$article" />@endforeach</div></div></section>
@endif
@endsection
