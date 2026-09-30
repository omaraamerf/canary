<?php

namespace App\Http\Controllers;

use App\Enums\SettingKey;
use App\Http\Requests\UpdateAccountRequest;
use App\Http\Requests\UpdatePasswordRequest;
use App\Models\Comment;
use App\Models\Post;
use App\Models\Setting;
use App\Models\User;
use App\Services\AccountService;
use Illuminate\Http\Request;

class AccountController extends Controller
{
    public function __construct(private readonly AccountService $accounts) {}

    public function show(Request $request)
    {
        $user = $request->user()->load(['country', 'region', 'sellerProfile']);

        return view('account.show', [
            'user' => $user,
            'communityEnabled' => Setting::boolean(SettingKey::CommunityEnabled->value, true),
            'stats' => self::stats($user),
            'posts' => $user->posts()->with(['media', 'breed', 'user.sellerProfile', 'country', 'region'])->withCount('visibleComments')->latest()->paginate(10, pageName: 'posts'),
            'comments' => $user->comments()
                ->whereHasMorph('commentable', [Post::class])
                ->with('commentable')
                ->latest()
                ->paginate(10, pageName: 'replies'),
        ]);
    }

    public function edit(Request $request)
    {
        return view('account.edit', ['user' => $request->user()]);
    }

    public function update(UpdateAccountRequest $request)
    {
        $this->accounts->updateProfile($request->user(), $request->validated());

        return redirect()->route('account.show')->with('success', __('ui.account.saved'));
    }

    public function updatePassword(UpdatePasswordRequest $request)
    {
        $this->accounts->updatePassword($request->user(), $request->validated('password'));

        return redirect()->route('account.edit')->with('password_success', __('ui.account.password_saved'));
    }

    /**
     * @return array{posts: int, replies: int, solutions: int}
     */
    public static function stats(User $user): array
    {
        return [
            'posts' => $user->posts()->published()->count(),
            'replies' => $user->comments()->where('is_hidden', false)->count(),
            'solutions' => Post::query()->whereIn('accepted_comment_id', Comment::query()->select('id')->where('user_id', $user->id))->count(),
        ];
    }
}
