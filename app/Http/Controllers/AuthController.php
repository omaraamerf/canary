<?php

namespace App\Http\Controllers;

use App\Enums\SettingKey;
use App\Enums\UserStatus;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterMemberRequest;
use App\Models\Setting;
use App\Models\User;
use App\Services\MemberService;
use Filament\Facades\Filament;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    /** Pages a member is never sent back to after signing in. */
    private const NO_RETURN = ['login', 'register', 'seller/register'];

    public function __construct(private readonly MemberService $members) {}

    public function login(Request $request)
    {
        $this->rememberReturnPage($request);

        return view('auth.login');
    }

    /**
     * The one sign-in for everyone: members, sellers and admins. A protected page the user
     * was sent here from wins; otherwise staff land in their panel and members go back
     * to the page they came from.
     */
    public function authenticate(LoginRequest $request)
    {
        $credentials = $request->safe()->only(['email', 'password']);

        if (! Auth::attempt([...$credentials, 'status' => UserStatus::Active->value], $request->boolean('remember'))) {
            return back()->withErrors(['email' => $this->failureMessage($credentials)])->onlyInput('email');
        }

        $request->session()->regenerate();

        return redirect()->intended($this->homeFor($request));
    }

    public function register(Request $request)
    {
        $this->rememberReturnPage($request);

        return view('auth.register');
    }

    public function storeRegistration(RegisterMemberRequest $request)
    {
        Auth::login($this->members->register($request->validated()));
        $request->session()->regenerate();

        return redirect()->intended($this->homeFor($request))->with('success', __('ui.auth.registered'));
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home');
    }

    /**
     * A correct password on an account that cannot sign in yet gets a reason; anything
     * else gets the same message, so the form does not reveal which emails exist.
     *
     * @param  array{email: string, password: string}  $credentials
     */
    private function failureMessage(array $credentials): string
    {
        if (! Auth::validate($credentials)) {
            return __('ui.auth.failed');
        }

        $status = User::query()->where('email', $credentials['email'])->value('status');

        return __($status === UserStatus::Pending->value ? 'ui.auth.pending' : 'ui.auth.inactive');
    }

    private function homeFor(Request $request): string
    {
        $user = $request->user();
        $returnTo = $request->session()->pull('login.return_to');
        $intended = $request->session()->get('url.intended');

        // A member sent here from a panel they cannot open would only meet a 403.
        if ($intended && ! $this->mayOpen($user, $intended)) {
            $request->session()->forget('url.intended');
        }

        return $user->panelUrl()
            ?? $returnTo
            ?? (Setting::boolean(SettingKey::CommunityEnabled->value, true) ? route('community.index') : route('home'));
    }

    private function mayOpen(User $user, string $url): bool
    {
        $path = '/'.trim((string) parse_url($url, PHP_URL_PATH), '/').'/';

        foreach (Filament::getPanels() as $panel) {
            if (str_starts_with($path, '/'.$panel->getPath().'/') && ! $user->canAccessPanel($panel)) {
                return false;
            }
        }

        return true;
    }

    /** Remembers the page the visitor came from on this site, as a path, to return there. */
    private function rememberReturnPage(Request $request): void
    {
        $previous = $request->headers->get('referer');

        if (! $previous || parse_url($previous, PHP_URL_HOST) !== $request->getHost()) {
            return;
        }

        $path = trim((string) parse_url($previous, PHP_URL_PATH), '/');

        if (in_array($path, self::NO_RETURN, true)) {
            return;
        }

        $query = parse_url($previous, PHP_URL_QUERY);
        $request->session()->put('login.return_to', '/'.$path.($query ? '?'.$query : ''));
    }
}
