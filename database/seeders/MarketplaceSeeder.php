<?php

namespace Database\Seeders;

use App\Models\Bird;
use App\Models\Breed;
use App\Models\Region;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class MarketplaceSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::updateOrCreate(
            ['email' => 'admin@canary.local'],
            ['name' => 'مدير كناري', 'password' => Hash::make('password'), 'role' => 'admin', 'status' => 'active']
        );

        $breeds = collect([
            ['name' => 'يوركشاير', 'slug' => 'yorkshire', 'description' => 'كناري طويل القامة بهيئة رشيقة وصوت واضح.'],
            ['name' => 'جلوستر', 'slug' => 'gloster', 'description' => 'سلالة صغيرة مميزة بالتاج الدائري.'],
            ['name' => 'بلدي', 'slug' => 'local', 'description' => 'طيور قوية ومتأقلمة، مناسبة للمربين الجدد.'],
        ])->mapWithKeys(function ($breed) {
            $model = Breed::updateOrCreate(['slug' => $breed['slug']], [...$breed, 'active' => true]);
            return [$breed['slug'] => $model];
        });

        $regions = collect([
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
        ])->mapWithKeys(function ($data, $index) {
            $region = Region::updateOrCreate(
                ['name' => $data['name']],
                ['slug' => $data['slug'], 'active' => true, 'sort_order' => $index + 1]
            );

            return [$data['name'] => $region];
        });

        Setting::put('seller_approval_required', '1');
        Setting::put('listing_approval_required', '1');

        $birds = [
            ['title' => 'ذكر يوركشاير أصفر مغرد', 'slug' => 'yellow-yorkshire-singer', 'breed' => 'yorkshire', 'sex' => 'male', 'hatch_year' => 2025, 'color' => 'أصفر صافي', 'molt_status' => 'ready', 'breeding_ready' => true, 'singing_status' => 'singing', 'ring_number' => 'YR-2518', 'price' => 650, 'city' => 'دمشق', 'delivery_type' => 'agreement', 'featured' => true, 'description' => 'ذكر نشيط بصحة جيدة، تغريد واضح ومتواصل. تمت معاينته والعناية به يوميًا.', 'image' => '/images/birds/yellow-canary.jpg'],
            ['title' => 'جلوستر متوج هادئ', 'slug' => 'calm-crowned-gloster', 'breed' => 'gloster', 'sex' => 'male', 'hatch_year' => 2025, 'color' => 'أخضر وأصفر', 'molt_status' => 'ready', 'breeding_ready' => false, 'singing_status' => 'singing', 'ring_number' => 'GL-2507', 'price' => 520, 'city' => 'حلب', 'delivery_type' => 'pickup', 'featured' => true, 'description' => 'جلوستر بتاج متناسق وحركة هادئة. مناسب للهواة ومحبي السلالة.', 'image' => '/images/birds/gloster-canary.jpg'],
            ['title' => 'أنثى كناري جاهزة', 'slug' => 'ready-female-canary', 'breed' => 'local', 'sex' => 'female', 'hatch_year' => 2024, 'color' => 'أصفر فاتح', 'molt_status' => 'ready', 'breeding_ready' => true, 'singing_status' => 'female', 'ring_number' => null, 'price' => 280, 'city' => 'جرمانا', 'region' => 'ريف دمشق', 'delivery_type' => 'delivery', 'featured' => true, 'description' => 'أنثى بصحة ممتازة ونشيطة، جاهزة للموسم ومتعودة على الخلطات المحلية.', 'image' => '/images/birds/classic-canary.jpg'],
            ['title' => 'ذكر بلدي صغير', 'slug' => 'young-local-male', 'breed' => 'local', 'sex' => 'male', 'hatch_year' => 2026, 'color' => 'ليموني', 'molt_status' => 'young', 'breeding_ready' => false, 'singing_status' => 'young', 'ring_number' => null, 'price' => 220, 'city' => 'حمص', 'delivery_type' => 'agreement', 'featured' => false, 'description' => 'فرخ نشيط بدأ بمحاولات التغريد، مناسب للتدريب والتربية المنزلية.', 'image' => '/images/birds/yellow-canary.jpg'],
            ['title' => 'يوركشاير أبيض وأصفر', 'slug' => 'white-yellow-yorkshire', 'breed' => 'yorkshire', 'sex' => 'unknown', 'hatch_year' => 2026, 'color' => 'أبيض وأصفر', 'molt_status' => 'young', 'breeding_ready' => false, 'singing_status' => 'young', 'ring_number' => 'YR-2631', 'price' => 390, 'city' => 'حماة', 'delivery_type' => 'pickup', 'featured' => false, 'description' => 'طائر صغير بريش نظيف وحركة ممتازة، الجنس غير مؤكد حتى الآن.', 'image' => '/images/birds/classic-canary.jpg'],
            ['title' => 'جلوستر أصفر للتربية', 'slug' => 'yellow-gloster-breeder', 'breed' => 'gloster', 'sex' => 'female', 'hatch_year' => 2025, 'color' => 'أصفر مخضر', 'molt_status' => 'ready', 'breeding_ready' => true, 'singing_status' => 'female', 'ring_number' => 'GL-2522', 'price' => 440, 'city' => 'اللاذقية', 'delivery_type' => 'delivery', 'featured' => false, 'description' => 'أنثى جلوستر متناسقة ومعتادة على القفص، مناسبة للتزاوج.', 'image' => '/images/birds/gloster-canary.jpg'],
        ];

        foreach ($birds as $data) {
            $bird = Bird::updateOrCreate(['slug' => $data['slug']], [
                'seller_id' => $admin->id,
                'breed_id' => $breeds[$data['breed']]->id,
                'title' => $data['title'],
                'sex' => $data['sex'],
                'hatch_year' => $data['hatch_year'],
                'color' => $data['color'],
                'molt_status' => $data['molt_status'],
                'breeding_ready' => $data['breeding_ready'],
                'singing_status' => $data['singing_status'],
                'ring_number' => $data['ring_number'],
                'price' => $data['price'],
                'currency' => 'SAR',
                'city' => $data['city'],
                'region_id' => $regions[$data['region'] ?? $data['city']]->id,
                'delivery_type' => $data['delivery_type'],
                'description' => $data['description'],
                'status' => 'available',
                'featured' => $data['featured'],
                'approval_status' => 'approved',
                'published_at' => now(),
            ]);

            $bird->media()->updateOrCreate(
                ['type' => 'image', 'sort_order' => 0],
                ['url' => $data['image']]
            );
        }
    }
}
