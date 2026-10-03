# رفقنا (Rifqna): خريطة المشروع

سوق لبيع طيور الكناري، مع دليل مقالات ومجتمع أسئلة. واجهة الموقع عربية أولاً (RTL) ومعها إنجليزية.

> **قاعدة هذا الملف:** اقرأه أولاً في كل مهمة بدل إعادة قراءة المشروع. عند أي تعديل يغيّر شيئاً مذكوراً هنا (ملف جديد، صفحة، صلاحية، قاعدة، مشكلة معروفة) حدّث القسم المناسب في نفس التعديل.

## قواعد إلزامية

- **ممنوع بدون إذن صريح في كل مرة:** `migrate` و `db:seed` و `migrate:fresh`، وأي أمر يكتب على قاعدة MySQL المحلية `canary`. وأيضاً `php artisan test`، و git commit/push.
- الاختبارات تعمل على SQLite في الذاكرة (`phpunit.xml`). كانت سابقاً تعمل على `canary` ومسحت بياناتها في 2026-10-01.
- `PermissionSeeder` يعيد صلاحيات أدوار البائع والعضو إلى قيمها الافتراضية (`syncPermissions`)، فيمسح ما عُدّل من صفحة الأدوار.
- عدّل فقط ما طُلب (skill: focused-clean-coding). اقرأ كل ما يخص المهمة دفعة واحدة، ثم اكتب التعديلات معاً. تأكد أن الشيء غير موجود قبل إضافته (مفاتيح الترجمة خصوصاً).
- الرد بالعربية.

## البيئة المحلية

- Windows، والقاعدة MySQL 8.0.30 على Laragon (`C:\laragon\data\mysql-8`، والـ binlog مفعّل). `.env`: `DB_DATABASE=canary`، `APP_URL=http://127.0.0.1:8080`، `APP_LOCALE=ar`.
- التشغيل: `php artisan serve --port=8080`.
- الواجهات تحتاج بناء Vite: `npm ci` ثم `npm run build` (أو `npm run dev`). المجلد `public/build` مستثنى في git.
  - خطأ `Cannot find native binding` (rolldown) معناه أن `node_modules` ناقص. احذفه وشغّل `npm ci`، ولا تحذف `package-lock.json`.
- Python غير مثبّت. PowerShell 5.1 و Git Bash متاحان.
- Docker: `Dockerfile` (مرحلة node تبني الأصول) و `docker-compose.yml`. ملف `docker/entrypoint.sh` يشغّل `migrate --force` تلقائياً إلا إذا كان `RUN_MIGRATIONS=false`.

## التقنيات

PHP 8.2، Laravel 12، Filament 5، spatie/laravel-permission 6، mallardduck/blade-lucide-icons (`<x-lucide-…>` و `'lucide-…'`)، Vite 8 + Tailwind 4، خط Readex Pro. الوسائط على Cloudinary (`docs/cloudinary-media.md`).

## البنية

التفاصيل في `docs/code-architecture.md`: Route → FormRequest → Controller → Service → Model.

| المكان | المحتوى |
|---|---|
| `app/Enums` | القيم الثابتة. أغلبها فيه `label()`، وبعضها `options()` و `labelFor()` |
| `app/Services` | منطق الأعمال والـ transactions. صفحات Filament تستدعيها في `handleRecordCreation/Update` |
| `app/Policies` | `ManageResourcePolicy` (أساس للموارد البسيطة، فيه `$permission`) + سياسات خاصة |
| `app/Support` | أدوات: `UniqueSlug`, `ImageUrl`, `PhoneNumber`, `ArticleContent`, `LocationOptions`, `MarketplaceLocation` |
| `app/Http/Middleware` | `SetLocale` (من الجلسة)، `TrackSiteVisit`، `MarkPersonalizedResponses`، `EnsureCommunityEnabled` |
| `app/Filament/Resources` | لوحة الإدارة `/admin` |
| `app/Filament/Seller` | لوحة البائع `/seller`: طيوره، طلباته، ملفه، الإحصائيات |
| `app/Filament/Shared` | مكونات مشتركة بين اللوحتين: `EntityActions` (حذف/استرجاع عبر `EntityLifecycleService`)، `UpdateOrderStatusAction`، `BirdForm`، `OrderInfolist`، `Columns` |
| `app/Filament/Support` | `SiteTheme` (ألوان وخط وشعار مشترك بين اللوحتين)، `InitialAvatarProvider` |
| `resources/views` | واجهة الموقع (Blade)، ومكونات `components/ui/*` |
| `resources/css`, `resources/js` | مداخل Vite: `app.css`, `fonts.css`, `panels.css` (للوحتين), `app.js` |
| `public/images/brand` | الشعار الكامل `logo.svg` والعلامة `mark.svg` (رأس الموقع والفوتر واللوحتين). أيقونات التطبيق والـ favicon في `public/images/icons` |
| `scripts/ui-check` | `npm run ui:audit / ui:baseline / ui:compare / ui:perf` على نسخة شغالة |

