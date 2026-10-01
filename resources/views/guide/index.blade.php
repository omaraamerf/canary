@extends('layouts.app')
@section('title', __('ui.guide.title'))
@section('meta_description', __('ui.guide.description'))
@section('content')
<section class="guide-head">
    <div class="container guide-head-grid">
        <div>
            <span class="kicker">{{ __('ui.guide.for_all') }}</span>
            <h1>{{ __('ui.guide.title') }}</h1>
            <p>{{ __('ui.guide.lead') }}</p>
        </div>
        <div class="guide-head-search">
            <form action="{{ route('guide.index') }}" method="get" class="search-bar" role="search">
                <x-lucide-search />
                <input type="search" name="q" value="{{ $query }}" placeholder="{{ __('ui.guide.search_placeholder') }}" aria-label="{{ __('ui.guide.search') }}">
                <button type="submit">{{ __('ui.common.search') }}</button>
            </form>
            <p class="guide-head-stats"><span><x-lucide-book-open />{{ __('ui.guide.articles_count', ['count' => $articlesCount]) }}</span><span><x-lucide-layout-grid />{{ __('ui.guide.categories_count', ['count' => $categories->count()]) }}</span></p>
        </div>
    </div>
</section>

@if($results !== null)
    <section class="section-band">
        <div class="container guide-layout">
            <div>
                <div class="section-heading"><div><h2>{{ __('ui.guide.results_for', ['q' => $query]) }}</h2><p class="catalog-count">{{ __('ui.guide.articles_count', ['count' => $results->total()]) }}</p></div></div>
                @if($results->count())
                    <div class="article-list">@foreach($results as $article)<x-article-row :article="$article" />@endforeach</div>
                    {{ $results->links() }}
                @else
                    <x-ui.empty-state icon="search" :title="__('ui.guide.no_results')" :text="__('ui.guide.no_results_help')"><x-ui.button variant="outline" :href="route('guide.index')">{{ __('ui.guide.back') }}</x-ui.button></x-ui.empty-state>
                @endif
            </div>
            @include('guide.partials.sidebar')
        </div>
    </section>
@else
    <section class="section-band">
        <div class="container">
            <div class="section-heading"><div><span class="kicker">{{ __('ui.guide.start') }}</span><h2>{{ __('ui.guide.categories') }}</h2></div></div>
            <div class="topic-grid">
                @foreach($categories as $category)
                    <a class="topic-tile" href="{{ route('guide.category', $category) }}">
                        <span class="topic-icon"><x-dynamic-component :component="'lucide-'.$category->icon" /></span>
                        <span class="topic-body"><strong>{{ $category->name }}</strong><small>{{ $category->description }}</small></span>
                        <b>{{ __('ui.guide.articles_count', ['count' => $category->published_articles_count]) }}</b>
                    </a>
                @endforeach
            </div>
        </div>
    </section>

    @if($latestArticles->isNotEmpty())
        @php($featured = $latestArticles->first())
        <section class="section-band section-muted">
            <div class="container">
                <div class="section-heading"><div><span class="kicker">{{ __('ui.guide.content') }}</span><h2>{{ __('ui.guide.latest') }}</h2></div></div>
                <div class="guide-latest">
                    <article class="article-feature">
                        <img src="{{ $featured->featured_image ?: '/images/birds/yellow-canary.jpg' }}" alt="" loading="lazy">
                        <div>
                            <span class="badge badge-primary">{{ __('ui.guide.featured') }}</span>
                            <span class="article-row-category"><x-dynamic-component :component="'lucide-'.$featured->category->icon" />{{ $featured->category->name }}</span>
                            <h3><a class="article-row-link" href="{{ route('guide.show', [$featured->category, $featured]) }}">{{ $featured->title }}</a></h3>
                            <p>{{ $featured->summary }}</p>
                            <p class="article-meta"><span><x-lucide-clock />{{ __('ui.guide.reading_time', ['minutes' => $featured->reading_minutes]) }}</span></p>
                        </div>
                    </article>
                    <div class="article-list">
                        @foreach($latestArticles->skip(1) as $article)<x-article-row :article="$article" />@endforeach
                    </div>
                </div>
            </div>
        </section>
    @endif
@endif
@endsection
