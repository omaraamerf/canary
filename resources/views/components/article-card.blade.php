@props(['article'])
<article class="article-card">
    <div class="article-card-image">
        <x-img :src="$article->featured_image ?: '/images/birds/yellow-canary.jpg'" :width="400" sizes="(max-width: 767px) 100vw, 380px" alt="" loading="lazy" />
    </div>
    <div class="article-card-body">
        <span class="article-row-category"><x-dynamic-component :component="'lucide-'.$article->category->icon" />{{ $article->category->name }}</span>
        <h3><a class="article-row-link" href="{{ route('guide.show', [$article->category, $article]) }}">{{ $article->title }}</a></h3>
        <p>{{ $article->summary }}</p>
        <p class="article-meta"><span><x-lucide-clock />{{ __('ui.guide.reading_time', ['minutes' => $article->reading_minutes]) }}</span><time datetime="{{ $article->updated_at->toDateString() }}">{{ __('ui.guide.updated', ['date' => $article->updated_at->format('Y/m/d')]) }}</time></p>
    </div>
</article>
