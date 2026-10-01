<?php

namespace Tests\Feature;

use App\Models\Article;
use App\Models\ArticleCategory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GuidePagesTest extends TestCase
{
    use RefreshDatabase;

    public function test_guide_search_finds_articles_by_their_text(): void
    {
        $category = ArticleCategory::create(['name' => 'العناية', 'slug' => 'test-care', 'sort_order' => 1]);
        $this->article($category, 'غذاء الكناري', 'الخس والبيض المسلوق مفيدان.');
        $this->article($category, 'تنظيف القفص', 'نظّف القفص أسبوعيًا.');

        $this->get(route('guide.index', ['q' => 'البيض']))
            ->assertOk()
            ->assertSee('غذاء الكناري')
            ->assertDontSee('تنظيف القفص');
    }

    public function test_article_page_shows_a_table_of_contents_for_its_headings(): void
    {
        $category = ArticleCategory::create(['name' => 'الوراثة', 'slug' => 'test-genetics', 'sort_order' => 1]);
        $article = $this->article($category, 'أخطاء شائعة', "«اللون يحدد الجنس.»\nفي معظم الحالات لا.\n\n«الأحمر يلد أحمر.»\nيحتاج إلى تغذية.");

        $this->get(route('guide.show', [$category, $article]))
            ->assertOk()
            ->assertSee(__('ui.guide.on_this_page'))
            ->assertSee('href="#section-2"', false)
            ->assertSee('<h3 id="section-1">', false);
    }

    public function test_category_page_lists_its_articles_and_the_other_sections(): void
    {
        $care = ArticleCategory::create(['name' => 'العناية', 'slug' => 'test-care', 'sort_order' => 1]);
        ArticleCategory::create(['name' => 'التفريخ', 'slug' => 'test-breeding', 'sort_order' => 2]);
        $this->article($care, 'غذاء الكناري', 'نص.');

        $this->get(route('guide.category', $care))
            ->assertOk()
            ->assertSee('غذاء الكناري')
            ->assertSee('التفريخ');
    }

    private function article(ArticleCategory $category, string $title, string $content): Article
    {
        return Article::create([
            'category_id' => $category->id,
            'title' => $title,
            'slug' => 'a-'.str()->random(8),
            'summary' => 'ملخص',
            'content' => $content,
            'status' => 'published',
            'published_at' => now()->subDay(),
        ]);
    }
}
