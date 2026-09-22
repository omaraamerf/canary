@extends('layouts.admin')
@section('title','الإعدادات') @section('heading','إعدادات الموقع') @section('eyebrow','التحكم في الخصائص والموافقات')
@section('content')
<form class="admin-form" method="post" action="{{ route('admin.settings.update') }}">@csrf @method('put')
    <section class="form-section"><div class="form-section-title"><h2>موافقة حسابات البائعين</h2><p>عند تشغيلها يبقى الحساب الجديد معلقًا حتى يقبله الأدمن.</p></div><div class="form-grid"><label class="check-row align-end"><input type="checkbox" name="seller_approval_required" value="1" @checked($sellerApprovalRequired)><span>تتطلب حسابات البائعين الجديدة موافقة الأدمن</span></label></div></section>
    <section class="form-section"><div class="form-section-title"><h2>موافقة الإعلانات</h2><p>عند تشغيلها يراجع الأدمن إعلان الطائر قبل ظهوره للعامة، وكذلك بعد تعديل البائع له.</p></div><div class="form-grid"><label class="check-row align-end"><input type="checkbox" name="listing_approval_required" value="1" @checked($listingApprovalRequired)><span>تتطلب إعلانات البائعين موافقة الأدمن</span></label></div></section>
    <section class="form-section"><div class="form-section-title"><h2>دليل الكناري</h2><p>يمكن إيقاف الدليل مؤقتًا مع الاحتفاظ بجميع التصنيفات والمقالات.</p></div><div class="form-grid"><label class="check-row align-end"><input type="checkbox" name="guide_enabled" value="1" @checked($guideEnabled)><span>إظهار دليل الكناري للزوار</span></label></div></section>
    <div class="form-actions"><button class="btn btn-primary btn-large" type="submit"><i data-lucide="save"></i>حفظ الإعدادات</button></div>
</form>
@endsection
