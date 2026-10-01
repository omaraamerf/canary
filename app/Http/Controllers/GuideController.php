<?php

namespace App\Http\Controllers;

use App\Enums\SettingKey;
use App\Models\Article;
use App\Models\ArticleCategory;
use App\Models\Setting;
use App\Support\ArticleContent;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;

class GuideController extends Controller
{
    public function index(Request $request)
    {
        $this->ensureEnabled();
        $query = $request->string('q')->trim()->toString();

        return view('guide.index', [
            'categories' => $this->categories(),
            'query' => $query,
            'results' => $query === '' ? null : Article::with('category')
                ->published()
                ->where(fn ($match) => $match
                    ->where('title', 'like', "%{$query}%")
                    ->orWhere('summary', 'like', "%{$query}%")
                    ->orWhere('content', 'like', "%{$query}%"))
                ->latest('published_at')
                ->paginate(12)
                ->withQueryString(),
            'latestArticles' => Article::with(['category', 'tags'])
                ->published()
                ->latest('published_at')
                ->take(5)
                ->get(),
            'articlesCount' => Article::published()->count(),
        ]);
    }

    public function category(ArticleCategory $category)
    {
        $this->ensureEnabled();

        return view('guide.category', [
            'category' => $category,
            'categories' => $this->categories(),
            'articles' => $category->articles()
                ->with(['tags', 'category'])
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
            'content' => ArticleContent::render($article->content),
            'categories' => $this->categories(),
            'relatedArticles' => Article::with('category')
                ->published()
                ->where('category_id', $category->id)
                ->where('id', '!=', $article->id)
                ->latest('published_at')
                ->take(3)
                ->get(),
        ]);
    }

    private function categories(): Collection
    {
        return ArticleCategory::query()
            ->withCount(['articles as published_articles_count' => fn ($query) => $query->published()])
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();
    }

    private function ensureEnabled(): void
    {
        abort_unless(Setting::boolean(SettingKey::GuideEnabled->value, true), 404);
    }
}
