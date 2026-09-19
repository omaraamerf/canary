<?php

namespace App\Http\Controllers;

use App\Models\Bird;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class OrderController extends Controller
{
    public function store(Request $request, Bird $bird)
    {
        $validated = $request->validate([
            'buyer_name' => ['required', 'string', 'max:100'],
            'phone' => ['required', 'string', 'max:30'],
            'city' => ['required', 'string', 'max:100'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $order = DB::transaction(function () use ($bird, $validated) {
            $lockedBird = Bird::query()->lockForUpdate()->findOrFail($bird->id);

            abort_if($lockedBird->status !== 'available', 422, 'هذا الطائر لم يعد متاحًا للحجز.');

            do {
                $reference = 'CNY-'.Str::upper(Str::random(7));
            } while (Order::where('reference', $reference)->exists());

            $order = $lockedBird->orders()->create([
                ...$validated,
                'reference' => $reference,
                'status' => 'pending',
                'delivery_method' => $lockedBird->delivery_type,
                'price_snapshot' => $lockedBird->price,
                'currency_snapshot' => $lockedBird->currency,
            ]);

            $order->statusLogs()->create([
                'new_status' => 'pending',
                'note' => 'تم استلام طلب الحجز من الموقع.',
            ]);

            return $order;
        });

        return redirect()->route('orders.received', $order);
    }

    public function received(Order $order)
    {
        return view('orders.received', compact('order'));
    }
}
