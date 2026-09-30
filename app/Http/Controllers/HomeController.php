<?php

namespace App\Http\Controllers;

use App\Models\Bird;
use App\Models\Breed;
use App\Support\MarketplaceLocation;

class HomeController extends Controller
{
    public function __invoke(MarketplaceLocation $location)
    {
        $countryId = $location->countryId();
        $regionId = $location->regionId();

        return view('home', [
            'featuredBirds' => MarketplaceLocation::scope(Bird::with(['breed', 'media', 'region']), $countryId, $regionId)
                ->published()
                ->where('status', 'available')
                ->latest('featured')
                ->latest()
                ->take(6)
                ->get(),
            'breeds' => Breed::where('active', true)
                ->withCount(['birds' => fn ($query) => MarketplaceLocation::scope($query, $countryId, $regionId)->published()->where('status', 'available')])
                ->orderByDesc('birds_count')
                ->take(4)
                ->get(),
        ]);
    }
}
