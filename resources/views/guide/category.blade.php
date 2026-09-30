@extends('layouts.app')
@section('title', $category->name.' | '.__('ui.guide.title'))
@section('meta_description', $category->description ?: __('ui.guide.description'))
@section('content')
<section class="page-head"><div class="container"><nav class="breadcrumbs"><a href="{{ route('home') }}">{{ __('ui.nav.home') }}</a><i data-lucide="chevron-left"></i><a href="{{ route('guide.index') }}">{{ __('ui.guide.title') }}</a><i data-lucide="chevron-left"></i><span>{{ $category->name }}</span></nav><span class="kicker">{{ __('ui.guide.category') }}</span><h1>{{ $category->name }}</h1><p>{{ $category->description }}</p></div></section>
<section class="section-band"><div class="container">
    @if($articles->count())<div class="article-grid">@foreach($articles as $article)<x-article-card :article="$article" />@endforeach</div>{{ $articles->links() }}
    @else<div class="empty-state"><i data-lucide="book-open"></i><h2>{{ __('ui.guide.empty') }}</h2><p>{{ __('ui.guide.empty_help') }}</p><a class="btn btn-dark" href="{{ route('guide.index') }}">{{ __('ui.guide.back') }}</a></div>@endif
</div></section>
@endsection
