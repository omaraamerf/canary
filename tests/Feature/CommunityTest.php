<?php

namespace Tests\Feature;

use App\Enums\PostCategory;
use App\Enums\PostStatus;
use App\Enums\SettingKey;
use App\Enums\UserRole;
use App\Enums\UserStatus;
use App\Models\Comment;
use App\Models\Post;
use App\Models\Setting;
use App\Models\User;
use Database\Seeders\PermissionSeeder;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class CommunityTest extends TestCase
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

    public function test_guests_can_browse_published_posts_but_not_hidden_ones(): void
    {
        $published = $this->makePost(['title' => 'Canary is fluffed up']);
        $hidden = $this->makePost(['title' => 'Hidden question', 'status' => PostStatus::Hidden->value]);

        $this->get(route('community.index'))
            ->assertOk()
            ->assertSee('Canary is fluffed up')
            ->assertDontSee('Hidden question');

        $this->get(route('community.show', $published))->assertOk()->assertSee(__('ui.community.login_to_comment'));
        $this->get(route('community.show', $hidden))->assertNotFound();
        $this->actingAs($hidden->user)->get(route('community.show', $hidden))->assertOk();
    }

    public function test_guests_are_sent_to_login_before_posting_or_commenting(): void
    {
        $post = $this->makePost();

        $this->get(route('community.create'))->assertRedirect(route('login'));
        $this->post(route('community.store'), [])->assertRedirect(route('login'));
        $this->post(route('community.comments.store', $post), ['body' => 'Hello'])->assertRedirect(route('login'));
    }

    public function test_visitor_can_register_as_member_and_is_logged_in(): void
    {
        $this->post(route('register.store'), [
            'name' => 'Omar',
            'email' => 'omar@example.com',
            'password' => 'secret-password',
            'password_confirmation' => 'secret-password',
        ])->assertRedirect(route('community.index'));

        $user = User::where('email', 'omar@example.com')->firstOrFail();
        $this->assertAuthenticatedAs($user);
        $this->assertSame(UserRole::Member->value, $user->role);
        $this->assertTrue($user->hasRole(UserRole::Member->value));
        $this->assertFalse($user->canAccessPanel(Filament::getPanel('admin')));
        $this->assertFalse($user->canAccessPanel(Filament::getPanel('seller')));
    }

    public function test_member_can_log_in_and_is_redirected_to_intended_page(): void
    {
        $member = $this->user(UserRole::Member);

        $this->get(route('community.create'))->assertRedirect(route('login'));
        $this->post(route('login.store'), ['email' => $member->email, 'password' => 'password'])
            ->assertRedirect(route('community.create'));
        $this->assertAuthenticatedAs($member);
    }

    public function test_member_can_publish_a_post(): void
    {
        $member = $this->user(UserRole::Member);

        $response = $this->actingAs($member)->post(route('community.store'), [
            'category' => PostCategory::Health->value,
            'title' => 'Canary not eating for two days',
            'body' => 'He is fluffed up and sleeping a lot during the day.',
        ]);

        $post = Post::firstOrFail();
        $response->assertRedirect(route('community.show', $post));
        $this->assertSame($member->id, $post->user_id);
        $this->assertSame(PostStatus::Published, $post->status);
        $this->assertSame(PostCategory::Health, $post->category);
    }

    public function test_seller_comment_is_highlighted_with_a_badge(): void
    {
        $post = $this->makePost();
        $seller = $this->user(UserRole::Seller);
        $member = $this->user(UserRole::Member);

        $this->actingAs($seller)->post(route('community.comments.store', $post), ['body' => 'Try vitamins in the water.'])
            ->assertRedirect();
        $this->actingAs($member)->post(route('community.comments.store', $post), ['body' => 'Same happened to mine.'])
            ->assertRedirect();

        $this->assertSame(2, $post->comments()->count());
        $this->assertSame('post', Comment::firstOrFail()->commentable_type);

        $this->get(route('community.show', $post))
            ->assertOk()
            ->assertSee('comment-staff', false)
            ->assertSee(__('ui.community.seller_badge'));
    }

    public function test_only_the_author_can_accept_a_comment(): void
    {
        $post = $this->makePost();
        $comment = $post->comments()->create(['user_id' => $this->user(UserRole::Seller)->id, 'body' => 'Isolate the bird.']);
        $stranger = $this->user(UserRole::Member);

        $this->actingAs($stranger)->post(route('community.comments.accept', [$post, $comment]))->assertForbidden();
        $this->assertNull($post->fresh()->accepted_comment_id);

        $this->actingAs($post->user)->post(route('community.comments.accept', [$post, $comment]))->assertRedirect();
        $this->assertSame($comment->id, $post->fresh()->accepted_comment_id);

        $this->actingAs($post->user)->post(route('community.comments.accept', [$post, $comment]));
        $this->assertNull($post->fresh()->accepted_comment_id);
    }

    public function test_comment_from_another_post_cannot_be_accepted(): void
    {
        $post = $this->makePost();
        $other = $this->makePost();
        $comment = $other->comments()->create(['user_id' => $other->user_id, 'body' => 'Unrelated.']);

        $this->actingAs($post->user)->post(route('community.comments.accept', [$post, $comment]))->assertNotFound();
    }

    public function test_members_can_delete_only_their_own_posts_and_comments(): void
    {
        $post = $this->makePost();
        $stranger = $this->user(UserRole::Member);
        $comment = $post->comments()->create(['user_id' => $stranger->id, 'body' => 'My reply.']);

        $this->actingAs($stranger)->delete(route('community.destroy', $post))->assertForbidden();
        $this->actingAs($post->user)->delete(route('community.comments.destroy', $comment))->assertForbidden();

        $this->actingAs($stranger)->delete(route('community.comments.destroy', $comment))->assertRedirect();
        $this->assertModelMissing($comment);

        $this->actingAs($post->user)->delete(route('community.destroy', $post))->assertRedirect(route('community.index'));
        $this->assertSoftDeleted($post);
    }

    public function test_hidden_posts_do_not_accept_comments(): void
    {
        $post = $this->makePost(['status' => PostStatus::Hidden->value]);

        $this->actingAs($this->user(UserRole::Member))
            ->post(route('community.comments.store', $post), ['body' => 'Hello there'])
            ->assertNotFound();
    }

    public function test_admin_moderator_can_delete_any_post(): void
    {
        $post = $this->makePost();

        $this->actingAs($this->user(UserRole::Admin))->delete(route('community.destroy', $post))->assertRedirect();
        $this->assertSoftDeleted($post);
    }

    public function test_community_can_be_disabled(): void
    {
        Setting::put(SettingKey::CommunityEnabled->value, '0');

        $this->get(route('community.index'))->assertNotFound();
    }

    private function user(UserRole $role): User
    {
        $user = User::factory()->create([
            'role' => $role->value,
            'status' => UserStatus::Active->value,
        ]);
        $user->assignRole($role->value);

        return $user;
    }

    private function makePost(array $attributes = []): Post
    {
        static $sequence = 0;
        $sequence++;

        return Post::create([
            'user_id' => $this->user(UserRole::Member)->id,
            'category' => PostCategory::Health->value,
            'title' => 'Question '.$sequence,
            'slug' => 'question-'.$sequence.'-'.uniqid(),
            'body' => 'Details about the sick bird.',
            'status' => PostStatus::Published->value,
            ...$attributes,
        ]);
    }
}
