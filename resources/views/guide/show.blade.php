@extends('layouts.app')
@section('title', $article->title)
@section('meta_description', \Illuminate\Support\Str::limit($article->summary, 155))
@section('content')
<div class="reading-progress" aria-hidden="true"></div>
<article class="guide-article">
    <header class="article-head">
        <div class="container">
            <nav class="breadcrumbs" aria-label="{{ __('ui.nav.home') }}"><a href="{{ route('guide.index') }}">{{ __('ui.guide.title') }}</a><x-lucide-chevron-left class="dir-icon" /><a href="{{ route('guide.category', $article->category) }}">{{ $article->category->name }}</a><x-lucide-chevron-left class="dir-icon" /><span>{{ $article->title }}</span></nav>
            <div class="article-head-body">
                <a class="article-row-category" href="{{ route('guide.category', $article->category) }}"><x-dynamic-component :component="'lucide-'.$article->category->icon" />{{ $article->category->name }}</a>
                <h1>{{ $article->title }}</h1>
                @if($article->summary)<p class="article-lead">{{ $article->summary }}</p>@endif
                <p class="article-meta">
                    <span><x-lucide-clock />{{ __('ui.guide.reading_time', ['minutes' => $article->reading_minutes]) }}</span>
                    <time datetime="{{ $article->updated_at->toDateString() }}">{{ __('ui.guide.updated', ['date' => $article->updated_at->format('Y/m/d')]) }}</time>
                    <button type="button" class="btn btn-ghost btn-sm" data-share data-share-title="{{ $article->title }}" data-share-copied="{{ __('ui.detail.link_copied') }}"><x-lucide-share-2 />{{ __('ui.detail.share') }}</button>
                </p>
            </div>
        </div>
    </header>

    <div class="container article-layout">
        <div class="article-main">
            <x-img class="article-cover" :src="$article->featured_image ?: '/images/birds/yellow-canary.jpg'" :width="860" sizes="(max-width: 1000px) 100vw, 860px" alt="" fetchpriority="high" />
            <div class="prose">{!! $content['html'] !!}</div>
            @if($article->tags->count())<div class="article-tags"><strong>{{ __('ui.guide.tags') }}</strong>@foreach($article->tags as $tag)<span>{{ $tag->name }}</span>@endforeach</div>@endif
            <aside class="guide-market-cta"><div><span class="kicker">{{ __('ui.guide.market') }}</span><h2>{{ __('ui.guide.market_title') }}</h2><p>{{ __('ui.guide.market_text') }}</p></div><x-ui.button :href="route('birds.index')" size="lg" icon="bird">{{ __('ui.guide.market_button') }}</x-ui.button></aside>
        </div>
        <div class="article-side">
            @if(count($content['headings']) >= 2)
                <nav class="side-card toc" aria-label="{{ __('ui.guide.on_this_page') }}">
                    <h2>{{ __('ui.guide.on_this_page') }}</h2>
                    <ol>
                        @foreach($content['headings'] as $heading)
                            <li @class(['is-sub' => $heading['level'] === 3])><a href="#{{ $heading['id'] }}">{{ $heading['text'] }}</a></li>
                        @endforeach
                    </ol>
                </nav>
            @endif
            @include('guide.partials.sidebar', ['current' => $article->category])
        </div>
    </div>
</article>
@if($relatedArticles->count())
    <section class="section-band section-muted"><div class="container"><div class="section-heading"><div><span class="kicker">{{ __('ui.guide.continue') }}</span><h2>{{ __('ui.guide.related') }}</h2></div></div><div class="article-grid">@foreach($relatedArticles as $related)<x-article-card :article="$related" />@endforeach</div></div></section>
@endif
@endsection
