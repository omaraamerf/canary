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

class MarketplaceCatalogTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(PermissionSeeder::class);
    }

    public function test_price_range_applies_within_the_chosen_currency(): void
    {
        $this->makeBird(['title' => 'Dollar bird', 'price' => 300, 'currency' => 'USD']);
        $this->makeBird(['title' => 'Pound bird', 'price' => 900000, 'currency' => 'SYP']);

        $this->get(route('birds.index', ['currency' => 'SYP', 'min_price' => 500000]))
            ->assertOk()
            ->assertSee('Pound bird')
            ->assertDontSee('Dollar bird');

        // Without a currency the range would compare dollars with pounds, so it is ignored.
        $this->get(route('birds.index', ['min_price' => 500000]))
            ->assertOk()
            ->assertSee('Pound bird')
            ->assertSee('Dollar bird');
    }

    public function test_unknown_currency_filter_is_ignored(): void
    {
        $this->makeBird(['title' => 'Dollar bird', 'currency' => 'USD']);

        $this->get(route('birds.index', ['currency' => 'EUR']))->assertOk()->assertSee('Dollar bird');
    }

    public function test_listing_is_priced_in_its_own_currency(): void
    {
        $bird = $this->makeBird(['price' => 1500000, 'currency' => 'SYP']);

        $this->get(route('birds.show', $bird))
            ->assertOk()
            ->assertSee('1,500,000')
            ->assertSee(__('ui.currencies.SYP'));
    }

    public function test_whatsapp_button_appears_only_when_the_seller_publishes_a_number(): void
    {
        $bird = $this->makeBird();

        $this->get(route('birds.show', $bird))->assertOk()->assertDontSee('https://wa.me/', false);
        $this->get(route('sellers.show', $bird->seller))->assertOk()->assertDontSee('https://wa.me/', false);

        $bird->seller->sellerProfile->update(['whatsapp' => '0999 123 456']);

        $this->get(route('birds.show', $bird))->assertOk()->assertSee('https://wa.me/963999123456', false);
        $this->get(route('sellers.show', $bird->seller))->assertOk()->assertSee('https://wa.me/963999123456', false);
    }

    private function makeBird(array $attributes = []): Bird
    {
        $region = Region::whereHas('country', fn ($country) => $country->where('code', 'SY'))->firstOrFail();
        $seller = User::create([
            'name' => 'Seller',
            'email' => Str::random(8).'@example.test',
            'password' => 'password',
            'role' => UserRole::Seller->value,
            'status' => UserStatus::Active->value,
            'country_id' => $region->country_id,
            'region_id' => $region->id,
        ]);
        $seller->syncRoles([UserRole::Seller->value]);
        SellerProfile::create(['user_id' => $seller->id, 'display_name' => 'Test farm', 'region_id' => $region->id, 'approval_status' => 'approved']);
        $breed = Breed::create(['name' => 'Test breed', 'slug' => Str::random(8), 'active' => true]);

        return Bird::create([
            'seller_id' => $seller->id,
            'breed_id' => $breed->id,
            'title' => 'Test bird',
            'slug' => Str::random(10),
            'sex' => 'male',
            'color' => 'Yellow',
            'price' => 100,
            'currency' => 'USD',
            'city' => $region->name,
            'region_id' => $region->id,
            'delivery_type' => 'pickup',
            'status' => 'available',
            'approval_status' => 'approved',
            'published_at' => now(),
            ...$attributes,
        ]);
    }
}
