<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Arab countries and their first-level administrative divisions.
     * Syria's governorates already exist and are attached to Syria below.
     */
    private const COUNTRIES = [
        ['code' => 'SY', 'name' => 'سوريا', 'name_en' => 'Syria', 'regions' => []],
        ['code' => 'SA', 'name' => 'السعودية', 'name_en' => 'Saudi Arabia', 'regions' => ['الرياض', 'مكة المكرمة', 'المدينة المنورة', 'القصيم', 'المنطقة الشرقية', 'عسير', 'تبوك', 'حائل', 'الحدود الشمالية', 'جازان', 'نجران', 'الباحة', 'الجوف']],
        ['code' => 'JO', 'name' => 'الأردن', 'name_en' => 'Jordan', 'regions' => ['عمّان', 'إربد', 'الزرقاء', 'البلقاء', 'المفرق', 'جرش', 'عجلون', 'مادبا', 'الكرك', 'الطفيلة', 'معان', 'العقبة']],
        ['code' => 'LB', 'name' => 'لبنان', 'name_en' => 'Lebanon', 'regions' => ['بيروت', 'جبل لبنان', 'كسروان جبيل', 'الشمال', 'عكار', 'البقاع', 'بعلبك الهرمل', 'الجنوب', 'النبطية']],
        ['code' => 'IQ', 'name' => 'العراق', 'name_en' => 'Iraq', 'regions' => ['بغداد', 'البصرة', 'نينوى', 'أربيل', 'السليمانية', 'دهوك', 'حلبجة', 'كركوك', 'الأنبار', 'ديالى', 'صلاح الدين', 'بابل', 'كربلاء', 'النجف', 'واسط', 'القادسية', 'المثنى', 'ذي قار', 'ميسان']],
        ['code' => 'PS', 'name' => 'فلسطين', 'name_en' => 'Palestine', 'regions' => ['القدس', 'رام الله والبيرة', 'الخليل', 'بيت لحم', 'نابلس', 'جنين', 'طولكرم', 'قلقيلية', 'سلفيت', 'طوباس', 'أريحا والأغوار', 'غزة', 'شمال غزة', 'دير البلح', 'خان يونس', 'رفح']],
        ['code' => 'EG', 'name' => 'مصر', 'name_en' => 'Egypt', 'regions' => ['القاهرة', 'الجيزة', 'الإسكندرية', 'القليوبية', 'الشرقية', 'الدقهلية', 'الغربية', 'المنوفية', 'البحيرة', 'كفر الشيخ', 'دمياط', 'بورسعيد', 'الإسماعيلية', 'السويس', 'الفيوم', 'بني سويف', 'المنيا', 'أسيوط', 'سوهاج', 'قنا', 'الأقصر', 'أسوان', 'البحر الأحمر', 'الوادي الجديد', 'مطروح', 'شمال سيناء', 'جنوب سيناء']],
        ['code' => 'KW', 'name' => 'الكويت', 'name_en' => 'Kuwait', 'regions' => ['العاصمة', 'حولي', 'الفروانية', 'الأحمدي', 'الجهراء', 'مبارك الكبير']],
        ['code' => 'AE', 'name' => 'الإمارات', 'name_en' => 'United Arab Emirates', 'regions' => ['أبوظبي', 'دبي', 'الشارقة', 'عجمان', 'أم القيوين', 'رأس الخيمة', 'الفجيرة']],
        ['code' => 'QA', 'name' => 'قطر', 'name_en' => 'Qatar', 'regions' => ['الدوحة', 'الريان', 'الوكرة', 'الخور', 'الشمال', 'أم صلال', 'الضعاين', 'الشحانية']],
        ['code' => 'BH', 'name' => 'البحرين', 'name_en' => 'Bahrain', 'regions' => ['العاصمة', 'المحرق', 'الشمالية', 'الجنوبية']],
        ['code' => 'OM', 'name' => 'عُمان', 'name_en' => 'Oman', 'regions' => ['مسقط', 'ظفار', 'مسندم', 'البريمي', 'الداخلية', 'شمال الباطنة', 'جنوب الباطنة', 'جنوب الشرقية', 'شمال الشرقية', 'الظاهرة', 'الوسطى']],
        ['code' => 'YE', 'name' => 'اليمن', 'name_en' => 'Yemen', 'regions' => ['أمانة العاصمة', 'صنعاء', 'عدن', 'تعز', 'الحديدة', 'إب', 'ذمار', 'حضرموت', 'المهرة', 'شبوة', 'أبين', 'لحج', 'الضالع', 'البيضاء', 'مأرب', 'الجوف', 'صعدة', 'حجة', 'عمران', 'المحويت', 'ريمة', 'أرخبيل سقطرى']],
        ['code' => 'LY', 'name' => 'ليبيا', 'name_en' => 'Libya', 'regions' => ['طرابلس', 'بنغازي', 'مصراتة', 'الزاوية', 'الجفارة', 'المرقب', 'النقاط الخمس', 'الجبل الغربي', 'نالوت', 'البطنان', 'درنة', 'الجبل الأخضر', 'المرج', 'الواحات', 'الكفرة', 'سرت', 'الجفرة', 'سبها', 'مرزق', 'وادي الحياة', 'وادي الشاطئ', 'غات']],
        ['code' => 'TN', 'name' => 'تونس', 'name_en' => 'Tunisia', 'regions' => ['تونس', 'أريانة', 'بن عروس', 'منوبة', 'نابل', 'زغوان', 'بنزرت', 'باجة', 'جندوبة', 'الكاف', 'سليانة', 'سوسة', 'المنستير', 'المهدية', 'صفاقس', 'القيروان', 'القصرين', 'سيدي بوزيد', 'قابس', 'مدنين', 'تطاوين', 'قفصة', 'توزر', 'قبلي']],
        ['code' => 'DZ', 'name' => 'الجزائر', 'name_en' => 'Algeria', 'regions' => ['أدرار', 'الشلف', 'الأغواط', 'أم البواقي', 'باتنة', 'بجاية', 'بسكرة', 'بشار', 'البليدة', 'البويرة', 'تمنراست', 'تبسة', 'تلمسان', 'تيارت', 'تيزي وزو', 'الجزائر', 'الجلفة', 'جيجل', 'سطيف', 'سعيدة', 'سكيكدة', 'سيدي بلعباس', 'عنابة', 'قالمة', 'قسنطينة', 'المدية', 'مستغانم', 'المسيلة', 'معسكر', 'ورقلة', 'وهران', 'البيض', 'إليزي', 'برج بوعريريج', 'بومرداس', 'الطارف', 'تندوف', 'تيسمسيلت', 'الوادي', 'خنشلة', 'سوق أهراس', 'تيبازة', 'ميلة', 'عين الدفلى', 'النعامة', 'عين تموشنت', 'غرداية', 'غليزان', 'تيميمون', 'برج باجي مختار', 'أولاد جلال', 'بني عباس', 'عين صالح', 'عين قزام', 'تقرت', 'جانت', 'المغير', 'المنيعة']],
        ['code' => 'MA', 'name' => 'المغرب', 'name_en' => 'Morocco', 'regions' => ['طنجة تطوان الحسيمة', 'الشرق', 'فاس مكناس', 'الرباط سلا القنيطرة', 'بني ملال خنيفرة', 'الدار البيضاء سطات', 'مراكش آسفي', 'درعة تافيلالت', 'سوس ماسة', 'كلميم واد نون', 'العيون الساقية الحمراء', 'الداخلة وادي الذهب']],
        ['code' => 'SD', 'name' => 'السودان', 'name_en' => 'Sudan', 'regions' => ['الخرطوم', 'الجزيرة', 'النيل الأبيض', 'النيل الأزرق', 'سنار', 'القضارف', 'كسلا', 'البحر الأحمر', 'نهر النيل', 'الشمالية', 'شمال كردفان', 'جنوب كردفان', 'غرب كردفان', 'شمال دارفور', 'جنوب دارفور', 'شرق دارفور', 'غرب دارفور', 'وسط دارفور']],
    ];

    public function up(): void
    {
        Schema::create('countries', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('name_en')->nullable();
            $table->string('code', 2)->unique();
            $table->boolean('active')->default(true);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::table('regions', function (Blueprint $table) {
            $table->foreignId('country_id')->nullable()->after('id')->constrained()->nullOnDelete();
        });

        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('country_id')->nullable()->after('status')->constrained()->nullOnDelete();
            $table->text('bio')->nullable()->after('phone');
            $table->text('avatar_url')->nullable()->after('bio');
            $table->string('avatar_public_id')->nullable()->after('avatar_url');
        });

        Schema::table('posts', function (Blueprint $table) {
            $table->foreignId('country_id')->nullable()->after('breed_id')->constrained()->nullOnDelete();
        });

        $now = now();

        foreach (self::COUNTRIES as $position => $country) {
            $countryId = DB::table('countries')->insertGetId([
                'name' => $country['name'],
                'name_en' => $country['name_en'],
                'code' => $country['code'],
                'active' => true,
                'sort_order' => $position + 1,
                'created_at' => $now,
                'updated_at' => $now,
            ]);

            if ($country['code'] === 'SY') {
                // Every region created before countries existed belongs to the original Syrian marketplace.
                DB::table('regions')->whereNull('country_id')->update(['country_id' => $countryId]);

                continue;
            }

            foreach ($country['regions'] as $index => $name) {
                DB::table('regions')->insert([
                    'country_id' => $countryId,
                    'name' => $name,
                    'slug' => strtolower($country['code']).'-'.($index + 1),
                    'active' => true,
                    'sort_order' => $index + 1,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }
        }

        foreach (DB::table('regions')->whereNotNull('country_id')->pluck('country_id', 'id') as $regionId => $countryId) {
            DB::table('users')->where('region_id', $regionId)->update(['country_id' => $countryId]);
            DB::table('posts')->where('region_id', $regionId)->update(['country_id' => $countryId]);
        }
    }

    public function down(): void
    {
        $codes = collect(self::COUNTRIES)->where('code', '!=', 'SY')->pluck('code')->map(fn (string $code) => strtolower($code).'-%');

        DB::table('regions')->where(function ($query) use ($codes) {
            foreach ($codes as $pattern) {
                $query->orWhere('slug', 'like', $pattern);
            }
        })
            ->whereNotExists(fn ($query) => $query->selectRaw('1')->from('birds')->whereColumn('birds.region_id', 'regions.id'))
            ->whereNotExists(fn ($query) => $query->selectRaw('1')->from('seller_profiles')->whereColumn('seller_profiles.region_id', 'regions.id'))
            ->whereNotExists(fn ($query) => $query->selectRaw('1')->from('orders')->whereColumn('orders.buyer_region_id', 'regions.id'))
            ->delete();

        Schema::table('posts', fn (Blueprint $table) => $table->dropConstrainedForeignId('country_id'));
        Schema::table('users', function (Blueprint $table) {
            $table->dropConstrainedForeignId('country_id');
            $table->dropColumn(['bio', 'avatar_url', 'avatar_public_id']);
        });
        Schema::table('regions', fn (Blueprint $table) => $table->dropConstrainedForeignId('country_id'));
        Schema::dropIfExists('countries');
    }
};
