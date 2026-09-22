<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\SaveArticleRequest;
use App\Models\Article;
use App\Models\ArticleCategory;
use App\Services\ArticleService;
use Illuminate\Http\Request;

class ArticleController extends Controller
{
    public function __construct(private readonly ArticleService $articles) {}

    public function index(Request $request)
    {
        return view('admin.guide.articles.index', [
            'articles' => Article::with(['category', 'tags'])
                ->when($request->filled('status'), fn ($query) => $query->where('status', $request->string('status')))
                ->when($request->filled('category'), fn ($query) => $query->where('category_id', $request->integer('category')))
                ->latest()
                ->paginate(20)
                ->withQueryString(),
            'categories' => ArticleCategory::orderBy('sort_order')->orderBy('name')->get(),
        ]);
    }

    public function create()
    {
        return view('admin.guide.articles.form', [
            'article' => new Article,
            'categories' => ArticleCategory::orderBy('sort_order')->orderBy('name')->get(),
        ]);
    }

    public function store(SaveArticleRequest $request)
    {
        $this->articles->create($request->validated());

        return redirect()->route('admin.guide-articles.index')->with('success', 'تم حفظ المقال.');
    }

    public function edit(Article $guideArticle)
    {
        $guideArticle->load('tags');

        return view('admin.guide.articles.form', [
            'article' => $guideArticle,
            'categories' => ArticleCategory::orderBy('sort_order')->orderBy('name')->get(),
        ]);
    }

    public function update(SaveArticleRequest $request, Article $guideArticle)
    {
        $this->articles->update($guideArticle, $request->validated());

        return redirect()->route('admin.guide-articles.index')->with('success', 'تم تحديث المقال.');
    }
}
