<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Enums\UserStatus;
use App\Models\User;
use Database\Seeders\PermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class ArabicLocalizationTest extends TestCase
{
    use RefreshDatabase;

    protected function beforeRefreshingDatabase(): void
    {
        config(['database.default' => 'sqlite', 'database.connections.sqlite.database' => ':memory:']);
        DB::purge('sqlite');
    }

    protected function setUp(): void
    {
        parent::setUp();

        app()->setLocale('ar');
        $this->seed(PermissionSeeder::class);
    }

    public function test_validation_messages_and_attributes_are_arabic(): void
    {
        $member = User::factory()->create(['role' => UserRole::Member->value, 'status' => UserStatus::Active->value]);
        $member->assignRole(UserRole::Member->value);

        $this->actingAs($member)
            ->put(route('account.password'), ['current_password' => 'wrong', 'password' => 'new-secret-123', 'password_confirmation' => 'new-secret-123'])
            ->assertSessionHasErrorsIn('password', ['current_password' => 'كلمة المرور الحالية غير صحيحة.']);

        $this->actingAs($member)
            ->post(route('community.store'), ['category' => 'unknown', 'title' => 'سؤال', 'body' => ''])
            ->assertSessionHasErrors([
                'category' => 'القيمة المختارة في نوع الاستفسار غير صالحة.',
                'body' => 'حقل التفاصيل مطلوب.',
            ]);
    }

    public function test_error_pages_are_arabic(): void
    {
        $this->get('/this-page-does-not-exist')
            ->assertNotFound()
            ->assertSee('الصفحة غير موجودة')
            ->assertSee('dir="rtl"', false);
    }

    public function test_seller_menu_profile_label_is_translated(): void
    {
        $this->assertSame('الملف الشخصي', __('ui.account.profile'));
        $this->assertSame('حسابي', __('ui.account.my_account'));
    }
}
