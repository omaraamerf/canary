@extends('layouts.app')

@section('title', 'تم استلام الطلب')

@section('content')
<section class="success-page">
    <div class="success-panel">
        <span class="success-icon"><i data-lucide="check"></i></span>
        <span class="kicker">تم استلام طلبك</span>
        <h1>يمكنك متابعة حالة الطلب</h1>
        <p>احتفظ برقم الطلب، وستحتاج إليه مع رقم هاتفك إذا فتحت التتبع من جهاز آخر.</p>

        <strong class="reference">{{ $order->reference }}</strong>

        <div class="success-summary">
            <span>الطائر<b>{{ $order->bird->title }}</b></span>
            <span>الحالة<b>{{ \App\Enums\OrderStatus::from($order->status)->label() }}</b></span>
            <span>السعر<b>{{ number_format((float) $order->price_snapshot, 2) }} {{ $order->currency_snapshot }}</b></span>
        </div>

        <div class="tracking-actions">
            <a class="btn btn-primary btn-large" href="{{ route('orders.track.show', $order) }}">
                <i data-lucide="map-pin-check"></i> تتبع الطلب
            </a>
            <a class="btn btn-dark" href="{{ route('birds.index') }}">
                <i data-lucide="arrow-right"></i> العودة إلى الطيور
            </a>
        </div>
    </div>
</section>
@endsection
