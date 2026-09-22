<?php

namespace App\Http\Controllers\Admin;

use App\Enums\UserRole;
use App\Enums\UserStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateSellerStatusRequest;
use App\Models\User;
use App\Services\SellerService;
use Illuminate\Http\Request;

class SellerController extends Controller
{
    public function __construct(private readonly SellerService $sellers) {}

    public function index(Request $request)
    {
        return view('admin.sellers.index', [
            'sellers' => User::with(['sellerProfile.region'])->where('role', UserRole::Seller->value)
                ->when($request->filled('status'), fn ($query) => $query->where('status', $request->string('status')))
                ->latest()->paginate(20)->withQueryString(),
        ]);
    }

    public function updateStatus(UpdateSellerStatusRequest $request, User $seller)
    {
        abort_unless($seller->role === UserRole::Seller->value, 404);
        $data = $request->validated();
        $this->sellers->updateStatus(
            $seller,
            UserStatus::from($data['status']),
            $data['rejection_reason'] ?? null,
        );

        return back()->with('success', 'تم تحديث حالة البائع.');
    }
}
