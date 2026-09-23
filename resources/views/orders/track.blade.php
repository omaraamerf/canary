@extends('layouts.app')

@section('title', 'تتبع الطلب')

@section('content')
<section class="success-page">
    <div class="success-panel tracking-panel">
        <span class="success-icon"><i data-lucide="search"></i></span>
        <span class="kicker">تتبع طلبك</span>
        <h1>أدخل بيانات الطلب</h1>
        <p>استخدم رقم الطلب ورقم الهاتف المسجل أثناء الحجز.</p>

        @if($errors->any())
            <div class="form-errors">{{ $errors->first() }}</div>
        @endif

        <form class="tracking-form" method="post" action="{{ route('orders.track.lookup') }}">
            @csrf
            <label>
                رقم الطلب
                <input name="reference" value="{{ old('reference', $reference) }}" placeholder="CNY-XXXXXXX" required dir="ltr">
            </label>
            <label>
                رقم الهاتف
                <input name="phone" value="{{ old('phone') }}" placeholder="رقم الهاتف المستخدم في الطلب" required dir="ltr">
            </label>
            <button class="btn btn-primary btn-large" type="submit">
                <i data-lucide="search"></i> عرض حالة الطلب
            </button>
        </form>
    </div>
</section>
@endsection
