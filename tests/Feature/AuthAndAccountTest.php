<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Enums\UserStatus;
use App\Models\Bird;
use App\Models\Breed;
use App\Models\Order;
use App\Models\Region;
use App\Models\SellerProfile;
use App\Models\User;
use Database\Seeders\PermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class AuthAndAccountTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(PermissionSeeder::class);
    }

    public function test_panel_login_pages_forward_to_the_site_login(): void
    {
        $this->get('/seller/login')->assertRedirect(route('login'));
        $this->get('/admin/login')->assertRedirect(route('login'));
    }

    public function test_seller_signing_in_from_the_site_lands_in_the_seller_panel(): void
    {
        $seller = $this->seller();

        $this->post(route('login.store'), ['email' => $seller->email, 'password' => 'password'])
            ->assertRedirect(url('/seller'));
    }

    public function test_seller_sent_from_a_panel_page_returns_to_it(): void
    {
        $seller = $this->seller();

        $this->get('/seller/birds')->assertRedirect();
        $this->post(route('login.store'), ['email' => $seller->email, 'password' => 'password'])
            ->assertRedirect(url('/seller/birds'));
    }

    public function test_member_goes_back_to_the_page_they_came_from(): void
    {
        $member = $this->user(UserRole::Member);

        $this->get(route('login'), ['referer' => url('/birds?sex=female')])->assertOk();
        $this->post(route('login.store'), ['email' => $member->email, 'password' => 'password'])
            ->assertRedirect('/birds?sex=female');
    }

    public function test_member_sent_from_the_admin_panel_is_not_returned_to_it(): void
    {
        $member = $this->user(UserRole::Member);

        $this->get('/admin');
        $this->post(route('login.store'), ['email' => $member->email, 'password' => 'password'])
            ->assertRedirect(route('community.index'));
    }

    public function test_return_page_must_be_on_this_site(): void
    {
        $member = $this->user(UserRole::Member);

        $this->get(route('login'), ['referer' => 'https://example.com/phish'])->assertOk();
        $this->post(route('login.store'), ['email' => $member->email, 'password' => 'password'])
            ->assertRedirect(route('community.index'));
    }

    public function test_a_pending_account_is_told_it_is_under_review_only_with_the_right_password(): void
    {
        $pending = $this->user(UserRole::Seller, UserStatus::Pending);

        $this->from(route('login'))->post(route('login.store'), ['email' => $pending->email, 'password' => 'password'])
            ->assertSessionHasErrors(['email' => __('ui.auth.pending')]);
        $this->from(route('login'))->post(route('login.store'), ['email' => $pending->email, 'password' => 'wrong-password'])
            ->assertSessionHasErrors(['email' => __('ui.auth.failed')]);
        $this->assertGuest();
    }

    public function test_seller_registration_uses_the_site_layout_and_sends_pending_sellers_to_login(): void
    {
        $this->get(route('seller.register'))
            ->assertOk()
            ->assertDontSee('admin-assets', false)
            ->assertSee('class="site-header"', false);

        $region = Region::query()->firstOrFail();

        $this->post(route('seller.register.store'), [
            'name' => 'New seller', 'display_name' => 'New farm', 'phone' => '0999000111',
            'email' => 'new-seller@example.test', 'country_id' => $region->country_id, 'region_id' => $region->id,
            'password' => 'password123', 'password_confirmation' => 'password123',
        ])->assertRedirect(route('login'))->assertSessionHas('status');
    }

    public function test_orders_placed_while_signed_in_show_under_my_orders_and_stay_trackable(): void
    {
        $member = $this->user(UserRole::Member);
        $bird = $this->bird();
        $region = Region::query()->firstOrFail();

        $this->actingAs($member)->post(route('orders.store', $bird), [
            'buyer_name' => 'Buyer', 'phone' => '0999111222', 'buyer_region_id' => $region->id,
        ])->assertRedirect();

        $order = Order::query()->sole();
        $this->assertSame($member->id, $order->user_id);

        $this->actingAs($member)->get(route('account.show', ['tab' => 'orders']))
            ->assertOk()
            ->assertSee($order->reference)
            ->assertSee($bird->title);

        // A fresh session of the owner may track it; another account may not.
        $this->flushSession();
        $this->actingAs($member)->get(route('orders.track.show', $order))->assertOk();
        $this->flushSession();
        $this->actingAs($this->user(UserRole::Member))->get(route('orders.track.show', $order))->assertForbidden();
    }

    public function test_account_tabs_and_settings_page(): void
    {
        $member = $this->user(UserRole::Member);

        $this->actingAs($member)->get(route('account.show', ['tab' => 'replies']))
            ->assertOk()
            ->assertSee('aria-current="page"', false)
            ->assertSee(__('ui.account.no_replies'));

        $this->actingAs($member)->get(route('account.show', ['tab' => ['x']]))->assertOk();

        $this->actingAs($member)->get(route('account.edit'))
            ->assertOk()
            ->assertSee('<h1>'.__('ui.account.settings_title').'</h1>', false);
    }

    public function test_missing_page_offers_a_bird_search(): void
    {
        $this->get('/no-such-page')->assertNotFound()->assertSee('name="q"', false);
    }

    private function user(UserRole $role, UserStatus $status = UserStatus::Active): User
    {
        $user = User::factory()->create(['role' => $role->value, 'status' => $status->value, 'password' => 'password']);
        $user->syncRoles([$role->value]);

        return $user;
    }

    private function seller(): User
    {
        $seller = $this->user(UserRole::Seller);
        $region = Region::query()->firstOrFail();
        SellerProfile::create(['user_id' => $seller->id, 'display_name' => 'Test farm', 'region_id' => $region->id, 'approval_status' => 'approved']);

        return $seller;
    }

    private function bird(): Bird
    {
        $seller = $this->seller();
        $region = Region::query()->firstOrFail();

        return Bird::create([
            'seller_id' => $seller->id,
            'breed_id' => Breed::create(['name' => 'Test breed', 'slug' => Str::random(8), 'active' => true])->id,
            'title' => 'Order test bird', 'slug' => Str::random(10), 'sex' => 'male', 'color' => 'Yellow',
            'price' => 100, 'currency' => 'USD', 'city' => $region->name, 'region_id' => $region->id,
            'delivery_type' => 'pickup', 'status' => 'available', 'approval_status' => 'approved', 'published_at' => now(),
        ]);
    }
}
