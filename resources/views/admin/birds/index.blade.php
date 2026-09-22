@extends('layouts.admin')
@section('title','الطيور')
@section('heading','إدارة الطيور')
@section('eyebrow','المخزون والإعلانات')
@section('content')
<section class="admin-section">
    <div class="admin-section-head">
        <div><h2>كل الإعلانات</h2><p>راجع إعلانات البائعين وحدّث حالتها وبياناتها.</p></div>
        <div class="d-flex gap-2"><form><select name="approval" onchange="this.form.submit()"><option value="">كل حالات المراجعة</option><option value="pending" @selected(request('approval')==='pending')>بانتظار المراجعة</option><option value="approved" @selected(request('approval')==='approved')>منشور</option><option value="rejected" @selected(request('approval')==='rejected')>مرفوض</option></select></form><a class="btn btn-primary" href="{{ route('admin.birds.create') }}"><i data-lucide="plus"></i>إضافة طائر</a></div>
    </div>
    <div class="table-wrap"><table>
        <thead><tr><th>الطائر</th><th>البائع</th><th>السلالة</th><th>السعر</th><th>التوفر</th><th>المراجعة</th><th>إجراءات</th></tr></thead>
        <tbody>@forelse($birds as $bird)
            <tr>
                <td><div class="table-bird"><img src="{{ $bird->primary_image }}" alt=""><div><b>{{ $bird->title }}</b><small>{{ $bird->city }}@if($bird->region)، {{ $bird->region->name }}@endif</small></div></div></td>
                <td>{{ $bird->seller?->sellerProfile?->display_name ?: $bird->seller?->name }}</td>
                <td>{{ $bird->breed->name }}</td>
                <td>{{ number_format($bird->price) }} ر.س</td>
                <td><span class="status-pill status-{{ $bird->status }}">{{ ['available'=>'متاح','reserved'=>'محجوز','sold'=>'مباع'][$bird->status] }}</span></td>
                <td><span class="order-status status-{{ $bird->approval_status === 'approved' ? 'delivered' : ($bird->approval_status === 'pending' ? 'pending' : 'cancelled') }}">{{ ['approved'=>'منشور','pending'=>'قيد المراجعة','rejected'=>'مرفوض'][$bird->approval_status] }}</span></td>
                <td><div class="table-actions"><a class="icon-btn" href="{{ route('admin.birds.edit',$bird) }}" aria-label="تعديل"><i data-lucide="pencil"></i></a>@if($bird->approval_status !== 'approved')<form method="post" action="{{ route('admin.birds.approval',$bird) }}">@csrf @method('patch')<input type="hidden" name="approval_status" value="approved"><button class="icon-btn" title="موافقة"><i data-lucide="check"></i></button></form>@endif @if($bird->approval_status !== 'rejected')<form method="post" action="{{ route('admin.birds.approval',$bird) }}">@csrf @method('patch')<input type="hidden" name="approval_status" value="rejected"><button class="icon-btn danger" title="رفض"><i data-lucide="x"></i></button></form>@endif<form method="post" action="{{ route('admin.birds.destroy',$bird) }}" onsubmit="return confirm('حذف هذا الإعلان؟')">@csrf @method('delete')<button class="icon-btn danger" aria-label="حذف"><i data-lucide="trash-2"></i></button></form></div></td>
            </tr>
        @empty
            <tr><td colspan="7" class="empty-cell">لا توجد إعلانات.</td></tr>
        @endforelse</tbody>
    </table></div>
    {{ $birds->links() }}
</section>
@endsection
