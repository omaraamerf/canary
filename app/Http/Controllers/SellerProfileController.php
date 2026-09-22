<?php

namespace App\Http\Controllers;

use App\Models\User;

class SellerProfileController extends Controller
{
    public function show(User $seller)
    {
        abort_unless($seller->role === 'seller' && $seller->status === 'active', 404);
        $seller->load(['sellerProfile.region']);

        return view('sellers.show', [
            'seller' => $seller,
            'birds' => $seller->birds()->published()->where('status', 'available')->with(['breed', 'media', 'region'])->latest()->paginate(12),
        ]);
    }
}
