<?php

namespace App\Http\Controllers;

use App\Models\Bird;
use App\Models\Breed;
use Illuminate\Http\Request;

class BirdController extends Controller
{
    public function index(Request $request)
    {
        $birds = Bird::query()
            ->with(['breed', 'media'])
            ->when($request->filled('q'), function ($query) use ($request) {
                $term = '%'.$request->string('q')->trim().'%';
                $query->where(fn ($nested) => $nested
                    ->where('title', 'like', $term)
                    ->orWhere('color', 'like', $term)
                    ->orWhereHas('breed', fn ($breed) => $breed->where('name', 'like', $term)));
            })
            ->when($request->filled('breed'), fn ($query) => $query->whereHas('breed', fn ($breed) => $breed->where('slug', $request->string('breed'))))
            ->when($request->filled('sex'), fn ($query) => $query->where('sex', $request->string('sex')))
            ->when($request->filled('city'), fn ($query) => $query->where('city', $request->string('city')))
            ->when($request->filled('min_price'), fn ($query) => $query->where('price', '>=', $request->integer('min_price')))
            ->when($request->filled('max_price'), fn ($query) => $query->where('price', '<=', $request->integer('max_price')))
            ->when($request->boolean('delivery'), fn ($query) => $query->whereIn('delivery_type', ['delivery', 'agreement']))
            ->where('status', 'available');

        match ($request->string('sort')->toString()) {
            'price_asc' => $birds->orderBy('price'),
            'price_desc' => $birds->orderByDesc('price'),
            default => $birds->latest(),
        };

        return view('birds.index', [
            'birds' => $birds->paginate(9)->withQueryString(),
            'breeds' => Breed::where('active', true)->orderBy('name')->get(),
            'cities' => Bird::where('status', 'available')->distinct()->orderBy('city')->pluck('city'),
        ]);
    }

    public function show(Bird $bird)
    {
        $bird->load(['breed', 'media']);

        return view('birds.show', [
            'bird' => $bird,
            'relatedBirds' => Bird::with(['breed', 'media'])
                ->where('status', 'available')
                ->where('id', '!=', $bird->id)
                ->where('breed_id', $bird->breed_id)
                ->take(3)
                ->get(),
        ]);
    }
}
