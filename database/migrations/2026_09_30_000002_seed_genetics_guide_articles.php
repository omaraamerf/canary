<?php

use Database\Seeders\GeneticsGuideSeeder;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    private const SLUGS = [
        'canary-genetics-basics',
        'canary-sex-linked-inheritance',
        'canary-recessive-dominant-traits',
        'canary-lethal-factors-dominant-white-crest',
        'canary-colors-lipochrome-melanin',
        'canary-intensive-non-intensive-mosaic',
        'red-factor-canary-genetics-feeding',
        'canary-breeding-plan-records-inbreeding',
        'canary-genetics-common-myths',
        'new-canary-quarantine',
        'canary-chick-ringing',
    ];

    public function up(): void
    {
        (new GeneticsGuideSeeder)->run();
    }

    public function down(): void
    {
        $ids = DB::table('articles')->whereIn('slug', self::SLUGS)->pluck('id');

        DB::table('article_tag')->whereIn('article_id', $ids)->delete();
        DB::table('articles')->whereIn('id', $ids)->delete();
    }
};
