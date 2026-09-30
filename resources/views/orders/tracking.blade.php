@extends('layouts.app')

@section('title', __('ui.tracking.title').' '.$order->reference)

@section('content')
<section class="success-page">
    <div class="success-panel tracking-panel">
        <span class="success-icon"><i data-lucide="package-check"></i></span>
        <span class="kicker">{{ __('ui.tracking.status') }}</span>
        <h1>{{ \App\Enums\OrderStatus::from($order->status)->label() }}</h1>
        <p>{{ __('ui.tracking.reference_label') }} <b dir="ltr">{{ $order->reference }}</b></p>

        <div class="success-summary">
            <span>{{ __('ui.tracking.bird') }}<b>{{ $order->bird->title }}</b></span>
            <span>{{ __('ui.common.region') }}<b>{{ $order->buyerRegion?->name ?: $order->city }}</b></span>
            <span>{{ __('ui.tracking.date') }}<b>{{ $order->created_at->format('Y-m-d H:i') }}</b></span>
        </div>

        <div class="tracking-timeline">
            <h2>{{ __('ui.tracking.stages') }}</h2>
            @foreach($statusLogs as $log)
                <div class="tracking-step">
                    <span class="tracking-dot"></span>
                    <div>
                        <b>{{ \App\Enums\OrderStatus::from($log->new_status)->label() }}</b>
                        <time>{{ $log->created_at->format('Y-m-d H:i') }}</time>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="tracking-actions">
            <a class="btn btn-outline" href="{{ route('orders.track', ['reference' => $order->reference]) }}">{{ __('ui.tracking.another') }}</a>
            <a class="btn btn-dark" href="{{ route('birds.index') }}">{{ __('ui.tracking.back_birds') }}</a>
        </div>
    </div>
</section>
@endsection
