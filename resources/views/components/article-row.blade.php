@props(['article', 'showCategory' => true])
<article class="article-row">
    <img src="{{ $article->featured_image ?: '/images/birds/yellow-canary.jpg' }}" alt="" loading="lazy">
    <div>
        @if($showCategory)<span class="article-row-category"><x-dynamic-component :component="'lucide-'.$article->category->icon" />{{ $article->category->name }}</span>@endif
        <h3><a class="article-row-link" href="{{ route('guide.show', [$article->category, $article]) }}">{{ $article->title }}</a></h3>
        @if($article->summary)<p>{{ $article->summary }}</p>@endif
        <p class="article-meta"><span><x-lucide-clock />{{ __('ui.guide.reading_time', ['minutes' => $article->reading_minutes]) }}</span><time datetime="{{ $article->updated_at->toDateString() }}">{{ __('ui.guide.updated', ['date' => $article->updated_at->format('Y/m/d')]) }}</time></p>
    </div>
</article>
