<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Bird;
use App\Models\Order;

class DashboardController extends Controller
{
    public function __invoke()
    {
        return view('admin.dashboard', [
            'counts' => [
                'available' => Bird::where('status', 'available')->count(),
                'reserved' => Bird::where('status', 'reserved')->count(),
                'sold' => Bird::where('status', 'sold')->count(),
                'pending' => Order::where('status', 'pending')->count(),
            ],
            'latestOrders' => Order::with('bird')->latest()->take(7)->get(),
        ]);
    }
}
