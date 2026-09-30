<?php

namespace App\Http\Middleware;

use App\Enums\SettingKey;
use App\Models\Setting;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureCommunityEnabled
{
    public function handle(Request $request, Closure $next): Response
    {
        abort_unless(Setting::boolean(SettingKey::CommunityEnabled->value, true), 404);

        return $next($request);
    }
}
