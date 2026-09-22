<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function __invoke(Request $request)
    {
        $user = $request->user();
        $birdIds = $user->birds()->pluck('id');

        return view('seller.dashboard', [
            'counts' => [
                'birds' => $birdIds->count(),
                'pending_listings' => $user->birds()->where('approval_status', 'pending')->count(),
                'available' => $user->birds()->where('status', 'available')->count(),
                'orders' => Order::whereIn('bird_id', $birdIds)->where('status', 'pending')->count(),
            ],
            'latestOrders' => Order::with('bird')->whereIn('bird_id', $birdIds)->latest()->take(8)->get(),
        ]);
    }
}
