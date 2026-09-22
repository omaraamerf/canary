<?php

namespace App\Http\Controllers;

use App\Models\Bird;
use App\Models\Breed;

class HomeController extends Controller
{
    public function __invoke()
    {
        $regionId = session('marketplace_region_id');

        return view('home', [
            'featuredBirds' => Bird::with(['breed', 'media', 'region'])
                ->published()
                ->where('status', 'available')
                ->when($regionId, fn ($query) => $query->where('region_id', $regionId))
                ->latest('featured')
                ->latest()
                ->take(6)
                ->get(),
            'breeds' => Breed::where('active', true)
                ->withCount(['birds' => fn ($query) => $query->published()->where('status', 'available')->when($regionId, fn ($birds) => $birds->where('region_id', $regionId))])
                ->orderByDesc('birds_count')
                ->take(4)
                ->get(),
        ]);
    }
}
