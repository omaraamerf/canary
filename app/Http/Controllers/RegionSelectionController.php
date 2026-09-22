<?php

namespace App\Http\Controllers;

use App\Http\Requests\SelectRegionRequest;
use App\Models\Region;

class RegionSelectionController extends Controller
{
    public function store(SelectRegionRequest $request)
    {
        $data = $request->validated();

        if ($data['region'] === 'all') {
            $request->session()->forget('marketplace_region_id');
            $request->session()->put('marketplace_all_regions', true);
        } else {
            $region = Region::where('active', true)->findOrFail($data['region']);
            $request->session()->put('marketplace_region_id', $region->id);
            $request->session()->forget('marketplace_all_regions');
        }

        $request->session()->put('marketplace_region_chosen', true);

        return back();
    }
}
