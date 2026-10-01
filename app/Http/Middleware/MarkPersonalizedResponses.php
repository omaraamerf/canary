<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Tells the service worker (public/sw.js) not to save a page for offline use when it was
 * rendered for someone: a signed-in visitor, or a one-off flash message or form error.
 */
class MarkPersonalizedResponses
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        if ($request->user() || ($request->hasSession() && filled($request->session()->get('_flash.old')))) {
            $response->headers->set('X-Personalized', '1');
        }

        return $response;
    }
}
