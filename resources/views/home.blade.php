@extends('layouts.app')
@section('title', 'الرئيسية')
@section('content')
<section class="home-intro">
    <div class="container home-intro-grid">
        <div class="intro-copy">
            <span class="eyebrow"><i data-lucide="shield-check"></i> طيور مختارة ومعروضة بوضوح</span>
            <h1>ابحث عن الكناري المناسب لك</h1>
            <p>شاهد الحالة والسلالة والصور وفيديو التغريد، ثم أرسل طلب الحجز وسنتواصل معك للتأكيد.</p>
            <form action="{{ route('birds.index') }}" class="search-bar">
                <i data-lucide="search"></i>
                <input type="search" name="q" placeholder="ابحث بالسلالة أو اللون..." aria-label="البحث عن طائر">
                <button type="submit">بحث</button>
            </form>
            <div class="trust-row"><span><i data-lucide="badge-check"></i> بيانات واضحة</span><span><i data-lucide="phone"></i> تأكيد مباشر</span></div>
        </div>
        @if($featuredBirds->first())
            <a class="featured-visual" href="{{ route('birds.show', $featuredBirds->first()) }}">
                <img src="{{ $featuredBirds->first()->primary_image }}" alt="{{ $featuredBirds->first()->title }}">
                <div><span>اختيار مميز</span><strong>{{ $featuredBirds->first()->title }}</strong><small>{{ number_format($featuredBirds->first()->price) }} ر.س</small></div>
            </a>
        @endif
    </div>
</section>

<section class="section-band">
    <div class="container">
        <div class="section-heading"><div><span class="kicker">المتوفر الآن</span><h2>طيور جاهزة للحجز</h2></div><a class="text-link" href="{{ route('birds.index') }}">عرض الكل <i data-lucide="arrow-left"></i></a></div>
        <div class="birds-grid">@foreach($featuredBirds as $bird)<x-bird-card :bird="$bird" />@endforeach</div>
    </div>
</section>

<section class="section-band section-muted">
    <div class="container">
        <div class="section-heading"><div><span class="kicker">ابدأ من السلالة</span><h2>السلالات الأكثر توفرًا</h2></div></div>
        <div class="breed-list">
            @foreach($breeds as $breed)
                <a href="{{ route('birds.index', ['breed' => $breed->slug]) }}"><span>{{ sprintf('%02d', $loop->iteration) }}</span><div><strong>{{ $breed->name }}</strong><small>{{ $breed->description }}</small></div><b>{{ $breed->birds_count }} طائر</b><i data-lucide="arrow-up-left"></i></a>
            @endforeach
        </div>
    </div>
</section>

<section class="steps-band"><div class="container"><div class="section-heading"><div><span class="kicker">طلب بسيط</span><h2>من الاختيار إلى التأكيد</h2></div></div><div class="steps-grid"><div><span>1</span><i data-lucide="scan-search"></i><h3>اختر الطائر</h3><p>قارن السلالة والحالة والصور والفيديو.</p></div><div><span>2</span><i data-lucide="clipboard-pen-line"></i><h3>أرسل طلبك</h3><p>أدخل بيانات التواصل وطريقة الاستلام المناسبة.</p></div><div><span>3</span><i data-lucide="circle-check-big"></i><h3>نؤكد الحجز</h3><p>نتواصل معك ثم نثبت الطائر باسمك.</p></div></div></div></section>
@endsection
