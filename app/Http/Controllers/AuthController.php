<?php

namespace App\Http\Controllers;

use App\Enums\UserStatus;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterMemberRequest;
use App\Models\Region;
use App\Services\MemberService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function __construct(private readonly MemberService $members) {}

    public function login()
    {
        return view('auth.login');
    }

    public function authenticate(LoginRequest $request)
    {
        $credentials = $request->safe()->only(['email', 'password']);

        if (! Auth::attempt([...$credentials, 'status' => UserStatus::Active->value], $request->boolean('remember'))) {
            return back()->withErrors(['email' => __('بيانات الدخول غير صحيحة أو الحساب غير نشط.')])->onlyInput('email');
        }

        $request->session()->regenerate();

        return redirect()->intended(route('community.index'));
    }

    public function register()
    {
        return view('auth.register', [
            'regions' => Region::where('active', true)->orderBy('sort_order')->orderBy('name')->get(),
        ]);
    }

    public function storeRegistration(RegisterMemberRequest $request)
    {
        Auth::login($this->members->register($request->validated()));
        $request->session()->regenerate();

        return redirect()->intended(route('community.index'))->with('success', __('ui.auth.registered'));
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home');
    }
}
