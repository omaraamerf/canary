@extends('layouts.app')
@section('title',$category->name.' | دليل الكناري')
@section('meta_description',$category->description ?: 'مقالات دليل الكناري في '.$category->name)
@section('content')
<section class="page-head"><div class="container"><nav class="breadcrumbs"><a href="{{ route('home') }}">الرئيسية</a><i data-lucide="chevron-left"></i><a href="{{ route('guide.index') }}">دليل الكناري</a><i data-lucide="chevron-left"></i><span>{{ $category->name }}</span></nav><span class="kicker">قسم الدليل</span><h1>{{ $category->name }}</h1><p>{{ $category->description }}</p></div></section>
<section class="section-band"><div class="container">
    @if($articles->count())<div class="article-grid">@foreach($articles as $article)<x-article-card :article="$article" />@endforeach</div>{{ $articles->links() }}
    @else<div class="empty-state"><i data-lucide="book-open"></i><h2>لا توجد مقالات منشورة في هذا القسم بعد</h2><p>ستظهر المقالات هنا بعد نشرها من لوحة الإدارة.</p><a class="btn btn-dark" href="{{ route('guide.index') }}">العودة إلى الدليل</a></div>@endif
</div></section>
@endsection
