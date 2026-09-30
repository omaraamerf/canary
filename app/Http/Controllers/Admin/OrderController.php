<?php

namespace App\Http\Controllers\Admin;

use App\Enums\OrderStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateOrderStatusRequest;
use App\Models\Order;
use App\Services\OrderService;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function __construct(private readonly OrderService $orders) {}

    public function index(Request $request)
    {
        return view('admin.orders.index', [
            'orders' => Order::with('bird')
                ->when($request->filled('status'), fn ($query) => $query->where('status', $request->string('status')))
                ->latest()
                ->paginate(20)
                ->withQueryString(),
        ]);
    }

    public function show(Order $order)
    {
        return view('admin.orders.show', ['order' => $order->load(['bird.media', 'statusLogs'])]);
    }

    public function updateStatus(UpdateOrderStatusRequest $request, Order $order)
    {
        $data = $request->validated();
        $this->orders->updateStatus(
            $order,
            OrderStatus::from($data['status']),
            $data['note'] ?? null,
            $request->user(),
        );

        return back()->with('success', __('تم تحديث حالة الطلب.'));
    }
}
