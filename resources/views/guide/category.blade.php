@extends('layouts.app')
@section('title', $category->name.' | '.__('ui.guide.title'))
@section('meta_description', $category->description ?: __('ui.guide.description'))
@section('content')
<section class="page-head"><div class="container"><nav class="breadcrumbs"><a href="{{ route('home') }}">{{ __('ui.nav.home') }}</a><x-lucide-chevron-left /><a href="{{ route('guide.index') }}">{{ __('ui.guide.title') }}</a><x-lucide-chevron-left /><span>{{ $category->name }}</span></nav><span class="kicker">{{ __('ui.guide.category') }}</span><h1>{{ $category->name }}</h1><p>{{ $category->description }}</p></div></section>
<section class="section-band"><div class="container">
    @if($articles->count())<div class="article-grid">@foreach($articles as $article)<x-article-card :article="$article" />@endforeach</div>{{ $articles->links() }}
    @else<x-ui.empty-state icon="book-open" :title="__('ui.guide.empty')" :text="__('ui.guide.empty_help')"><x-ui.button variant="dark" :href="route('guide.index')">{{ __('ui.guide.back') }}</x-ui.button></x-ui.empty-state>@endif
</div></section>
@endsection
