@extends('layouts.app')
@section('title', __('ui.pages.about.title'))
@section('content')
<section class="content-page"><div class="container narrow"><span class="kicker">{{ __('ui.pages.about.eyebrow') }}</span><h1>{{ __('ui.pages.about.heading') }}</h1><p class="lead">{{ __('ui.pages.about.lead') }}</p><h2>{{ __('ui.pages.about.what') }}</h2><p>{{ __('ui.pages.about.what_text') }}</p><h2>{{ __('ui.pages.about.how') }}</h2><p>{{ __('ui.pages.about.how_text') }}</p></div></section>
@endsection
