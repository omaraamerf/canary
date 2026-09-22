@extends('layouts.app')
@section('title',$seller->sellerProfile?->display_name ?: $seller->name)
@section('content')
<section class="page-head seller-profile-head"><div class="container"><span class="kicker">بائع على كناري</span><h1>{{ $seller->sellerProfile?->display_name ?: $seller->name }}</h1><p><i data-lucide="map-pin"></i> {{ $seller->sellerProfile?->region?->name }} @if($seller->sellerProfile?->bio) · {{ $seller->sellerProfile->bio }} @endif</p></div></section><section class="section-band"><div class="container"><div class="section-heading"><div><span class="kicker">المتاح حاليًا</span><h2>طيور البائع</h2></div></div>@if($birds->count())<div class="birds-grid">@foreach($birds as $bird)<x-bird-card :bird="$bird" />@endforeach</div>{{ $birds->links() }}@else<div class="empty-state"><i data-lucide="bird"></i><h2>لا توجد طيور متاحة الآن</h2></div>@endif</div></section>
@endsection
