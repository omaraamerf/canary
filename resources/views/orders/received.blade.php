@extends('layouts.app')

@section('title', __('ui.tracking.received_title'))

@section('content')
<section class="success-page">
    <div class="success-panel">
        <span class="success-icon"><x-lucide-check /></span>
        <span class="kicker">{{ __('ui.tracking.received') }}</span>
        <h1>{{ __('ui.tracking.can_track') }}</h1>
        <p>{{ __('ui.tracking.keep_reference') }}</p>

        <strong class="reference">{{ $order->reference }}</strong>

        <div class="success-summary">
            <span>{{ __('ui.tracking.bird') }}<b>{{ $order->bird->title }}</b></span>
            <span>{{ __('ui.common.status') }}<b>{{ \App\Enums\OrderStatus::from($order->status)->label() }}</b></span>
            <span>{{ __('ui.common.price') }}<b>{{ number_format((float) $order->price_snapshot, 2) }} {{ $order->currency_snapshot }}</b></span>
        </div>

        <div class="tracking-actions">
            <a class="btn btn-primary btn-large" href="{{ route('orders.track.show', $order) }}">
                <x-lucide-map-pin-check /> {{ __('ui.tracking.track') }}
            </a>
            <a class="btn btn-dark" href="{{ route('birds.index') }}">
                <x-lucide-arrow-right /> {{ __('ui.tracking.back_birds') }}
            </a>
        </div>
    </div>
</section>
@endsection
