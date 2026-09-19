<?php

namespace App\Http\Controllers;

use App\Models\Bird;
use App\Models\Breed;

class HomeController extends Controller
{
    public function __invoke()
    {
        return view('home', [
            'featuredBirds' => Bird::with(['breed', 'media'])
                ->where('status', 'available')
                ->latest('featured')
                ->latest()
                ->take(6)
                ->get(),
            'breeds' => Breed::where('active', true)
                ->withCount(['birds' => fn ($query) => $query->where('status', 'available')])
                ->orderByDesc('birds_count')
                ->take(4)
                ->get(),
        ]);
    }
}
