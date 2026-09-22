<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    private const GOVERNORATES = [
        ['name' => 'دمشق', 'slug' => 'damascus'],
        ['name' => 'ريف دمشق', 'slug' => 'rif-dimashq'],
        ['name' => 'حلب', 'slug' => 'aleppo'],
        ['name' => 'حمص', 'slug' => 'homs'],
        ['name' => 'حماة', 'slug' => 'hama'],
        ['name' => 'اللاذقية', 'slug' => 'latakia'],
        ['name' => 'طرطوس', 'slug' => 'tartus'],
        ['name' => 'إدلب', 'slug' => 'idlib'],
        ['name' => 'الرقة', 'slug' => 'raqqa'],
        ['name' => 'دير الزور', 'slug' => 'deir-ez-zor'],
        ['name' => 'الحسكة', 'slug' => 'al-hasakah'],
        ['name' => 'درعا', 'slug' => 'daraa'],
        ['name' => 'السويداء', 'slug' => 'as-suwayda'],
        ['name' => 'القنيطرة', 'slug' => 'quneitra'],
    ];

    public function up(): void
    {
        foreach (self::GOVERNORATES as $index => $governorate) {
            DB::table('regions')->updateOrInsert(
                ['name' => $governorate['name']],
                [
                    'slug' => $governorate['slug'],
                    'active' => true,
                    'sort_order' => $index + 1,
                    'updated_at' => now(),
                    'created_at' => now(),
                ]
            );
        }
    }

    public function down(): void
    {
        $slugs = array_column(self::GOVERNORATES, 'slug');

        DB::table('regions')
            ->whereIn('slug', $slugs)
            ->whereNotExists(fn ($query) => $query->selectRaw('1')->from('birds')->whereColumn('birds.region_id', 'regions.id'))
            ->whereNotExists(fn ($query) => $query->selectRaw('1')->from('seller_profiles')->whereColumn('seller_profiles.region_id', 'regions.id'))
            ->whereNotExists(fn ($query) => $query->selectRaw('1')->from('users')->whereColumn('users.region_id', 'regions.id'))
            ->whereNotExists(fn ($query) => $query->selectRaw('1')->from('orders')->whereColumn('orders.buyer_region_id', 'regions.id'))
            ->delete();
    }
};
