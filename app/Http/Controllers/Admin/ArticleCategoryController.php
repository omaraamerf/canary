<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\SaveArticleCategoryRequest;
use App\Models\ArticleCategory;
use App\Services\CatalogService;

class ArticleCategoryController extends Controller
{
    public function __construct(private readonly CatalogService $catalog) {}

    public function index()
    {
        return view('admin.guide.categories', [
            'categories' => ArticleCategory::withCount('articles')->orderBy('sort_order')->orderBy('name')->get(),
        ]);
    }

    public function store(SaveArticleCategoryRequest $request)
    {
        $this->catalog->saveArticleCategory($request->validated());

        return back()->with('success', 'تمت إضافة قسم الدليل.');
    }

    public function update(SaveArticleCategoryRequest $request, ArticleCategory $guideCategory)
    {
        $this->catalog->saveArticleCategory($request->validated(), $guideCategory);

        return back()->with('success', 'تم تحديث قسم الدليل.');
    }
}
