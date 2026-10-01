<?php

namespace Tests\Unit;

use App\Support\ArticleContent;
use Tests\TestCase;

class ArticleContentTest extends TestCase
{
    public function test_markdown_headings_get_anchors_for_the_table_of_contents(): void
    {
        $result = ArticleContent::render("## التغذية\nفقرة.\n\n### الفيتامينات\nفقرة أخرى.");

        $this->assertStringContainsString('<h2 id="section-1">التغذية</h2>', $result['html']);
        $this->assertStringContainsString('<h3 id="section-2">الفيتامينات</h3>', $result['html']);
        $this->assertSame([
            ['id' => 'section-1', 'text' => 'التغذية', 'level' => 2],
            ['id' => 'section-2', 'text' => 'الفيتامينات', 'level' => 3],
        ], $result['headings']);
    }

    public function test_a_line_alone_in_guillemets_becomes_a_sub_heading(): void
    {
        $result = ArticleContent::render("«الطائر الأحمر يلد أحمر.»\nاللون الأحمر يحتاج إلى تغذية لونية.");

        $this->assertSame('«الطائر الأحمر يلد أحمر.»', $result['headings'][0]['text']);
        $this->assertStringContainsString('<p>اللون الأحمر يحتاج إلى تغذية لونية.</p>', $result['html']);
    }

    public function test_quotes_inside_a_sentence_stay_in_the_paragraph(): void
    {
        $result = ArticleContent::render('انشرها في قسم «اسأل المربين» مع الصور.');

        $this->assertSame([], $result['headings']);
        $this->assertStringContainsString('<p>انشرها في قسم «اسأل المربين» مع الصور.</p>', $result['html']);
    }

    public function test_lists_and_notes_render_and_raw_html_is_dropped(): void
    {
        $result = ArticleContent::render("مقدمة:\n- البني\n- الأجات\n\n> احفظ سجلًا للأعشاش.\n\n<script>alert(1)</script>\n[رابط](javascript:alert(1))");

        $this->assertStringContainsString('<li>البني</li>', $result['html']);
        $this->assertStringContainsString('<blockquote>', $result['html']);
        $this->assertStringNotContainsString('<script', $result['html']);
        $this->assertStringNotContainsString('javascript:', $result['html']);
    }

    public function test_a_top_level_heading_is_demoted_below_the_page_title(): void
    {
        $this->assertStringContainsString('<h2 id="section-1">عنوان</h2>', ArticleContent::render('# عنوان')['html']);
    }

    public function test_reading_time_is_at_least_one_minute(): void
    {
        $this->assertSame(1, ArticleContent::readingMinutes('كلمة'));
        $this->assertSame(2, ArticleContent::readingMinutes(str_repeat('كلمة ', 181)));
    }
}
