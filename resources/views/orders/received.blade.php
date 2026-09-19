@extends('layouts.app')
@section('title', 'تم استلام الطلب')
@section('content')
<section class="success-page"><div class="success-panel"><span class="success-icon"><i data-lucide="check"></i></span><span class="kicker">تم استلام طلبك</span><h1>سنراجع الحجز ونتواصل معك</h1><p>احتفظ برقم الطلب التالي عند التواصل معنا.</p><strong class="reference">{{ $order->reference }}</strong><div class="success-summary"><span>الطائر<b>{{ $order->bird->title }}</b></span><span>الحالة<b>طلب جديد</b></span><span>السعر<b>{{ number_format($order->price_snapshot) }} ر.س</b></span></div><a class="btn btn-dark" href="{{ route('birds.index') }}"><i data-lucide="arrow-right"></i> العودة إلى الطيور</a></div></section>
@endsection
