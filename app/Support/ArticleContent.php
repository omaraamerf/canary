<?php

namespace App\Support;

use Illuminate\Support\Str;

/**
 * Renders guide article text (Markdown typed by admins) into safe HTML with heading anchors.
 *
 * Besides Markdown (## headings, - lists, > notes), a line written alone in «guillemets»
 * becomes a sub-heading: older articles use that convention for their sub-topics.
 */
final class ArticleContent
{
    private const WORDS_PER_MINUTE = 180;

    /**
     * @return array{html: string, headings: list<array{id: string, text: string, level: int}>}
     */
    public static function render(?string $content): array
    {
        $markdown = preg_replace(
            ['/^#\s+/mu', '/^«([^\n«»]{2,160})»\.?[ \t]*$/mu'],
            ['## ', '### «$1»'],
            (string) $content,
        );

        $html = Str::markdown($markdown, ['html_input' => 'strip', 'allow_unsafe_links' => false]);
        $headings = [];

        $html = preg_replace_callback('#<h([23])>(.*?)</h\1>#su', function (array $match) use (&$headings): string {
            $id = 'section-'.(count($headings) + 1);
            $headings[] = ['id' => $id, 'text' => html_entity_decode(strip_tags($match[2]), ENT_QUOTES), 'level' => (int) $match[1]];

            return "<h{$match[1]} id=\"{$id}\">{$match[2]}</h{$match[1]}>";
        }, $html);

        return ['html' => $html, 'headings' => $headings];
    }

    public static function readingMinutes(?string $content): int
    {
        $words = preg_split('/\s+/u', trim(strip_tags((string) $content)), -1, PREG_SPLIT_NO_EMPTY);

        return max(1, (int) ceil(count($words) / self::WORDS_PER_MINUTE));
    }
}
