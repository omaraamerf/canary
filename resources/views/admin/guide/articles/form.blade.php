@extends('layouts.admin')
@php($editing=$article->exists)
@section('title',$editing?'تعديل مقال':'مقال جديد')
@section('heading',$editing?'تعديل المقال':'إضافة مقال')
@section('eyebrow','محتوى دليل الكناري')
@section('content')
<form class="admin-form" method="post" action="{{ $editing ? route('admin.guide-articles.update',$article) : route('admin.guide-articles.store') }}">@csrf @if($editing)@method('put')@endif
    @if($errors->any())<div class="form-errors">{{ $errors->first() }}</div>@endif
    <section class="form-section"><div class="form-section-title"><h2>بيانات المقال</h2><p>عنوان واضح وملخص قصير يساعدان الزائر ومحركات البحث على فهم الموضوع.</p></div><div class="form-grid">
        <label class="span-2">العنوان<input name="title" required maxlength="190" value="{{ old('title',$article->title) }}"></label>
        <label>القسم<select name="category_id" required><option value="">اختر القسم</option>@foreach($categories as $category)<option value="{{ $category->id }}" @selected((string)old('category_id',$article->category_id)===(string)$category->id)>{{ $category->name }}</option>@endforeach</select></label>
        <label>الصورة الرئيسية<input name="featured_image" value="{{ old('featured_image',$article->featured_image) }}" placeholder="/images/birds/example.jpg أو رابط كامل"></label>
        <label class="span-2">الملخص<textarea name="summary" required rows="3" maxlength="1000">{{ old('summary',$article->summary) }}</textarea></label>
        <label class="span-2">المحتوى<textarea name="content" required rows="18" placeholder="اكتب المحتوى بفقرات واضحة، واترك سطرًا فارغًا بين الفقرات.">{{ old('content',$article->content) }}</textarea></label>
        <label class="span-2">الوسوم<input name="tags" value="{{ old('tags',$article->tags?->pluck('name')->join('، ')) }}" placeholder="العناية، التغذية، المبتدئ"></label>
    </div></section>
    <section class="form-section"><div class="form-section-title"><h2>النشر</h2><p>المقال المنشور فقط يظهر في الدليل، ويمكن أرشفته دون حذفه.</p></div><div class="form-grid">
        <label>الحالة<select name="status" required><option value="draft" @selected(old('status',$article->status ?: 'draft')==='draft')>مسودة</option><option value="published" @selected(old('status',$article->status)==='published')>منشور</option><option value="archived" @selected(old('status',$article->status)==='archived')>مؤرشف</option></select></label>
        <label>تاريخ النشر<input type="datetime-local" name="published_at" value="{{ old('published_at',$article->published_at?->format('Y-m-d\TH:i')) }}"></label>
    </div></section>
    <div class="form-actions"><a class="btn btn-outline" href="{{ route('admin.guide-articles.index') }}">إلغاء</a><button class="btn btn-primary btn-large" type="submit"><i data-lucide="save"></i>حفظ المقال</button></div>
</form>
@endsection
