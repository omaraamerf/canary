@extends('layouts.app')
@section('title','دليل الكناري')
@section('meta_description','دليل عملي للعناية بالكناري وصحته وتحديد الجنس والجهوزية والتفريخ والسلالات.')
@section('content')
<section class="guide-hero"><div class="container"><span class="kicker">للمربي والمبتدئ</span><h1>دليل الكناري</h1><p>إجابات عملية تساعدك على فهم طائرك والعناية به واتخاذ قرار أوضح قبل الشراء.</p></div></section>
<section class="section-band"><div class="container">
    <div class="section-heading"><div><span class="kicker">ابدأ من الموضوع</span><h2>أقسام الدليل</h2></div></div>
    <div class="guide-category-grid">
        @foreach($categories as $category)
            <a class="guide-category-card" href="{{ route('guide.category',$category) }}">
                <img src="{{ $category->image ?: '/images/birds/classic-canary.jpg' }}" alt="{{ $category->name }}" loading="lazy">
                <span></span><div><small>{{ $category->published_articles_count }} مقال</small><h2>{{ $category->name }}</h2><p>{{ $category->description }}</p><i data-lucide="arrow-up-left"></i></div>
            </a>
        @endforeach
    </div>
</div></section>
@if($latestArticles->count())
<section class="section-band section-muted"><div class="container"><div class="section-heading"><div><span class="kicker">محتوى مختار</span><h2>أحدث المقالات</h2></div></div><div class="article-grid">@foreach($latestArticles as $article)<x-article-card :article="$article" />@endforeach</div></div></section>
@endif
@endsection
