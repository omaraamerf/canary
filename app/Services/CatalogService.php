<?php

namespace App\Services;

use App\Models\ArticleCategory;
use App\Models\Breed;
use App\Models\Region;
use App\Support\UniqueSlug;

class CatalogService
{
    public function __construct(private readonly UniqueSlug $slugs) {}

    public function saveBreed(array $data, ?Breed $breed = null): Breed
    {
        $breed ??= new Breed;
        $breed->fill([
            ...$data,
            'slug' => $breed->exists && $breed->name === $data['name']
                ? $breed->slug
                : $this->slugs->for(Breed::class, $data['name'], 'breed', $breed->id),
        ])->save();

        return $breed;
    }

    public function saveRegion(array $data, ?Region $region = null): Region
    {
        $region ??= new Region;
        $region->fill([
            ...$data,
            'slug' => $region->exists && $region->name === $data['name']
                ? $region->slug
                : $this->slugs->for(Region::class, $data['name'], 'region', $region->id),
            'sort_order' => $data['sort_order'] ?? 0,
        ])->save();

        return $region;
    }

    public function saveArticleCategory(array $data, ?ArticleCategory $category = null): ArticleCategory
    {
        $category ??= new ArticleCategory;
        $category->fill([
            ...$data,
            'slug' => $category->exists && $category->name === $data['name']
                ? $category->slug
                : $this->slugs->for(ArticleCategory::class, $data['name'], 'guide-category', $category->id),
            'sort_order' => $data['sort_order'] ?? 0,
        ])->save();

        return $category;
    }
}
