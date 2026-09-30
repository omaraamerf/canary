<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('posts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('breed_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('region_id')->nullable()->constrained()->nullOnDelete();
            $table->string('category');
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('body');
            $table->string('status')->default('published');
            $table->unsignedBigInteger('accepted_comment_id')->nullable()->index();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['status', 'created_at']);
            $table->index(['category', 'status']);
        });

        Schema::create('comments', function (Blueprint $table) {
            $table->id();
            $table->morphs('commentable');
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->text('body');
            $table->boolean('is_hidden')->default(false);
            $table->timestamps();
        });

        Schema::create('media', function (Blueprint $table) {
            $table->id();
            $table->morphs('mediable');
            $table->string('type');
            $table->string('provider')->nullable();
            $table->string('public_id')->nullable()->index();
            $table->text('url');
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });

        DB::table('settings')->updateOrInsert(
            ['key' => 'community_enabled'],
            ['value' => '1', 'created_at' => now(), 'updated_at' => now()]
        );

        app(PermissionRegistrar::class)->forgetCachedPermissions();
        Role::findOrCreate('member', 'web');
        $manageCommunity = Permission::findOrCreate('community.manage', 'web');
        Role::query()->where('name', 'admin')->where('guard_name', 'web')->first()?->givePermissionTo($manageCommunity);
        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }

    public function down(): void
    {
        Schema::dropIfExists('media');
        Schema::dropIfExists('comments');
        Schema::dropIfExists('posts');

        DB::table('settings')->where('key', 'community_enabled')->delete();
        Permission::query()->where('name', 'community.manage')->delete();
        Role::query()->where('name', 'member')->delete();
        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }
};
