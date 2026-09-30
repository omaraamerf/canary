<?php

namespace Database\Seeders;

use App\Models\Country;
use App\Models\Region;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * Countries and their first-level regions (governorates / provinces).
 *
 * Safe to run repeatedly: existing countries and regions are kept as they are
 * (including anything an admin changed), and only missing ones are added.
 */
class LocationSeeder extends Seeder
{
    /**
     * Regions are either a name (slug becomes "{code}-{position}") or [name, slug].
     */
    public const COUNTRIES = [
        ['code' => 'SY', 'name' => 'سوريا', 'name_en' => 'Syria', 'regions' => [
            ['دمشق', 'damascus'], ['ريف دمشق', 'rif-dimashq'], ['حلب', 'aleppo'], ['حمص', 'homs'],
            ['حماة', 'hama'], ['اللاذقية', 'latakia'], ['طرطوس', 'tartus'], ['إدلب', 'idlib'],
            ['الرقة', 'raqqa'], ['دير الزور', 'deir-ez-zor'], ['الحسكة', 'al-hasakah'], ['درعا', 'daraa'],
            ['السويداء', 'as-suwayda'], ['القنيطرة', 'quneitra'],
        ]],
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

    public function run(): void
    {
        DB::transaction(function () {
            foreach (self::COUNTRIES as $position => $data) {
                $country = Country::firstOrCreate(
                    ['code' => $data['code']],
                    ['name' => $data['name'], 'name_en' => $data['name_en'], 'active' => true, 'sort_order' => $position + 1],
                );

                if ($data['code'] === 'SY') {
                    // Regions created before countries existed belong to the original Syrian marketplace.
                    Region::query()->whereNull('country_id')->update(['country_id' => $country->id]);
                }

                foreach ($data['regions'] as $index => $entry) {
                    [$name, $slug] = is_array($entry) ? $entry : [$entry, strtolower($data['code']).'-'.($index + 1)];

                    $region = Region::query()->where('country_id', $country->id)->where('name', $name)->first()
                        ?? Region::query()->where('slug', $slug)->first();

                    if ($region) {
                        $region->country_id ??= $country->id;
                        $region->save();

                        continue;
                    }

                    Region::create([
                        'country_id' => $country->id,
                        'name' => $name,
                        'slug' => $slug,
                        'active' => true,
                        'sort_order' => $index + 1,
                    ]);
                }
            }

            $this->backfillCountries();
        });
    }

    /**
     * Users and posts that only have a region get that region's country.
     */
    private function backfillCountries(): void
    {
        $regionIds = DB::table('users')->whereNull('country_id')->whereNotNull('region_id')->pluck('region_id')
            ->merge(DB::table('posts')->whereNull('country_id')->whereNotNull('region_id')->pluck('region_id'))
            ->unique();

        foreach (Region::query()->whereKey($regionIds)->whereNotNull('country_id')->pluck('country_id', 'id') as $regionId => $countryId) {
            DB::table('users')->where('region_id', $regionId)->whereNull('country_id')->update(['country_id' => $countryId]);
            DB::table('posts')->where('region_id', $regionId)->whereNull('country_id')->update(['country_id' => $countryId]);
        }
    }
}
