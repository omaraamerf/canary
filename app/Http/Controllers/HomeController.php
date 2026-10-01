<?php

namespace App\Http\Controllers;

use App\Enums\ApprovalStatus;
use App\Models\Article;
use App\Models\Bird;
use App\Models\Breed;
use App\Models\Region;
use App\Models\Setting;
use App\Models\User;
use App\Support\MarketplaceLocation;
use Illuminate\Database\Eloquent\Builder;

class HomeController extends Controller
{
    public function __invoke(MarketplaceLocation $location)
    {
        $countryId = $location->countryId();
        $regionId = $location->regionId();
        $available = fn (Builder $query): Builder => MarketplaceLocation::scope($query, $countryId, $regionId)
            ->published()
            ->where('status', 'available');

        return view('home', [
            'featuredBirds' => $available(Bird::with(['breed', 'media', 'region', 'seller.sellerProfile']))
                ->latest('featured')
                ->latest()
                ->take(8)
                ->get(),
            'breeds' => Breed::where('active', true)
                ->whereHas('birds', $available)
                ->withCount(['birds' => $available])
                // One recent bird per breed supplies the breed tile's photo.
                ->with(['birds' => fn ($birds) => tap($birds, fn ($relation) => $available($relation->getQuery()))->with('media')->latest()->limit(1)])
                ->orderByDesc('birds_count')
                ->take(6)
                ->get(),
            'regions' => $regionId ? collect() : Region::with('country')
                ->whereHas('birds', $available)
                ->withCount(['birds' => $available])
                ->orderByDesc('birds_count')
                ->take(8)
                ->get(),
            'stats' => [
                'birds' => $available(Bird::query())->count(),
                'sellers' => User::whereHas('birds', $available)
                    ->whereHas('sellerProfile', fn (Builder $profile) => $profile->where('approval_status', ApprovalStatus::Approved->value))
                    ->count(),
                'regions' => Region::whereHas('birds', $available)->count(),
            ],
            'articles' => Setting::boolean('guide_enabled', true)
                ? Article::with('category')->published()->latest('published_at')->take(3)->get()
                : collect(),
        ]);
    }
}
