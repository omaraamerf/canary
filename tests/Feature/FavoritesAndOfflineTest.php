<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Enums\UserStatus;
use App\Models\Bird;
use App\Models\Breed;
use App\Models\Region;
use App\Models\SellerProfile;
use App\Models\User;
use Database\Seeders\PermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class FavoritesAndOfflineTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(PermissionSeeder::class);
    }

    public function test_favourite_cards_show_only_birds_still_on_the_site_most_recent_first(): void
    {
        $older = $this->bird(['title' => 'Saved first']);
        $newer = $this->bird(['title' => 'Saved last']);
        $hidden = $this->bird(['title' => 'Under review', 'approval_status' => 'pending']);

        $html = $this->get(route('favorites.cards', ['ids' => "{$older->id},{$newer->id},{$hidden->id},999,abc"]))
            ->assertOk()
            ->assertSee('Saved first')
            ->assertDontSee('Under review')
            ->getContent();

        $this->assertLessThan(strpos($html, 'Saved first'), strpos($html, 'Saved last'));
    }

    public function test_favourites_page_and_empty_list_render(): void
    {
        $this->get(route('favorites.index'))->assertOk()->assertSee('data-favorites-list', false);
        $this->get(route('favorites.cards'))->assertOk()->assertDontSee('bird-card', false);
    }

    public function test_manifest_points_at_icons_that_exist(): void
    {
        $manifest = $this->get(route('manifest'))
            ->assertOk()
            ->assertHeader('Content-Type', 'application/manifest+json')
            ->json();

        $this->assertSame('standalone', $manifest['display']);
        $this->assertNotEmpty($manifest['shortcuts']);

        foreach ($manifest['icons'] as $icon) {
            $this->assertFileExists(public_path($icon['src']));
        }
    }

    public function test_offline_page_and_service_worker_are_in_place(): void
    {
        $this->get(route('offline'))->assertOk()->assertSee(__('ui.pwa.offline_title'));
        $this->assertFileExists(public_path('sw.js'));
        $this->assertStringContainsString("const OFFLINE_URL = '/offline';", file_get_contents(public_path('sw.js')));
    }

    public function test_pages_rendered_for_a_signed_in_visitor_are_not_saved_offline(): void
    {
        $this->get(route('about'))->assertOk()->assertHeaderMissing('X-Personalized');

        $member = User::factory()->create(['role' => UserRole::Member->value, 'status' => UserStatus::Active->value]);
        $this->actingAs($member)->get(route('about'))->assertOk()->assertHeader('X-Personalized', '1');
    }

    private function bird(array $attributes = []): Bird
    {
        $region = Region::query()->firstOrFail();
        $seller = User::factory()->create(['role' => UserRole::Seller->value, 'status' => UserStatus::Active->value]);
        $seller->syncRoles([UserRole::Seller->value]);
        SellerProfile::create(['user_id' => $seller->id, 'display_name' => 'Test farm', 'region_id' => $region->id, 'approval_status' => 'approved']);

        return Bird::create([
            'seller_id' => $seller->id,
            'breed_id' => Breed::create(['name' => 'Test breed', 'slug' => Str::random(8), 'active' => true])->id,
            'title' => 'Favourite bird', 'slug' => Str::random(10), 'sex' => 'male', 'color' => 'Yellow',
            'price' => 100, 'currency' => 'USD', 'city' => $region->name, 'region_id' => $region->id,
            'delivery_type' => 'pickup', 'status' => 'available', 'approval_status' => 'approved', 'published_at' => now(),
            ...$attributes,
        ]);
    }
}
