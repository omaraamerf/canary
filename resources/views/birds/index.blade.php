@extends('layouts.app')
@section('title', 'كل الطيور')
@section('content')
<section class="page-head"><div class="container"><span class="kicker">المعروض حاليًا</span><h1>كل الطيور</h1><p>نتائج حقيقية متاحة للحجز الآن.</p></div></section>
<section class="section-band pt-8"><div class="container catalog-layout">
    <aside class="filters">
        <div class="filter-title"><h2>تصفية النتائج</h2><a href="{{ route('birds.index') }}">مسح</a></div>
        <form action="{{ route('birds.index') }}" method="get" class="filter-form">
            <label>بحث<input type="search" name="q" value="{{ request('q') }}" placeholder="اسم أو لون"></label>
            <label>السلالة<select name="breed"><option value="">كل السلالات</option>@foreach($breeds as $breed)<option value="{{ $breed->slug }}" @selected(request('breed') === $breed->slug)>{{ $breed->name }}</option>@endforeach</select></label>
            <label>الجنس<select name="sex"><option value="">الكل</option><option value="male" @selected(request('sex') === 'male')>ذكر</option><option value="female" @selected(request('sex') === 'female')>أنثى</option><option value="unknown" @selected(request('sex') === 'unknown')>غير محدد</option></select></label>
            <label>المدينة<select name="city"><option value="">كل المدن</option>@foreach($cities as $city)<option value="{{ $city }}" @selected(request('city') === $city)>{{ $city }}</option>@endforeach</select></label>
            <div class="two-inputs"><label>السعر من<input type="number" name="min_price" value="{{ request('min_price') }}"></label><label>إلى<input type="number" name="max_price" value="{{ request('max_price') }}"></label></div>
            <label class="check-row"><input type="checkbox" name="delivery" value="1" @checked(request('delivery'))><span>التوصيل متاح</span></label>
            <input type="hidden" name="sort" value="{{ request('sort') }}"><button class="btn btn-primary w-full" type="submit"><i data-lucide="list-filter"></i> تطبيق الفلاتر</button>
        </form>
    </aside>
    <div>
        <div class="results-toolbar"><span><strong>{{ $birds->total() }}</strong> نتيجة</span><form><input type="hidden" name="q" value="{{ request('q') }}"><input type="hidden" name="breed" value="{{ request('breed') }}"><select name="sort" onchange="this.form.submit()" aria-label="ترتيب النتائج"><option value="newest">الأحدث</option><option value="price_asc" @selected(request('sort') === 'price_asc')>الأقل سعرًا</option><option value="price_desc" @selected(request('sort') === 'price_desc')>الأعلى سعرًا</option></select></form></div>
        @if($birds->count())<div class="birds-grid catalog-grid">@foreach($birds as $bird)<x-bird-card :bird="$bird" />@endforeach</div>{{ $birds->links() }}@else<div class="empty-state"><i data-lucide="bird"></i><h2>لا توجد نتائج مطابقة</h2><p>جرّب تغيير السلالة أو نطاق السعر.</p><a class="btn btn-dark" href="{{ route('birds.index') }}">عرض كل الطيور</a></div>@endif
    </div>
</div></section>
@endsection
