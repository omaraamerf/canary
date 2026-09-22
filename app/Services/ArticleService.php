<?php

namespace App\Services;

use App\Enums\ArticleStatus;
use App\Models\Article;
use App\Models\Tag;
use App\Support\UniqueSlug;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ArticleService
{
    public function __construct(private readonly UniqueSlug $slugs) {}

    public function create(array $data): Article
    {
        return DB::transaction(function () use ($data) {
            $article = Article::create([
                ...Arr::except($data, 'tags'),
                'slug' => $this->slugs->for(Article::class, $data['title'], 'article'),
                'published_at' => $data['status'] === ArticleStatus::Published->value
                    ? ($data['published_at'] ?? now())
                    : null,
            ]);

            $this->syncTags($article, $data['tags'] ?? '');

            return $article;
        });
    }

    public function update(Article $article, array $data): Article
    {
        return DB::transaction(function () use ($article, $data) {
            $publishedAt = match ($data['status']) {
                ArticleStatus::Published->value => $data['published_at'] ?? $article->published_at ?? now(),
                ArticleStatus::Archived->value => $article->published_at ?? ($data['published_at'] ?? null),
                default => null,
            };

            $article->update([
                ...Arr::except($data, 'tags'),
                'slug' => $article->title === $data['title']
                    ? $article->slug
                    : $this->slugs->for(Article::class, $data['title'], 'article', $article->id),
                'published_at' => $publishedAt,
            ]);

            $this->syncTags($article, $data['tags'] ?? '');

            return $article;
        });
    }

    private function syncTags(Article $article, string $value): void
    {
        $ids = collect(preg_split('/[,،\r\n]+/u', $value))
            ->map(fn ($name) => trim($name))
            ->filter()
            ->unique()
            ->map(function ($name) {
                return Tag::firstOrCreate(
                    ['name' => $name],
                    ['slug' => Str::slug($name) ?: 'tag-'.substr(sha1($name), 0, 10)]
                )->id;
            });

        $article->tags()->sync($ids);
    }
}
