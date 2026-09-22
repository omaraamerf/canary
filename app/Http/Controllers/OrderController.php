<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreOrderRequest;
use App\Models\Bird;
use App\Models\Order;
use App\Services\OrderService;

class OrderController extends Controller
{
    public function __construct(private readonly OrderService $orders) {}

    public function store(StoreOrderRequest $request, Bird $bird)
    {
        $order = $this->orders->create($bird, $request->validated());

        return redirect()->route('orders.received', $order);
    }

    public function received(Order $order)
    {
        return view('orders.received', compact('order'));
    }
}
