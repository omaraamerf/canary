<?php

namespace App\Http\Controllers;

use App\Enums\UserStatus;
use App\Models\User;

class MemberController extends Controller
{
    public function show(User $user)
    {
        abort_unless($user->status === UserStatus::Active->value, 404);

        $user->load(['country', 'region', 'sellerProfile']);

        return view('members.show', [
            'member' => $user,
            'stats' => AccountController::stats($user),
            'posts' => $user->posts()->published()->with(['media', 'user.sellerProfile', 'breed', 'country', 'region'])->withCount('visibleComments')->latest()->paginate(10),
        ]);
    }
}
