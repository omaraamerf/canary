<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Bird;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class OrderController extends Controller
{
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

    public function updateStatus(Request $request, Order $order)
    {
        $validated = $request->validate([
            'status' => ['required', 'in:pending,confirmed,preparing,out_for_delivery,delivered,cancelled'],
            'note' => ['nullable', 'string', 'max:1000'],
        ]);

        DB::transaction(function () use ($request, $order, $validated) {
            $lockedOrder = Order::query()->lockForUpdate()->findOrFail($order->id);
            $bird = Bird::query()->lockForUpdate()->findOrFail($lockedOrder->bird_id);
            $oldStatus = $lockedOrder->status;
            $newStatus = $validated['status'];

            if ($newStatus === 'confirmed' && $bird->status !== 'available' && $oldStatus !== 'confirmed') {
                throw ValidationException::withMessages(['status' => 'الطائر محجوز أو مباع بالفعل.']);
            }

            if ($newStatus === 'confirmed') {
                $bird->update(['status' => 'reserved']);
                Order::where('bird_id', $bird->id)
                    ->where('id', '!=', $lockedOrder->id)
                    ->where('status', 'pending')
                    ->update(['status' => 'cancelled']);
            } elseif ($newStatus === 'delivered') {
                $bird->update(['status' => 'sold']);
            } elseif ($newStatus === 'cancelled' && in_array($oldStatus, ['confirmed', 'preparing', 'out_for_delivery'], true)) {
                $bird->update(['status' => 'available']);
            }

            $lockedOrder->update(['status' => $newStatus]);
            $lockedOrder->statusLogs()->create([
                'old_status' => $oldStatus,
                'new_status' => $newStatus,
                'note' => $validated['note'] ?? null,
                'changed_by' => $request->user()->id,
            ]);
        });

        return back()->with('success', 'تم تحديث حالة الطلب.');
    }
}
