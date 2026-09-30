<?php

namespace App\Http\Controllers\Seller;

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
        return view('seller.orders.index', [
            'orders' => Order::with('bird')
                ->whereHas('bird', fn ($bird) => $bird->where('seller_id', $request->user()->id))
                ->when($request->filled('status'), fn ($query) => $query->where('status', $request->string('status')))
                ->latest()->paginate(20)->withQueryString(),
        ]);
    }

    public function show(Request $request, Order $order)
    {
        $this->owns($request, $order);

        return view('seller.orders.show', ['order' => $order->load(['bird.media', 'statusLogs'])]);
    }

    public function updateStatus(UpdateOrderStatusRequest $request, Order $order)
    {
        $this->owns($request, $order);
        $data = $request->validated();
        $this->orders->updateStatus(
            $order,
            OrderStatus::from($data['status']),
            $data['note'] ?? null,
            $request->user(),
            $request->user()->id,
        );

        return back()->with('success', __('تم تحديث حالة الطلب.'));
    }

    private function owns(Request $request, Order $order): void
    {
        abort_unless($order->bird()->where('seller_id', $request->user()->id)->exists(), 403);
    }
}
