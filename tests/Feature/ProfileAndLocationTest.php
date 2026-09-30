<?php

namespace Tests\Feature;

use App\Enums\PostCategory;
use App\Enums\PostStatus;
use App\Enums\UserRole;
use App\Enums\UserStatus;
use App\Models\Country;
use App\Models\Post;
use App\Models\Region;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Database\Seeders\LocationSeeder;
use Database\Seeders\PermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class ProfileAndLocationTest extends TestCase
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

        $this->seed(PermissionSeeder::class);
    }

    public function test_countries_are_seeded_with_their_regions(): void
    {
        $syria = Country::where('code', 'SY')->firstOrFail();
        $saudi = Country::where('code', 'SA')->firstOrFail();

        $this->assertTrue($syria->regions()->where('name', 'دمشق')->exists());
        $this->assertSame(13, $saudi->regions()->count());
        $this->assertSame(58, Country::where('code', 'DZ')->firstOrFail()->regions()->count());
    }

    public function test_location_seeder_includes_syria_and_can_run_repeatedly(): void
    {
        $this->seed(DatabaseSeeder::class);
        $this->seed(LocationSeeder::class);

        $syria = Country::where('code', 'SY')->firstOrFail();

        $this->assertSame(14, $syria->regions()->count());
        $this->assertTrue($syria->regions()->where('slug', 'damascus')->where('name', 'دمشق')->exists());
        $this->assertSame(0, Region::whereNull('country_id')->count());
        $this->assertSame(1, Country::where('code', 'SY')->count());
        $this->assertSame(Region::count(), Region::distinct()->count('slug'));
    }

    public function test_forms_with_location_and_image_pickers_render(): void
    {
        $this->get(route('register'))->assertOk()->assertSee('data-location-picker', false);
        $this->get(route('birds.index'))->assertOk()->assertSee('data-location-picker', false);
        $this->get(route('seller.register'))->assertOk()->assertSee('location-data', false);

        $member = $this->member();
        $this->actingAs($member)->get(route('community.create'))->assertOk()->assertSee('data-image-picker', false);
        $this->actingAs($member)->get(route('account.edit'))->assertOk()->assertSee('name="avatar"', false);
    }

    public function test_account_pages_require_login(): void
    {
        $this->get(route('account.show'))->assertRedirect(route('login'));
        $this->get(route('account.edit'))->assertRedirect(route('login'));
    }

    public function test_member_sees_their_account_with_posts_and_stats(): void
    {
        $member = $this->member();
        $this->post_for($member, 'My sick canary');

        $this->actingAs($member)->get(route('account.show'))
            ->assertOk()
            ->assertSee('My sick canary')
            ->assertSee(__('ui.account.stats_solutions'));
    }

    public function test_member_can_update_profile_and_location(): void
    {
        $member = $this->member();
        $jordan = Country::where('code', 'JO')->firstOrFail();
        $amman = $jordan->regions()->where('name', 'عمّان')->firstOrFail();

        $this->actingAs($member)->put(route('account.update'), [
            'name' => 'Omar Breeder',
            'email' => $member->email,
            'bio' => 'Gloster breeder',
            'country_id' => $jordan->id,
            'region_id' => $amman->id,
        ])->assertRedirect(route('account.show'));

        $member->refresh();
        $this->assertSame('Omar Breeder', $member->name);
        $this->assertSame('Gloster breeder', $member->bio);
        $this->assertSame($jordan->id, $member->country_id);
        $this->assertSame($amman->id, $member->region_id);
    }

    public function test_region_must_belong_to_the_selected_country(): void
    {
        $member = $this->member();
        $jordan = Country::where('code', 'JO')->firstOrFail();
        $damascus = Country::where('code', 'SY')->firstOrFail()->regions()->firstOrFail();

        $this->actingAs($member)->put(route('account.update'), [
            'name' => $member->name,
            'email' => $member->email,
            'country_id' => $jordan->id,
            'region_id' => $damascus->id,
        ])->assertSessionHasErrors('region_id');
    }

    public function test_country_is_optional_and_can_be_cleared(): void
    {
        $member = $this->member();
        $member->update(['country_id' => Country::where('code', 'SY')->value('id')]);

        $this->actingAs($member)->put(route('account.update'), [
            'name' => $member->name,
            'email' => $member->email,
        ])->assertSessionHasNoErrors();

        $this->assertNull($member->fresh()->country_id);
    }

    public function test_password_change_requires_the_current_password(): void
    {
        $member = $this->member();

        $this->actingAs($member)->put(route('account.password'), [
            'current_password' => 'wrong-password',
            'password' => 'new-secret-123',
            'password_confirmation' => 'new-secret-123',
        ])->assertSessionHasErrorsIn('password', 'current_password');

        $this->actingAs($member)->put(route('account.password'), [
            'current_password' => 'password',
            'password' => 'new-secret-123',
            'password_confirmation' => 'new-secret-123',
        ])->assertRedirect(route('account.edit'));

        $this->assertTrue(Hash::check('new-secret-123', $member->fresh()->password));
    }

    public function test_public_member_profile_lists_published_posts_only(): void
    {
        $member = $this->member();
        $this->post_for($member, 'Visible question');
        $this->post_for($member, 'Hidden question', PostStatus::Hidden);

        $this->get(route('members.show', $member))
            ->assertOk()
            ->assertSee('Visible question')
            ->assertDontSee('Hidden question');

        $member->update(['status' => UserStatus::Suspended->value]);
        $this->get(route('members.show', $member))->assertNotFound();
    }

    public function test_post_region_fills_in_its_country(): void
    {
        $member = $this->member();
        $cairo = Country::where('code', 'EG')->firstOrFail()->regions()->where('name', 'القاهرة')->firstOrFail();

        $this->actingAs($member)->post(route('community.store'), [
            'category' => PostCategory::Health->value,
            'title' => 'Bird sneezing a lot',
            'body' => 'Started three days ago after cleaning the cage.',
            'region_id' => $cairo->id,
        ])->assertRedirect();

        $post = Post::firstOrFail();
        $this->assertSame($cairo->id, $post->region_id);
        $this->assertSame($cairo->country_id, $post->country_id);
    }

    public function test_marketplace_location_can_be_a_country_a_region_or_everywhere(): void
    {
        $saudi = Country::where('code', 'SA')->firstOrFail();
        $riyadh = $saudi->regions()->where('name', 'الرياض')->firstOrFail();

        $this->post(route('region.select'), ['country_id' => $saudi->id])
            ->assertSessionHas('marketplace_country_id', $saudi->id);
        $this->assertNull(session('marketplace_region_id'));

        $this->post(route('region.select'), ['country_id' => $saudi->id, 'region_id' => $riyadh->id])
            ->assertSessionHas('marketplace_country_id', $saudi->id)
            ->assertSessionHas('marketplace_region_id', $riyadh->id);

        $this->post(route('region.select'), ['country_id' => $saudi->id, 'region_id' => $riyadh->id, 'scope' => 'all'])
            ->assertSessionHas('marketplace_region_chosen', true);
        $this->assertNull(session('marketplace_country_id'));
        $this->assertNull(session('marketplace_region_id'));

        $this->get(route('birds.index'))->assertOk();
    }

    private function member(): User
    {
        $user = User::factory()->create(['role' => UserRole::Member->value, 'status' => UserStatus::Active->value]);
        $user->assignRole(UserRole::Member->value);

        return $user;
    }

    private function post_for(User $user, string $title, PostStatus $status = PostStatus::Published): Post
    {
        return Post::create([
            'user_id' => $user->id,
            'category' => PostCategory::General->value,
            'title' => $title,
            'slug' => str()->slug($title).'-'.uniqid(),
            'body' => 'Some details about the bird.',
            'status' => $status->value,
        ]);
    }
}
