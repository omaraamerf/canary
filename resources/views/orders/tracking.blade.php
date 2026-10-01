@extends('layouts.app')

@section('title', __('ui.tracking.title').' '.$order->reference)

@section('content')
@php
    $status = \App\Enums\OrderStatus::from($order->status);
    $flow = [\App\Enums\OrderStatus::Pending, \App\Enums\OrderStatus::Confirmed, \App\Enums\OrderStatus::Preparing, \App\Enums\OrderStatus::OutForDelivery, \App\Enums\OrderStatus::Delivered];
    $reached = array_search($status, $flow, true);
@endphp
<section class="order-page">
    <div class="container order-layout">
        <div class="order-card order-main">
            <span class="kicker">{{ __('ui.tracking.status') }}</span>
            <h1>{{ $status->label() }}</h1>
            <p class="order-reference">{{ __('ui.tracking.reference_label') }} <b dir="ltr">{{ $order->reference }}</b></p>

            @if($status === \App\Enums\OrderStatus::Cancelled)
                <div class="alert alert-danger"><x-lucide-circle-x />{{ __('ui.tracking.cancelled_note') }}</div>
            @else
                <ol class="order-stepper" aria-label="{{ __('ui.tracking.progress') }}">
                    @foreach($flow as $step)
                        <li @class(['is-done' => $loop->index < $reached, 'is-current' => $loop->index === $reached]) @if($loop->index === $reached) aria-current="step" @endif>
                            <span class="order-step-dot">@if($loop->index < $reached)<x-lucide-check />@else{{ $loop->iteration }}@endif</span>
                            <span>{{ $step->label() }}</span>
                        </li>
                    @endforeach
                </ol>
            @endif

            <div class="tracking-timeline">
                <h2>{{ __('ui.tracking.stages') }}</h2>
                @foreach($statusLogs as $log)
                    <div class="tracking-step">
                        <span class="tracking-dot"></span>
                        <div>
                            <b>{{ \App\Enums\OrderStatus::from($log->new_status)->label() }}</b>
                            <time datetime="{{ $log->created_at->toIso8601String() }}">{{ $log->created_at->format('Y-m-d H:i') }}</time>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="tracking-actions">
                <x-ui.button variant="outline" :href="route('orders.track', ['reference' => $order->reference])">{{ __('ui.tracking.another') }}</x-ui.button>
                <x-ui.button variant="dark" :href="route('birds.index')">{{ __('ui.tracking.back_birds') }}</x-ui.button>
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
                <div><dt>{{ __('ui.common.price') }}</dt><dd>{{ number_format((float) $order->price_snapshot) }} {{ \App\Enums\Currency::labelFor($order->currency_snapshot) }}</dd></div>
                <div><dt>{{ __('ui.common.region') }}</dt><dd>{{ $order->buyerRegion?->name ?: $order->city }}</dd></div>
                <div><dt>{{ __('ui.tracking.date') }}</dt><dd>{{ $order->created_at->format('Y-m-d H:i') }}</dd></div>
            </dl>
        </aside>
    </div>
</section>
@endsection
