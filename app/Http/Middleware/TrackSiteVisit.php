<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class TrackSiteVisit
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        if (! $request->isMethod('GET') || ! $response->isSuccessful() || ! $this->isPublicPage($request)) {
            return $response;
        }

        $today = now()->toDateString();
        $isUniqueVisitor = $request->session()->get('site_visit_date') !== $today;
        $now = now();

        DB::table('site_visits')->insertOrIgnore([
            'visited_on' => $today,
            'page_views' => 0,
            'unique_visitors' => 0,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        DB::table('site_visits')
            ->where('visited_on', $today)
            ->incrementEach([
                'page_views' => 1,
                'unique_visitors' => $isUniqueVisitor ? 1 : 0,
            ], ['updated_at' => $now]);

        if ($isUniqueVisitor) {
            $request->session()->put('site_visit_date', $today);
        }

        return $response;
    }

    private function isPublicPage(Request $request): bool
    {
        return $request->routeIs(
            'home',
            'birds.*',
            'guide.*',
            'community.index',
            'community.show',
            'about',
            'policy',
            'start-selling',
            'sellers.show',
            'orders.received',
            'orders.track',
            'orders.track.show',
        );
    }
}
