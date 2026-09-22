<?php

namespace App\Http\Controllers;

use App\Models\Bird;
use App\Models\Breed;
use Illuminate\Http\Request;

class BirdController extends Controller
{
    public function index(Request $request)
    {
        $regionId = $request->filled('region') ? $request->integer('region') : session('marketplace_region_id');

        $birds = Bird::query()
            ->with(['breed', 'media', 'region'])
            ->published()
            ->when($request->filled('q'), function ($query) use ($request) {
                $term = '%'.$request->string('q')->trim().'%';
                $query->where(fn ($nested) => $nested
                    ->where('title', 'like', $term)
                    ->orWhere('color', 'like', $term)
                    ->orWhere('city', 'like', $term)
                    ->orWhereHas('breed', fn ($breed) => $breed->where('name', 'like', $term)));
            })
            ->when($request->filled('breed'), fn ($query) => $query->whereHas('breed', fn ($breed) => $breed->where('slug', $request->string('breed'))))
            ->when($request->filled('sex'), fn ($query) => $query->where('sex', $request->string('sex')))
            ->when($regionId && ! $request->boolean('all_regions'), fn ($query) => $query->where('region_id', $regionId))
            ->when($request->filled('color'), fn ($query) => $query->where('color', 'like', '%'.$request->string('color')->trim().'%'))
            ->when($request->filled('molt_status'), fn ($query) => $query->where('molt_status', $request->string('molt_status')))
            ->when($request->filled('singing_status'), fn ($query) => $query->where('singing_status', $request->string('singing_status')))
            ->when($request->filled('breeding_ready'), fn ($query) => $query->where('breeding_ready', $request->boolean('breeding_ready')))
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
            'regions' => \App\Models\Region::where('active', true)->orderBy('sort_order')->orderBy('name')->get(),
            'activeRegionId' => $request->boolean('all_regions') ? null : $regionId,
        ]);
    }

    public function show(Bird $bird)
    {
        abort_unless($bird->approval_status === 'approved' && $bird->seller?->status === 'active', 404);
        $bird->load(['breed', 'media', 'seller.sellerProfile.region', 'region']);

        return view('birds.show', [
            'bird' => $bird,
            'navigationRegions' => \App\Models\Region::where('active', true)->orderBy('sort_order')->orderBy('name')->get(),
            'selectedRegion' => session('marketplace_region_id') ? \App\Models\Region::find(session('marketplace_region_id')) : null,
            'relatedBirds' => Bird::with(['breed', 'media', 'region'])
                ->published()
                ->where('status', 'available')
                ->where('id', '!=', $bird->id)
                ->where('breed_id', $bird->breed_id)
                ->take(3)
                ->get(),
        ]);
    }
}
