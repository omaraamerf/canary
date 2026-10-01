@extends('layouts.app')

@section('title', __('ui.tracking.received_title'))

@section('content')
<section class="order-page">
    <div class="container order-layout">
        <div class="order-card order-main">
            <span class="success-icon"><x-lucide-check /></span>
            <span class="kicker">{{ __('ui.tracking.received') }}</span>
            <h1>{{ __('ui.tracking.can_track') }}</h1>
            <p>{{ __('ui.tracking.keep_reference') }}</p>
            <p class="reference" dir="ltr">{{ $order->reference }}</p>

            <ol class="order-stepper" aria-label="{{ __('ui.tracking.progress') }}">
                <li class="is-current" aria-current="step"><span class="order-step-dot">1</span><span>{{ __('ui.order_status.pending') }}</span></li>
                <li><span class="order-step-dot">2</span><span>{{ __('ui.order_status.confirmed') }}</span></li>
                <li><span class="order-step-dot">3</span><span>{{ __('ui.order_status.delivered') }}</span></li>
            </ol>

            <div class="tracking-actions">
                <x-ui.button size="lg" icon="map-pin-check" :href="route('orders.track.show', $order)">{{ __('ui.tracking.track') }}</x-ui.button>
                <x-ui.button variant="outline" :href="route('birds.index')">{{ __('ui.tracking.back_birds') }}</x-ui.button>
            </div>
        </div>

        <aside class="order-card order-aside">
            <h2>{{ __('ui.tracking.order_summary') }}</h2>
            @if($order->bird)
                <a class="order-bird" href="{{ route('birds.show', $order->bird) }}">
                    <img src="{{ $order->bird->primary_image }}" alt="" loading="lazy">
                    <span><small>{{ __('ui.tracking.bird') }}</small><strong>{{ $order->bird->title }}</strong></span>
                </a>
            @endif
            <dl class="order-facts">
                <div><dt>{{ __('ui.common.status') }}</dt><dd>{{ \App\Enums\OrderStatus::from($order->status)->label() }}</dd></div>
                <div><dt>{{ __('ui.common.price') }}</dt><dd>{{ number_format((float) $order->price_snapshot) }} {{ \App\Enums\Currency::labelFor($order->currency_snapshot) }}</dd></div>
            </dl>
        </aside>
    </div>
</section>
@endsection
