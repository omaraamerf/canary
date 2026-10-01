<?php

namespace App\Http\Controllers\Seller;

use App\Enums\UserStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Seller\RegisterRequest;
use App\Services\SellerService;
use Filament\Facades\Filament;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function __construct(private readonly SellerService $sellers) {}

    public function register()
    {
        return view('seller.auth.register');
    }

    public function storeRegistration(RegisterRequest $request)
    {
        $user = $this->sellers->register($request->validated());

        if ($user->status === UserStatus::Active->value) {
            Auth::login($user);
            $request->session()->regenerate();

            return redirect()->to(Filament::getPanel('seller')->getUrl())->with('success', __('تم إنشاء حساب البائع.'));
        }

        return redirect()->to(Filament::getPanel('seller')->getLoginUrl())->with('success', __('تم استلام طلب حسابك وسيظهر لك الدخول بعد موافقة الإدارة.'));
    }
}
