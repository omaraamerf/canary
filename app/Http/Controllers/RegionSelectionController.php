<?php

namespace App\Http\Controllers;

use App\Http\Requests\SelectRegionRequest;
use App\Models\Country;
use App\Models\Region;
use App\Support\MarketplaceLocation;

class RegionSelectionController extends Controller
{
    public function store(SelectRegionRequest $request, MarketplaceLocation $location)
    {
        $region = $request->validated('region_id') ? Region::find($request->validated('region_id')) : null;
        $country = $region ? null : ($request->validated('country_id') ? Country::find($request->validated('country_id')) : null);

        $location->choose($country, $region);

        return back();
    }
}
