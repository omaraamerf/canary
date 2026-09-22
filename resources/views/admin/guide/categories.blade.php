@extends('layouts.admin')
@section('title','أقسام الدليل')
@section('heading','أقسام دليل الكناري')
@section('eyebrow','تنظيم المحتوى')
@section('content')
<section class="admin-section">
    <div class="admin-section-head"><div><h2>إضافة قسم</h2><p>تظهر الأقسام كبطاقات رئيسية في صفحة الدليل.</p></div><a class="btn btn-outline" href="{{ route('admin.guide-articles.index') }}"><i data-lucide="files"></i>المقالات</a></div>
    @if($errors->any())<div class="form-errors mb-3">{{ $errors->first() }}</div>@endif
    <form class="inline-admin-form" method="post" action="{{ route('admin.guide-categories.store') }}">@csrf
        <input name="name" required placeholder="اسم القسم">
        <input name="description" placeholder="وصف مختصر">
        <input name="image" placeholder="رابط الصورة">
        <input type="number" name="sort_order" min="0" value="0" placeholder="الترتيب">
        <button class="btn btn-primary" type="submit"><i data-lucide="plus"></i>إضافة</button>
    </form>
</section>
<section class="admin-section mt-3">
    <div class="admin-section-head"><div><h2>الأقسام الحالية</h2></div></div>
    <div class="table-wrap"><table><thead><tr><th>القسم</th><th>المقالات</th><th>التعديل</th></tr></thead><tbody>
        @foreach($categories as $category)<tr>
            <td><b>{{ $category->name }}</b><small class="d-block text-muted mt-1">{{ $category->description }}</small></td><td>{{ $category->articles_count }}</td>
            <td><form class="inline-admin-form" method="post" action="{{ route('admin.guide-categories.update',$category) }}">@csrf @method('put')<input name="name" required value="{{ $category->name }}"><input name="description" value="{{ $category->description }}"><input name="image" value="{{ $category->image }}" placeholder="رابط الصورة"><input type="number" name="sort_order" min="0" value="{{ $category->sort_order }}"><button class="btn btn-outline" type="submit">حفظ</button></form></td>
        </tr>@endforeach
    </tbody></table></div>
</section>
@endsection
