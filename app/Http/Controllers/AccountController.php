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

    /** The personal dashboard: one tab at a time (questions, replies, orders); settings live on edit. */
    public function show(Request $request)
    {
        $user = $request->user()->load(['country', 'region', 'sellerProfile']);
        $tabs = self::tabs($user);
        $requested = $request->query('tab');
        $tab = is_string($requested) && array_key_exists($requested, $tabs) ? $requested : array_key_first($tabs);

        return view('account.show', [
            'user' => $user,
            'stats' => self::stats($user),
            'tabs' => $tabs,
            'tab' => $tab,
            'items' => match ($tab) {
                'posts' => $user->posts()->with(['media', 'breed', 'user.sellerProfile', 'country', 'region'])->withCount('visibleComments')->latest()->paginate(10)->withQueryString(),
                'replies' => $user->comments()->whereHasMorph('commentable', [Post::class])->with('commentable')->latest()->paginate(10)->withQueryString(),
                'orders' => $user->orders()->with('bird.media')->latest()->paginate(10)->withQueryString(),
            },
        ]);
    }

    public function edit(Request $request)
    {
        return view('account.edit', ['user' => $request->user(), 'tabs' => self::tabs($request->user())]);
    }

    /**
     * Tab => count. Questions and replies only while the community is on.
     *
     * @return array<string, int>
     */
    private static function tabs(User $user): array
    {
        $community = Setting::boolean(SettingKey::CommunityEnabled->value, true);

        return array_filter([
            'posts' => $community ? $user->posts()->count() : null,
            'replies' => $community ? $user->comments()->whereHasMorph('commentable', [Post::class])->count() : null,
            'orders' => $user->orders()->count(),
        ], fn (?int $count): bool => $count !== null);
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
