@extends('layouts.app')
@section('title','ابدأ البيع')
@section('content')
<section class="content-page seller-start-page"><div class="container narrow"><span class="kicker">للمربين والبائعين</span><h1>اعرض طيورك أمام المشترين في منطقتك</h1><p class="lead">أنشئ ملف البائع، أضف بيانات الطائر وصوره وفيديوه، ثم تابع طلبات الحجز من لوحة واحدة.</p><div class="seller-steps"><div><b>1</b><h2>أنشئ حسابك</h2><p>أدخل بياناتك والمنطقة التي تعمل فيها.</p></div><div><b>2</b><h2>أضف الطائر</h2><p>حدد السعر وطريقة التسليم وارفع الصور والفيديو.</p></div><div><b>3</b><h2>تابع الطلب</h2><p>أكد الحجز وحدّث حالة الطلب حتى التسليم.</p></div></div><div class="d-flex gap-2 flex-wrap"><a class="btn btn-primary btn-large" href="{{ route('seller.register') }}">إنشاء حساب بائع</a><a class="btn btn-dark btn-large" href="{{ route('filament.seller.auth.login') }}">دخول البائع</a></div></div></section>
@endsection
