@extends('layouts.app')
@section('title', __('ui.pages.sell.title'))
@section('content')
<section class="content-page seller-start-page"><div class="container narrow"><span class="kicker">{{ __('ui.pages.sell.eyebrow') }}</span><h1>{{ __('ui.pages.sell.heading') }}</h1><p class="lead">{{ __('ui.pages.sell.lead') }}</p><div class="seller-steps"><div><b>1</b><h2>{{ __('ui.pages.sell.create') }}</h2><p>{{ __('ui.pages.sell.create_text') }}</p></div><div><b>2</b><h2>{{ __('ui.pages.sell.add') }}</h2><p>{{ __('ui.pages.sell.add_text') }}</p></div><div><b>3</b><h2>{{ __('ui.pages.sell.follow') }}</h2><p>{{ __('ui.pages.sell.follow_text') }}</p></div></div><div class="d-flex gap-2 flex-wrap"><a class="btn btn-primary btn-large" href="{{ route('seller.register') }}">{{ __('ui.pages.sell.register') }}</a><a class="btn btn-dark btn-large" href="{{ route('filament.seller.auth.login') }}">{{ __('ui.pages.sell.login') }}</a></div></div></section>
@endsection
