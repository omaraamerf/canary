<?php

namespace App\Http\Controllers\Admin;

use App\Enums\UserRole;
use App\Enums\UserStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function create()
    {
        return view('admin.auth.login');
    }

    public function store(LoginRequest $request)
    {
        $credentials = $request->safe()->only(['email', 'password']);

        if (! Auth::attempt([...$credentials, 'status' => UserStatus::Active->value], $request->boolean('remember'))) {
            return back()->withErrors(['email' => __('بيانات الدخول غير صحيحة.')])->onlyInput('email');
        }

        $request->session()->regenerate();

        if ($request->user()->role !== UserRole::Admin->value) {
            Auth::logout();

            return back()->withErrors(['email' => __('هذا الحساب لا يملك صلاحية الإدارة.')]);
        }

        return redirect()->intended(route('admin.dashboard'));
    }

    public function destroy(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home');
    }
}
