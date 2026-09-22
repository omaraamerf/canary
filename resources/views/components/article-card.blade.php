@props(['article'])
<article class="article-card">
    <a class="article-card-image" href="{{ route('guide.show', [$article->category, $article]) }}">
        <img src="{{ $article->featured_image ?: '/images/birds/yellow-canary.jpg' }}" alt="{{ $article->title }}" loading="lazy">
    </a>
    <div class="article-card-body">
        <span>{{ $article->category->name }}</span>
        <h3><a href="{{ route('guide.show', [$article->category, $article]) }}">{{ $article->title }}</a></h3>
        <p>{{ $article->summary }}</p>
        <div><time datetime="{{ $article->updated_at->toDateString() }}">آخر تحديث {{ $article->updated_at->format('Y/m/d') }}</time><a href="{{ route('guide.show', [$article->category, $article]) }}" aria-label="قراءة {{ $article->title }}"><i data-lucide="arrow-left"></i></a></div>
    </div>
</article>
