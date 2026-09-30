<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Http\Requests\Seller\UpdateProfileRequest;
use App\Models\Region;
use App\Services\SellerService;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    public function __construct(private readonly SellerService $sellers) {}

    public function edit(Request $request)
    {
        return view('seller.profile.edit', [
            'seller' => $request->user()->load('sellerProfile'),
            'regions' => Region::where('active', true)->orderBy('sort_order')->orderBy('name')->get(),
        ]);
    }

    public function update(UpdateProfileRequest $request)
    {
        $this->sellers->updateProfile($request->user(), $request->validated());

        return back()->with('success', __('تم تحديث ملف البائع.'));
    }
}
