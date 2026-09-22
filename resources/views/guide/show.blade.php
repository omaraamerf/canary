@extends('layouts.app')
@section('title',$article->title)
@section('meta_description', \Illuminate\Support\Str::limit($article->summary, 155))
@section('content')
<article class="guide-article">
    <header class="guide-article-head"><div class="container narrow">
        <nav class="breadcrumbs"><a href="{{ route('guide.index') }}">دليل الكناري</a><i data-lucide="chevron-left"></i><a href="{{ route('guide.category',$article->category) }}">{{ $article->category->name }}</a><i data-lucide="chevron-left"></i><span>{{ $article->title }}</span></nav>
        <span class="kicker">{{ $article->category->name }}</span><h1>{{ $article->title }}</h1><p>{{ $article->summary }}</p><time datetime="{{ $article->updated_at->toDateString() }}">آخر تحديث: {{ $article->updated_at->format('Y/m/d') }}</time>
    </div></header>
    <div class="container narrow">
        <img class="guide-cover" src="{{ $article->featured_image ?: '/images/birds/yellow-canary.jpg' }}" alt="{{ $article->title }}">
        <div class="guide-article-body">{{ $article->content }}</div>
        @if($article->tags->count())<div class="article-tags"><strong>وسوم:</strong>@foreach($article->tags as $tag)<span>{{ $tag->name }}</span>@endforeach</div>@endif
        <aside class="guide-market-cta"><div><span class="kicker">من الدليل إلى السوق</span><h2>تبحث عن طيور متوفرة في منطقتك؟</h2><p>شاهد بيانات الطيور وصورها وفيديوهاتها ثم أرسل طلب الحجز مباشرة.</p></div><a class="btn btn-primary btn-large" href="{{ route('birds.index') }}"><i data-lucide="bird"></i>عرض الطيور المتوفرة</a></aside>
    </div>
</article>
@if($relatedArticles->count())<section class="section-band section-muted"><div class="container"><div class="section-heading"><div><span class="kicker">واصل القراءة</span><h2>مقالات مرتبطة</h2></div></div><div class="article-grid">@foreach($relatedArticles as $related)<x-article-card :article="$related" />@endforeach</div></div></section>@endif
@endsection
