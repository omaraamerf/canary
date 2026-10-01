{{-- Local-only design system gallery (route "dev.ui"). Not translated on purpose. --}}
@extends('layouts.app')

@section('title', 'نظام التصميم')

@section('content')
<section class="page-head">
    <div class="container">
        <span class="kicker">للمطوّرين</span>
        <h1>نظام التصميم</h1>
        <p>كل المكوّنات المشتركة في مكان واحد. بدّل الوضع الليلي من الترويسة لمراجعة الوضعين.</p>
    </div>
</section>

<div class="container ui-gallery">
    <section>
        <h2>الألوان الدلالية</h2>
        <div class="ui-swatches">
            @foreach(['bg' => 'الخلفية', 'surface' => 'السطح', 'surface-2' => 'سطح ثانوي', 'sunken' => 'سطح غائر', 'fg' => 'النص', 'fg-2' => 'نص ثانوي', 'fg-muted' => 'نص خافت', 'border' => 'الحدود', 'primary' => 'الأساسي', 'accent' => 'التمييز', 'inverse' => 'المعكوس', 'danger' => 'الخطر'] as $token => $name)
                <figure><span style="background: var(--{{ $token }})"></span><figcaption>{{ $name }}<code dir="ltr">--{{ $token }}</code></figcaption></figure>
            @endforeach
        </div>
    </section>

    <section>
        <h2>الطباعة</h2>
        <p class="ui-type-h1">ابحث عن الكناري المناسب لك</p>
        <h3>عنوان فرعي بحجم البطاقات</h3>
        <p>نص أساسي بحجم 16 بكسل وارتفاع سطر مريح للقراءة العربية. الأرقام: 650 · 2025 · ١٥.</p>
        <p class="field-hint">نص مساعد بحجم 13 بكسل، وهو أصغر حجم مسموح في الواجهة.</p>
    </section>

    <section>
        <h2>الأزرار</h2>
        <div class="ui-row">
            <x-ui.button icon="send">أساسي</x-ui.button>
            <x-ui.button variant="dark" icon="search">داكن</x-ui.button>
            <x-ui.button variant="outline">ثانوي</x-ui.button>
            <x-ui.button variant="ghost">شبحي</x-ui.button>
            <x-ui.button variant="danger" icon="trash-2">حذف</x-ui.button>
            <x-ui.button aria-busy="true">جارٍ الإرسال</x-ui.button>
        </div>
        <div class="ui-row">
            <x-ui.button size="sm">صغير</x-ui.button>
            <x-ui.button>متوسط</x-ui.button>
            <x-ui.button size="lg" icon-end="arrow-left">كبير</x-ui.button>
            <x-ui.icon-button icon="search" label="بحث" />
            <x-ui.icon-button icon="menu" label="القائمة" variant="ghost" />
        </div>
    </section>

    <section>
        <h2>الشارات والرقائق</h2>
        <div class="ui-row">
            <x-ui.badge>محايد</x-ui.badge>
            <x-ui.badge variant="primary" icon="star">مميز</x-ui.badge>
            <x-ui.badge variant="success" icon="badge-check">بائع موثّق</x-ui.badge>
            <x-ui.badge variant="warning">محجوز</x-ui.badge>
            <x-ui.badge variant="danger">طائر مريض</x-ui.badge>
            <x-ui.badge variant="info">علاج</x-ui.badge>
            <x-ui.badge variant="inverse" icon="play">فيديو</x-ui.badge>
        </div>
        <div class="ui-row">
            <x-ui.chip :active="true">كل السلالات</x-ui.chip>
            <x-ui.chip>يوركشاير</x-ui.chip>
            <x-ui.chip icon="map-pin" icon-end="chevron-down">دمشق</x-ui.chip>
            <x-ui.chip href="#" icon-end="x">ذكر</x-ui.chip>
        </div>
    </section>

    <section>
        <h2>الحقول</h2>
        <div class="ui-form card">
            <x-ui.input name="demo_name" label="الاسم الكامل" required placeholder="مثال: أبو خالد" />
            <x-ui.input name="demo_email" type="email" label="البريد الإلكتروني" value="not-an-email" dir="ltr" />
            <x-ui.input name="demo_breed" type="select" label="السلالة" placeholder="اختر السلالة" :options="['yorkshire' => 'يوركشاير', 'gloster' => 'جلوستر']" />
            <x-ui.input name="demo_phone" type="tel" label="الهاتف" optional hint="سنستخدمه لتأكيد الحجز فقط." dir="ltr" />
            <x-ui.input name="demo_notes" type="textarea" label="ملاحظات" placeholder="وقت مناسب للتواصل أو أي سؤال" />
            <label class="check-row"><input type="checkbox" checked><span>تذكّرني</span></label>
        </div>
    </section>

    <section>
        <h2>التنبيهات والإشعارات</h2>
        <div class="ui-stack">
            <div class="alert alert-success"><x-lucide-circle-check />تم حفظ ملفك الشخصي.</div>
            <div class="alert alert-warning"><x-lucide-triangle-alert />الردود خبرات مربين ولا تغني عن الطبيب البيطري.</div>
            <div class="alert alert-danger"><x-lucide-circle-alert />بيانات الدخول غير صحيحة.</div>
            <div class="alert alert-info"><x-lucide-info />لا يوجد دفع إلكتروني في هذه المرحلة.</div>
            <div class="toast"><x-lucide-circle-check /><p>تم إرسال طلب الحجز، وسنتواصل معك قريباً.</p><x-ui.icon-button icon="x" label="إغلاق" variant="ghost" /></div>
        </div>
    </section>

    <section>
        <h2>الأوراق والقوائم</h2>
        <div class="ui-row">
            <x-ui.button variant="outline" icon="panels-top-left" data-sheet-open="demo-sheet">افتح ورقة</x-ui.button>
        </div>
        <x-ui.sheet id="demo-sheet" title="تصفية النتائج">
            <div class="ui-stack">
                <x-ui.input name="demo_sheet_breed" type="select" label="السلالة" :options="['' => 'كل السلالات', 'yorkshire' => 'يوركشاير']" />
                <x-ui.button block>عرض 6 نتائج</x-ui.button>
            </div>
        </x-ui.sheet>
    </section>

    <section>
        <h2>الحالة الفارغة</h2>
        <x-ui.empty-state icon="bird" title="لا توجد نتائج مطابقة" text="جرّب توسيع المنطقة أو إزالة بعض المرشّحات.">
            <x-ui.button variant="dark">مسح المرشّحات</x-ui.button>
        </x-ui.empty-state>
    </section>
</div>
@endsection
