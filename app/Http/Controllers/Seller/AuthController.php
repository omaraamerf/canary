<?php

namespace App\Http\Controllers\Seller;

use App\Enums\UserRole;
use App\Enums\UserStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\Seller\RegisterRequest;
use App\Models\Region;
use App\Models\User;
use App\Services\SellerService;
use Filament\Facades\Filament;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function __construct(private readonly SellerService $sellers) {}

    public function create()
    {
        return view('seller.auth.login');
    }

    public function store(LoginRequest $request)
    {
        $credentials = $request->safe()->only(['email', 'password']);

        if (! Auth::attempt([...$credentials, 'role' => UserRole::Seller->value, 'status' => UserStatus::Active->value], $request->boolean('remember'))) {
            $seller = User::query()->where('email', $credentials['email'])->where('role', UserRole::Seller->value)->first();
            $message = $seller?->status === UserStatus::Pending->value
                ? __('حسابك ما زال بانتظار موافقة الإدارة.')
                : __('بيانات الدخول غير صحيحة أو الحساب غير نشط.');

            return back()->withErrors(['email' => $message])->onlyInput('email');
        }

        $request->session()->regenerate();

        return redirect()->intended(route('seller.dashboard'));
    }

    public function register()
    {
        return view('seller.auth.register', [
            'regions' => Region::where('active', true)->orderBy('sort_order')->orderBy('name')->get(),
        ]);
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

    public function destroy(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home');
    }
}
