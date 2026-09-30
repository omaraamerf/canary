<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\SaveRegionRequest;
use App\Models\Region;
use App\Services\CatalogService;

class RegionController extends Controller
{
    public function __construct(private readonly CatalogService $catalog) {}

    public function index()
    {
        return view('admin.regions.index', ['regions' => Region::orderBy('sort_order')->orderBy('name')->get()]);
    }

    public function store(SaveRegionRequest $request)
    {
        $this->catalog->saveRegion($request->validated());

        return back()->with('success', __('تمت إضافة المنطقة.'));
    }

    public function update(SaveRegionRequest $request, Region $region)
    {
        $this->catalog->saveRegion($request->validated(), $region);

        return back()->with('success', __('تم تحديث المنطقة.'));
    }
}
