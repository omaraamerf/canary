@extends('layouts.app')
@section('title',$seller->sellerProfile?->display_name ?: $seller->name)
@section('content')
<section class="page-head seller-profile-head"><div class="container"><span class="kicker">{{ __('ui.seller.public_label') }}</span><h1>{{ $seller->sellerProfile?->display_name ?: $seller->name }}</h1><p><x-lucide-map-pin /> {{ $seller->sellerProfile?->region?->name }} @if($seller->sellerProfile?->bio) · {{ $seller->sellerProfile->bio }} @endif</p></div></section><section class="section-band"><div class="container"><div class="section-heading"><div><span class="kicker">{{ __('ui.home.available_now') }}</span><h2>{{ __('ui.seller.birds') }}</h2></div></div>@if($birds->count())<div class="birds-grid">@foreach($birds as $bird)<x-bird-card :bird="$bird" />@endforeach</div>{{ $birds->links() }}@else<div class="empty-state"><x-lucide-bird /><h2>{{ __('ui.seller.no_birds') }}</h2></div>@endif</div></section>
@endsection
