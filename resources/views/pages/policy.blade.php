@extends('layouts.app')
@section('title', __('ui.pages.policy.title'))
@section('content')
<section class="content-page"><div class="container narrow"><span class="kicker">{{ __('ui.pages.policy.eyebrow') }}</span><h1>{{ __('ui.pages.policy.heading') }}</h1><p class="lead">{{ __('ui.pages.policy.lead') }}</p><h2>{{ __('ui.pages.policy.confirm') }}</h2><p>{{ __('ui.pages.policy.confirm_text') }}</p><h2>{{ __('ui.pages.policy.payment') }}</h2><p>{{ __('ui.pages.policy.payment_text') }}</p><h2>{{ __('ui.pages.policy.data') }}</h2><p>{{ __('ui.pages.policy.data_text') }}</p><h2>{{ __('ui.pages.policy.bird') }}</h2><p>{{ __('ui.pages.policy.bird_text') }}</p></div></section>
@endsection
