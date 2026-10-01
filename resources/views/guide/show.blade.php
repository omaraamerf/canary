@extends('layouts.app')
@section('title',$article->title)
@section('meta_description', \Illuminate\Support\Str::limit($article->summary, 155))
@section('content')
<article class="guide-article">
    <header class="guide-article-head"><div class="container narrow">
        <nav class="breadcrumbs"><a href="{{ route('guide.index') }}">{{ __('ui.guide.title') }}</a><x-lucide-chevron-left /><a href="{{ route('guide.category',$article->category) }}">{{ $article->category->name }}</a><x-lucide-chevron-left /><span>{{ $article->title }}</span></nav>
        <span class="kicker">{{ $article->category->name }}</span><h1>{{ $article->title }}</h1><p>{{ $article->summary }}</p><time datetime="{{ $article->updated_at->toDateString() }}">{{ __('ui.guide.updated', ['date' => $article->updated_at->format('Y/m/d')]) }}</time>
    </div></header>
    <div class="container narrow">
        <img class="guide-cover" src="{{ $article->featured_image ?: '/images/birds/yellow-canary.jpg' }}" alt="{{ $article->title }}">
        <div class="guide-article-body">{{ $article->content }}</div>
        @if($article->tags->count())<div class="article-tags"><strong>{{ __('ui.guide.tags') }}</strong>@foreach($article->tags as $tag)<span>{{ $tag->name }}</span>@endforeach</div>@endif
        <aside class="guide-market-cta"><div><span class="kicker">{{ __('ui.guide.market') }}</span><h2>{{ __('ui.guide.market_title') }}</h2><p>{{ __('ui.guide.market_text') }}</p></div><a class="btn btn-primary btn-large" href="{{ route('birds.index') }}"><x-lucide-bird />{{ __('ui.guide.market_button') }}</a></aside>
    </div>
</article>
@if($relatedArticles->count())<section class="section-band section-muted"><div class="container"><div class="section-heading"><div><span class="kicker">{{ __('ui.guide.continue') }}</span><h2>{{ __('ui.guide.related') }}</h2></div></div><div class="article-grid">@foreach($relatedArticles as $related)<x-article-card :article="$related" />@endforeach</div></div></section>@endif
@endsection
