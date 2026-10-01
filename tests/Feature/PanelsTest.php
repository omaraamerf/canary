<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Enums\UserStatus;
use App\Filament\Resources\Birds\BirdResource;
use App\Filament\Resources\Birds\Pages\ListBirds;
use App\Filament\Seller\Resources\Birds\Pages\CreateBird;
use App\Filament\Seller\Widgets\ListingPerformance;
use App\Filament\Support\InitialAvatarProvider;
use App\Filament\Widgets\NeedsAttention;
use App\Models\Bird;
use App\Models\Breed;
use App\Models\Region;
use App\Models\SellerProfile;
use App\Models\User;
use Database\Seeders\PermissionSeeder;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Livewire\Livewire;
use Tests\TestCase;

class PanelsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(PermissionSeeder::class);
    }

    public function test_admin_dashboard_lists_what_is_waiting(): void
    {
        $this->bird(['approval_status' => 'pending']);
        $this->actingAs($this->user(UserRole::Admin));
        Filament::setCurrentPanel(Filament::getPanel('admin'));

        $this->get('/admin')->assertOk();
        Livewire::test(NeedsAttention::class)
            ->assertSee(__('طيور بانتظار المراجعة'))
            ->assertSee(BirdResource::getUrl('index', ['filters' => ['approval_status' => ['value' => 'pending']]]));
        $this->assertSame('1', BirdResource::getNavigationBadge());
    }

    public function test_bird_table_shows_local_images_by_absolute_url(): void
    {
        $this->bird();
        $this->actingAs($this->user(UserRole::Admin));
        Filament::setCurrentPanel(Filament::getPanel('admin'));

        // A site-relative image path must not be read as a file on the storage disk.
        Livewire::test(ListBirds::class)
            ->assertSee(url('/images/birds/yellow-canary.jpg'), false)
            ->assertDontSee('/storage//images', false);
    }

    public function test_seller_adds_a_bird_one_step_at_a_time(): void
    {
        $seller = $this->bird()->seller;
        $this->actingAs($seller);
        Filament::setCurrentPanel(Filament::getPanel('seller'));

        Livewire::test(CreateBird::class)
            ->assertSee(__('السعر والتسليم'))
            ->assertSee(__('المراجعة'))
            ->goToNextWizardStep()
            ->assertHasFormErrors(['title', 'breed_id', 'sex', 'color']);
    }

    public function test_seller_sees_how_each_listing_performs(): void
    {
        // views_count is not mass assignable: only page views raise it.
        $bird = $this->bird(['title' => 'Most viewed bird']);
        $bird->forceFill(['views_count' => 42])->save();
        $this->actingAs($bird->seller);
        Filament::setCurrentPanel(Filament::getPanel('seller'));

        Livewire::test(ListingPerformance::class)->assertSee('Most viewed bird')->assertSee('42');
    }

    public function test_a_listing_view_counts_once_per_visitor_and_not_for_its_seller(): void
    {
        $bird = $this->bird();

        $this->get(route('birds.show', $bird))->assertOk();
        $this->get(route('birds.show', $bird))->assertOk();
        $this->assertSame(1, $bird->fresh()->views_count);

        $this->actingAs($bird->seller)->get(route('birds.show', $bird))->assertOk();
        $this->assertSame(1, $bird->fresh()->views_count);
    }

    public function test_panel_avatars_are_drawn_locally(): void
    {
        $avatar = app(InitialAvatarProvider::class)->get($this->user(UserRole::Member));

        $this->assertStringStartsWith('data:image/svg+xml;base64,', $avatar);
        $this->assertStringNotContainsString('ui-avatars.com', $avatar);
    }

    private function user(UserRole $role): User
    {
        $user = User::factory()->create(['role' => $role->value, 'status' => UserStatus::Active->value]);
        $user->syncRoles([$role->value]);

        return $user;
    }

    private function bird(array $attributes = []): Bird
    {
        $region = Region::query()->firstOrFail();
        $seller = $this->user(UserRole::Seller);
        SellerProfile::create(['user_id' => $seller->id, 'display_name' => 'Test farm', 'region_id' => $region->id, 'approval_status' => 'approved']);

        return Bird::create([
            'seller_id' => $seller->id,
            'breed_id' => Breed::create(['name' => 'Test breed', 'slug' => Str::random(8), 'active' => true])->id,
            'title' => 'Panel test bird', 'slug' => Str::random(10), 'sex' => 'male', 'color' => 'Yellow',
            'price' => 100, 'currency' => 'USD', 'city' => $region->name, 'region_id' => $region->id,
            'delivery_type' => 'pickup', 'status' => 'available', 'approval_status' => 'approved', 'published_at' => now(),
            ...$attributes,
        ]);
    }
}
