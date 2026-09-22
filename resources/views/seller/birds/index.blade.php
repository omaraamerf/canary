@extends('layouts.seller')
@section('title','إعلاناتي')
@section('heading','إعلانات الطيور')
@section('eyebrow','إدارة المخزون')
@section('content')
<section class="admin-section">
    <div class="admin-section-head">
        <div><h2>إعلاناتي</h2><p>الإعلانات المعلقة لا تظهر للزوار حتى تعتمدها الإدارة.</p></div>
        <a class="btn btn-primary" href="{{ route('seller.birds.create') }}"><i data-lucide="plus"></i>إضافة طائر</a>
    </div>
    <div class="table-wrap"><table>
        <thead><tr><th>الطائر</th><th>السلالة</th><th>السعر</th><th>التوفر</th><th>المراجعة</th><th>إجراءات</th></tr></thead>
        <tbody>@forelse($birds as $bird)
            <tr>
                <td><div class="table-bird"><img src="{{ $bird->primary_image }}" alt=""><div><b>{{ $bird->title }}</b><small>{{ $bird->city }}@if($bird->region)، {{ $bird->region->name }}@endif</small></div></div></td>
                <td>{{ $bird->breed->name }}</td>
                <td>{{ number_format($bird->price) }} ر.س</td>
                <td><span class="status-pill status-{{ $bird->status }}">{{ ['available'=>'متاح','reserved'=>'محجوز','sold'=>'مباع'][$bird->status] }}</span></td>
                <td><span class="order-status status-{{ $bird->approval_status === 'approved' ? 'delivered' : ($bird->approval_status === 'pending' ? 'pending' : 'cancelled') }}">{{ ['approved'=>'منشور','pending'=>'قيد المراجعة','rejected'=>'مرفوض'][$bird->approval_status] }}</span>@if($bird->rejection_reason)<small class="d-block text-danger mt-1">{{ $bird->rejection_reason }}</small>@endif</td>
                <td><div class="table-actions"><a class="icon-btn" href="{{ route('seller.birds.edit',$bird) }}"><i data-lucide="pencil"></i></a><form method="post" action="{{ route('seller.birds.destroy',$bird) }}" onsubmit="return confirm('حذف الإعلان؟')">@csrf @method('delete')<button class="icon-btn danger"><i data-lucide="trash-2"></i></button></form></div></td>
            </tr>
        @empty
            <tr><td colspan="6" class="empty-cell">لم تضف أي طائر بعد.</td></tr>
        @endforelse</tbody>
    </table></div>
    {{ $birds->links() }}
</section>
@endsection
