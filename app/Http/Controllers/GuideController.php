<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\ArticleCategory;
use App\Models\Setting;

class GuideController extends Controller
{
    public function index()
    {
        $this->ensureEnabled();

        return view('guide.index', [
            'categories' => ArticleCategory::query()
                ->withCount(['articles as published_articles_count' => fn ($query) => $query->published()])
                ->orderBy('sort_order')
                ->orderBy('name')
                ->get(),
            'latestArticles' => Article::with(['category', 'tags'])
                ->published()
                ->latest('published_at')
                ->take(6)
                ->get(),
        ]);
    }

    public function category(ArticleCategory $category)
    {
        $this->ensureEnabled();

        return view('guide.category', [
            'category' => $category,
            'articles' => $category->articles()
                ->with('tags')
                ->published()
                ->latest('published_at')
                ->paginate(12),
        ]);
    }

    public function show(ArticleCategory $category, Article $article)
    {
        $this->ensureEnabled();
        abort_unless($article->category_id === $category->id, 404);
        abort_unless($article->status === 'published' && $article->published_at?->isPast(), 404);
        $article->load(['category', 'tags']);

        return view('guide.show', [
            'article' => $article,
            'relatedArticles' => Article::with('category')
                ->published()
                ->where('category_id', $category->id)
                ->where('id', '!=', $article->id)
                ->latest('published_at')
                ->take(3)
                ->get(),
        ]);
    }

    private function ensureEnabled(): void
    {
        abort_unless(Setting::boolean('guide_enabled', true), 404);
    }
}
