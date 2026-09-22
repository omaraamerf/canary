@extends('layouts.app')
@section('title', $bird->title)
@section('content')
<section class="detail-section"><div class="container">
    <nav class="breadcrumbs"><a href="{{ route('home') }}">الرئيسية</a><i data-lucide="chevron-left"></i><a href="{{ route('birds.index') }}">الطيور</a><i data-lucide="chevron-left"></i><span>{{ $bird->title }}</span></nav>
    <div class="detail-grid">
        <div class="gallery">
            <div class="gallery-main"><img data-gallery-main src="{{ $bird->primary_image }}" alt="{{ $bird->title }}"><span class="status-pill status-{{ $bird->status }}">{{ ['available'=>'متاح','reserved'=>'محجوز','sold'=>'مباع'][$bird->status] }}</span></div>
            @if($bird->media->where('type', 'image')->count() > 1)<div class="gallery-thumbs">@foreach($bird->media->where('type', 'image') as $media)<button data-gallery-image="{{ $media->url }}"><img src="{{ $media->url }}" alt="صورة إضافية"></button>@endforeach</div>@endif
        </div>
        <div class="detail-info">
            <span class="kicker">{{ $bird->breed->name }}</span><h1>{{ $bird->title }}</h1>
            <div class="detail-location"><span><i data-lucide="map-pin"></i>{{ $bird->city }}@if($bird->region)، {{ $bird->region->name }}@endif</span><span><i data-lucide="truck"></i>{{ ['pickup'=>'استلام شخصي','delivery'=>'توصيل متاح','agreement'=>'بالاتفاق'][$bird->delivery_type] }}</span></div>
            <div class="price-line"><strong>{{ number_format($bird->price) }}</strong><span>ر.س</span></div>
            <p class="detail-description">{{ $bird->description }}</p>
            <dl class="spec-grid">
                <div><dt>الجنس</dt><dd>{{ ['male'=>'ذكر','female'=>'أنثى','unknown'=>'غير محدد'][$bird->sex] }}</dd></div>
                <div><dt>سنة الفقس</dt><dd>{{ $bird->hatch_year ?: 'غير معروف' }}</dd></div>
                <div><dt>اللون</dt><dd>{{ $bird->color }}</dd></div>
                <div><dt>مرحلة الريش</dt><dd>{{ ['ready'=>'جاهز','young'=>'فرخ','molting'=>'غيار ريش'][$bird->molt_status] ?? 'غير محدد' }}</dd></div>
                <div><dt>جاهزية التزاوج</dt><dd>{{ is_null($bird->breeding_ready) ? 'غير معروف' : ($bird->breeding_ready ? 'جاهز' : 'غير جاهز') }}</dd></div>
                <div><dt>التغريد</dt><dd>{{ ['singing'=>'يغرد','not_singing'=>'لا يغرد','young'=>'فرخ','female'=>'أنثى'][$bird->singing_status] ?? 'غير معروف' }}</dd></div>
            </dl>
            @if($bird->status === 'available')<a href="#reserve" class="btn btn-primary btn-large"><i data-lucide="bookmark-check"></i> اطلب حجز الطائر</a>@else<div class="notice">هذا الطائر غير متاح حاليًا. يمكنك مشاهدة الطيور الأخرى المتوفرة.</div>@endif
            @if($bird->seller->role === 'seller')<a class="seller-card" href="{{ route('sellers.show',$bird->seller) }}"><span class="seller-card-icon"><i data-lucide="store"></i></span><span><small>البائع</small><strong>{{ $bird->seller->sellerProfile?->display_name ?: $bird->seller->name }}</strong><em>{{ $bird->city }}@if($bird->region)، {{ $bird->region->name }}@endif</em></span><i data-lucide="arrow-left"></i></a>@else<div class="seller-card"><span class="seller-card-icon"><i data-lucide="store"></i></span><span><small>البائع</small><strong>إدارة كناري</strong><em>{{ $bird->city }}@if($bird->region)، {{ $bird->region->name }}@endif</em></span><i data-lucide="badge-check"></i></div>@endif
        </div>
    </div>

    @if($bird->media->where('type', 'video')->count())
        <section class="video-section"><div class="section-heading"><div><span class="kicker">شاهد الطائر</span><h2>الفيديو والتغريد</h2></div></div><div class="video-grid">@foreach($bird->media->where('type', 'video') as $video)<div class="video-frame">@if($video->isCloudinary())<video src="{{ $video->url }}" controls preload="metadata" playsinline title="فيديو {{ $bird->title }}"></video>@else<iframe src="{{ $video->embed_url }}" allow="autoplay" allowfullscreen title="فيديو {{ $bird->title }}"></iframe>@endif<span class="video-brand-cover" aria-hidden="true"><i data-lucide="bird"></i></span></div>@endforeach</div></section>
    @endif

    @if($bird->status === 'available')
    <section id="reserve" class="reserve-section"><div><span class="kicker">طلب غير ملزم بالدفع</span><h2>أرسل طلب الحجز</h2><p>بعد إرسال الطلب سنتواصل معك للتأكد من التفاصيل والتوفر وطريقة التسليم.</p><ul><li><i data-lucide="check"></i> لا يوجد دفع إلكتروني</li><li><i data-lucide="check"></i> السعر محفوظ داخل الطلب</li><li><i data-lucide="check"></i> التأكيد يتم بالتواصل المباشر</li></ul></div>
        <form action="{{ route('orders.store', $bird) }}" method="post" class="reserve-form">@csrf
            @if($errors->any())<div class="form-errors">{{ $errors->first() }}</div>@endif
            <label>الاسم الكامل<input required name="buyer_name" value="{{ old('buyer_name') }}" autocomplete="name"></label>
            <label>رقم الهاتف<input required name="phone" value="{{ old('phone') }}" inputmode="tel" autocomplete="tel" placeholder="05xxxxxxxx"></label>
            <label>المنطقة<select required name="buyer_region_id"><option value="">اختر منطقتك</option>@foreach($navigationRegions as $region)<option value="{{ $region->id }}" @selected((string)old('buyer_region_id',$selectedRegion?->id)===(string)$region->id)>{{ $region->name }}</option>@endforeach</select></label>
            <div class="delivery-summary full"><span>طريقة الاستلام التي حددها البائع</span><strong>{{ ['pickup' => 'استلام شخصي', 'delivery' => 'توصيل', 'agreement' => 'بالاتفاق'][$bird->delivery_type] }}</strong></div>
            <label class="full">ملاحظات<textarea name="notes" rows="3" placeholder="وقت مناسب للتواصل أو أي سؤال">{{ old('notes') }}</textarea></label>
            <button class="btn btn-primary btn-large full" type="submit"><i data-lucide="send"></i> إرسال طلب الحجز</button>
            <small class="full">بإرسال الطلب أنت توافق على سياسة الحجز والخصوصية.</small>
        </form>
    </section>
    @endif
</div></section>
@if($relatedBirds->count())<section class="section-band section-muted"><div class="container"><div class="section-heading"><div><span class="kicker">خيارات أخرى</span><h2>من نفس السلالة</h2></div></div><div class="birds-grid">@foreach($relatedBirds as $related)<x-bird-card :bird="$related" />@endforeach</div></div></section>@endif
@endsection
