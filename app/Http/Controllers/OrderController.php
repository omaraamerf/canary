<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreOrderRequest;
use App\Http\Requests\TrackOrderRequest;
use App\Models\Bird;
use App\Models\Order;
use App\Services\OrderService;
use App\Services\OrderTrackingService;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function __construct(
        private readonly OrderService $orders,
        private readonly OrderTrackingService $tracking,
    ) {}

    public function store(StoreOrderRequest $request, Bird $bird)
    {
        $order = $this->orders->create($bird, [...$request->validated(), 'user_id' => $request->user()?->id]);
        $this->tracking->authorizeSession($request->session(), $order);

        return redirect()->route('orders.received', $order);
    }

    public function received(Request $request, Order $order)
    {
        if (! $this->tracking->canTrack($request->session(), $request->user(), $order)) {
            return redirect()->route('orders.track', ['reference' => $order->reference]);
        }

        $order->load('bird');

        return view('orders.received', compact('order'));
    }

    public function track(Request $request)
    {
        return view('orders.track', [
            'reference' => $request->string('reference')->toString(),
        ]);
    }

    public function lookup(TrackOrderRequest $request)
    {
        $order = $this->tracking->findForCustomer(
            $request->validated('reference'),
            $request->validated('phone'),
        );

        $this->tracking->authorizeSession($request->session(), $order);

        return redirect()->route('orders.track.show', $order);
    }

    public function showTracking(Request $request, Order $order)
    {
        abort_unless($this->tracking->canTrack($request->session(), $request->user(), $order), 403);

        $order->load(['bird', 'buyerRegion']);

        return view('orders.tracking', [
            'order' => $order,
            'statusLogs' => $order->statusLogs()->oldest()->get(),
        ]);
    }
}