## الأدوار والصلاحيات

- الصلاحيات معرّفة في `App\Enums\Permission` (مع `label()` عربي). الكود يتحقق منها عبر `$user->can(...)` في الـ Policies و `User::canAccessPanel()`.
- الأدوار الأساسية في `App\Enums\UserRole`: `admin`, `seller`, `member`. الدالة `isBuiltIn()` تحدد إن كان الدور أساسياً.
- عمود `users.role` يُستخدم أيضاً في `isSeller()` و `isAdmin()`، و Spatie roles تحدد الصلاحيات. `SellerService` و `MemberService` يضبطان الاثنين معاً.
- `PermissionSeeder` ينشئ الصلاحيات ويوزّعها: المدير يأخذ كل شيء عدا `panels.seller.access`.
- صفحة **الأدوار** (`Resources/Roles`، والحفظ عبر `RoleService::save` الذي يستخدم `syncPermissions` لتفريغ الكاش):
  - دور المدير مقفل.
  - أسماء الأدوار الأساسية ثابتة.
  - الحذف مسموح للأدوار المضافة فقط، وبشرط ألا يحملها أي مستخدم.
  - القواعد في `RolePolicy`.
- صفحة **الصلاحيات** (`Resources/Permissions`) للعرض فقط.
- كلتا الصفحتين تتطلبان `roles.manage`. نماذج Spatie مسجّلة بـ `Gate::policy` في `AppServiceProvider`.

## لوحة الإدارة (Filament)

- **شكل كل مورد:** `XResource.php` + `Pages/` + `Schemas/XForm.php` + `Tables/XsTable.php`، وتُكتشف تلقائياً.
- **مجموعات القائمة:** `App\Filament\Navigation\AdminGroup`:
  - `Market`: الطيور، الطلبات، البائعون
  - `Content`: المقالات، أقسامها، استفسارات المجتمع
  - `Setup`: السلالات، الدول، المناطق، إعدادات الموقع (4)، الأدوار (5)، الصلاحيات (6)
- **النصوص:** تُكتب `__('نص عربي')`، وترجمتها الإنجليزية في `lang/en.json`. تأكد من عدم تكرار المفاتيح.
- **الإعدادات:** جدول `settings` (key/value).
  - المفاتيح في `App\Enums\SettingKey` مع `label()` و `description()`.
  - القراءة بـ `Setting::boolean(SettingKey::X->value)`. القيم كلها تُجلب باستعلام واحد لكل طلب (scoped binding باسم `Setting::VALUES`)، وتُمسح عند حفظ أي إعداد.
  - لا يمكن إضافة إعداد من اللوحة (`canCreate=false`).
- **صفحة "البائعون"** (`Resources/Users`): تعرض مستخدمي دور seller فقط، وفيها index فقط.

## واجهة الموقع

- **الترجمة:** نصوص الموقع في `lang/ar/ui.php` و `lang/en/ui.php` بنفس المفاتيح، وتُستخدم `__('ui.section.key')`. رسائل التحقق في `lang/ar/validation.php`. تبديل اللغة عبر `/locale/{ar|en}`.
- **المسارات** (`routes/web.php`): الرئيسية، `/birds`، الطلبات وتتبعها، `/guide`، `/community` (محمي بـ `EnsureCommunityEnabled`)، `/account`، `/members/{user}`، `/sellers/{seller}`، تسجيل البائع `/seller/register`، المفضلة، PWA (`manifest` و `offline`). صفحة `/_ui` معرض مكونات في البيئة المحلية فقط.
- **تسجيل الدخول:** صفحة واحدة `/login` للموقع واللوحتين (`AuthController`، و `SiteLogin` في Filament).

## الاختبارات

`tests/Feature/*` تستخدم `RefreshDatabase` و `$this->seed(PermissionSeeder::class)`. المستخدمون يُنشؤون بـ `User::factory()` ثم `syncRoles`. اختبارات Filament تستخدم `Livewire::test` بعد `Filament::setCurrentPanel`. **لا تشغّل الاختبارات بدون إذن.**

## نواقص معروفة

- لا توجد صفحة لكل المستخدمين، فلا يمكن إعطاء الأدوار المضافة لمستخدمين من اللوحة.
- `Resources/Users/Schemas/UserForm.php` و `Pages/CreateUser|EditUser` و `Settings/Pages/CreateSetting` غير مسجّلة في `getPages()`، وهي بقايا قديمة.
