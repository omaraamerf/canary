@extends('layouts.app')

@section('title', 'تتبع الطلب '.$order->reference)

@section('content')
<section class="success-page">
    <div class="success-panel tracking-panel">
        <span class="success-icon"><i data-lucide="package-check"></i></span>
        <span class="kicker">حالة الطلب</span>
        <h1>{{ \App\Enums\OrderStatus::from($order->status)->label() }}</h1>
        <p>رقم الطلب: <b dir="ltr">{{ $order->reference }}</b></p>

        <div class="success-summary">
            <span>الطائر<b>{{ $order->bird->title }}</b></span>
            <span>المنطقة<b>{{ $order->buyerRegion?->name ?: $order->city }}</b></span>
            <span>تاريخ الطلب<b>{{ $order->created_at->format('Y-m-d H:i') }}</b></span>
        </div>

        <div class="tracking-timeline">
            <h2>مراحل الطلب</h2>
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
            <a class="btn btn-outline" href="{{ route('orders.track', ['reference' => $order->reference]) }}">تتبع طلب آخر</a>
            <a class="btn btn-dark" href="{{ route('birds.index') }}">العودة إلى الطيور</a>
        </div>
    </div>
</section>
@endsection
