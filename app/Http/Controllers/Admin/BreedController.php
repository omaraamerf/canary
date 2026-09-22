<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\SaveBreedRequest;
use App\Models\Breed;
use App\Services\CatalogService;

class BreedController extends Controller
{
    public function __construct(private readonly CatalogService $catalog) {}

    public function index()
    {
        return view('admin.breeds.index', ['breeds' => Breed::withCount('birds')->orderBy('name')->get()]);
    }

    public function store(SaveBreedRequest $request)
    {
        $this->catalog->saveBreed($request->validated());

        return back()->with('success', 'تمت إضافة السلالة.');
    }

    public function update(SaveBreedRequest $request, Breed $breed)
    {
        $this->catalog->saveBreed($request->validated(), $breed);

        return back()->with('success', 'تم تحديث السلالة.');
    }
}
