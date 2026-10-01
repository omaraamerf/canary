<?php

namespace App\Http\Controllers;

use App\Enums\Currency;
use App\Models\Bird;
use App\Models\Breed;
use App\Models\Country;
use App\Models\Region;
use App\Support\MarketplaceLocation;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class BirdController extends Controller
{
    public function index(Request $request, MarketplaceLocation $location)
    {
        $filtersLocation = $request->hasAny(['country', 'region']);
        $regionId = $filtersLocation ? ($request->integer('region') ?: null) : $location->regionId();
        $countryId = $filtersLocation ? ($request->integer('country') ?: null) : $location->countryId();
        $allRegions = $request->boolean('all_regions');
        // Prices in different currencies cannot be compared, so a price range needs a currency.
        $currency = Currency::tryFrom($request->string('currency')->toString());

        $birds = MarketplaceLocation::scope(Bird::query(), $allRegions ? null : $countryId, $allRegions ? null : $regionId)
            ->with(['breed', 'media', 'region', 'seller.sellerProfile'])
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
            ->when($request->filled('color'), fn ($query) => $query->where('color', 'like', '%'.$request->string('color')->trim().'%'))
            ->when($request->filled('molt_status'), fn ($query) => $query->where('molt_status', $request->string('molt_status')))
            ->when($request->filled('singing_status'), fn ($query) => $query->where('singing_status', $request->string('singing_status')))
            ->when($request->filled('breeding_ready'), fn ($query) => $query->where('breeding_ready', $request->boolean('breeding_ready')))
            ->when($currency, fn ($query) => $query->where('currency', $currency->value))
            ->when($currency && $request->filled('min_price'), fn ($query) => $query->where('price', '>=', $request->integer('min_price')))
            ->when($currency && $request->filled('max_price'), fn ($query) => $query->where('price', '<=', $request->integer('max_price')))
            ->when($request->boolean('delivery'), fn ($query) => $query->whereIn('delivery_type', ['delivery', 'agreement']))
            ->where('status', 'available');

        match ($request->string('sort')->toString()) {
            // Without a currency filter, dollar prices are listed before pound prices.
            'price_asc' => $birds->orderByRaw('CASE WHEN currency = ? THEN 0 ELSE 1 END', [Currency::USD->value])->orderBy('price'),
            'price_desc' => $birds->orderByRaw('CASE WHEN currency = ? THEN 0 ELSE 1 END', [Currency::USD->value])->orderByDesc('price'),
            default => $birds->latest(),
        };

        $breeds = Breed::where('active', true)->orderBy('name')->get();

        return view('birds.index', [
            'birds' => $birds->paginate(12)->withQueryString(),
            'breeds' => $breeds,
            'currency' => $currency,
            'activeFilters' => $this->activeFilters($request, $breeds, $currency),
            'activeCountryId' => $allRegions ? null : $countryId,
            'activeRegionId' => $allRegions ? null : $regionId,
        ]);
    }

    public function show(Bird $bird)
    {
        abort_unless($bird->approval_status === 'approved' && $bird->seller?->status === 'active', 404);
        $bird->load(['breed', 'media', 'seller.sellerProfile.region.country', 'region']);
        $profile = $bird->seller->sellerProfile;

        return view('birds.show', [
            'bird' => $bird,
            'sellerBirdsCount' => $bird->seller->birds()->published()->where('status', 'available')->count(),
            'whatsappUrl' => $profile?->whatsappUrl(__('ui.detail.whatsapp_message', ['title' => $bird->title, 'url' => route('birds.show', $bird)])),
            // Same breed first, then other available birds.
            'relatedBirds' => Bird::with(['breed', 'media', 'region', 'seller.sellerProfile'])
                ->published()
                ->where('status', 'available')
                ->whereKeyNot($bird->id)
                ->orderByRaw('CASE WHEN breed_id = ? THEN 0 ELSE 1 END', [$bird->breed_id])
                ->latest()
                ->take(8)
                ->get(),
        ]);
    }

    /**
     * Chips for the filters in the URL, each with the link that removes it.
     *
     * @return Collection<int, array{label: string, url: string}>
     */
    private function activeFilters(Request $request, Collection $breeds, ?Currency $currency): Collection
    {
        $chip = fn (string $label, array $keys): array => ['label' => $label, 'url' => $request->fullUrlWithoutQuery([...$keys, 'page'])];
        $chips = collect();

        if ($request->filled('q')) {
            $chips->push($chip('«'.$request->string('q')->trim().'»', ['q']));
        }
        if ($breed = $breeds->firstWhere('slug', $request->string('breed')->toString())) {
            $chips->push($chip($breed->localized_name, ['breed']));
        }
        if (in_array($request->input('sex'), ['male', 'female', 'unknown'], true)) {
            $chips->push($chip(__('ui.sex.'.$request->input('sex')), ['sex']));
        }
        if ($request->filled('region') && $region = Region::find($request->integer('region'))) {
            $chips->push($chip($region->name, ['region']));
        } elseif ($request->filled('country') && $country = Country::find($request->integer('country'))) {
            $chips->push($chip($country->localized_name, ['country', 'region']));
        }
        if ($request->filled('color')) {
            $chips->push($chip(__('ui.birds.color').': '.$request->string('color')->trim(), ['color']));
        }
        if (in_array($request->input('molt_status'), ['ready', 'young', 'molting'], true)) {
            $chips->push($chip(__('ui.molt.'.$request->input('molt_status')), ['molt_status']));
        }
        if (in_array($request->input('singing_status'), ['singing', 'not_singing', 'young', 'female'], true)) {
            $chips->push($chip(__('ui.singing.'.$request->input('singing_status')), ['singing_status']));
        }
        if ($request->filled('breeding_ready')) {
            $chips->push($chip($request->boolean('breeding_ready') ? __('ui.card.breeding_ready') : __('ui.catalog.not_ready'), ['breeding_ready']));
        }
        if ($currency) {
            $range = collect([$request->input('min_price'), $request->input('max_price')])->filter(fn ($value) => filled($value))->map(fn ($value) => number_format((int) $value))->implode(' – ');
            $chips->push($chip(trim($range.' '.$currency->label()), ['currency', 'min_price', 'max_price']));
        }
        if ($request->boolean('delivery')) {
            $chips->push($chip(__('ui.catalog.delivery'), ['delivery']));
        }
        if ($request->boolean('all_regions')) {
            $chips->push($chip(__('ui.catalog.all_regions'), ['all_regions']));
        }

        return $chips;
    }
}
