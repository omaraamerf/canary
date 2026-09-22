@extends('layouts.admin')
@section('title','مقالات الدليل')
@section('heading','مقالات دليل الكناري')
@section('eyebrow','إنشاء المحتوى ونشره')
@section('content')
<section class="admin-section">
    <div class="admin-section-head">
        <div><h2>كل المقالات</h2><p>المسودات والمؤرشفة لا تظهر للزوار.</p></div>
        <div class="d-flex gap-2"><a class="btn btn-outline" href="{{ route('admin.guide-categories.index') }}"><i data-lucide="library"></i>الأقسام</a><a class="btn btn-primary" href="{{ route('admin.guide-articles.create') }}"><i data-lucide="plus"></i>مقال جديد</a></div>
    </div>
    <form class="inline-admin-form mb-3" method="get"><select name="status"><option value="">كل الحالات</option><option value="draft" @selected(request('status')==='draft')>مسودة</option><option value="published" @selected(request('status')==='published')>منشور</option><option value="archived" @selected(request('status')==='archived')>مؤرشف</option></select><select name="category"><option value="">كل الأقسام</option>@foreach($categories as $category)<option value="{{ $category->id }}" @selected((string)request('category')===(string)$category->id)>{{ $category->name }}</option>@endforeach</select><button class="btn btn-outline" type="submit"><i data-lucide="list-filter"></i>تصفية</button></form>
    <div class="table-wrap"><table><thead><tr><th>المقال</th><th>القسم</th><th>الحالة</th><th>آخر تحديث</th><th>إجراء</th></tr></thead><tbody>
        @forelse($articles as $article)<tr><td><b>{{ $article->title }}</b><small class="d-block text-muted mt-1">{{ \Illuminate\Support\Str::limit($article->summary,90) }}</small></td><td>{{ $article->category->name }}</td><td><span class="order-status status-{{ $article->status === 'published' ? 'delivered' : ($article->status === 'draft' ? 'pending' : 'cancelled') }}">{{ ['draft'=>'مسودة','published'=>'منشور','archived'=>'مؤرشف'][$article->status] }}</span></td><td>{{ $article->updated_at->format('Y/m/d') }}</td><td><a class="icon-btn" href="{{ route('admin.guide-articles.edit',$article) }}" aria-label="تعديل"><i data-lucide="pencil"></i></a></td></tr>
        @empty<tr><td colspan="5" class="empty-cell">لا توجد مقالات.</td></tr>@endforelse
    </tbody></table></div>{{ $articles->links() }}
</section>
@endsection
