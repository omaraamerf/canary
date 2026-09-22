<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('regions', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->foreignId('parent_id')->nullable()->constrained('regions')->nullOnDelete();
            $table->boolean('active')->default(true);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::table('users', function (Blueprint $table) {
            $table->string('phone', 30)->nullable()->after('name');
            $table->foreignId('region_id')->nullable()->after('status')->constrained()->nullOnDelete();
        });

        Schema::create('seller_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained()->cascadeOnDelete();
            $table->string('display_name');
            $table->text('bio')->nullable();
            $table->foreignId('region_id')->nullable()->constrained()->nullOnDelete();
            $table->string('approval_status')->default('pending');
            $table->text('rejection_reason')->nullable();
            $table->timestamps();
        });

        Schema::table('birds', function (Blueprint $table) {
            $table->foreignId('region_id')->nullable()->after('city')->constrained()->nullOnDelete();
            $table->string('approval_status')->default('approved')->after('featured');
            $table->text('rejection_reason')->nullable()->after('approval_status');
            $table->timestamp('published_at')->nullable()->after('rejection_reason');
            $table->index(['region_id', 'status', 'approval_status']);
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->foreignId('buyer_region_id')->nullable()->after('city')->constrained('regions')->nullOnDelete();
        });

        $cities = DB::table('birds')->whereNotNull('city')->distinct()->pluck('city');

        foreach ($cities as $position => $city) {
            $base = Str::slug($city) ?: 'region';
            $slug = $base;
            $counter = 2;

            while (DB::table('regions')->where('slug', $slug)->exists()) {
                $slug = $base.'-'.$counter++;
            }

            $regionId = DB::table('regions')->insertGetId([
                'name' => $city,
                'slug' => $slug,
                'active' => true,
                'sort_order' => $position + 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            DB::table('birds')->where('city', $city)->update(['region_id' => $regionId]);
        }

        DB::table('birds')->update([
            'approval_status' => 'approved',
            'published_at' => now(),
        ]);

        DB::table('settings')->updateOrInsert(
            ['key' => 'seller_approval_required'],
            ['value' => '1', 'created_at' => now(), 'updated_at' => now()]
        );
        DB::table('settings')->updateOrInsert(
            ['key' => 'listing_approval_required'],
            ['value' => '1', 'created_at' => now(), 'updated_at' => now()]
        );
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropConstrainedForeignId('buyer_region_id');
        });

        Schema::table('birds', function (Blueprint $table) {
            $table->dropIndex(['region_id', 'status', 'approval_status']);
            $table->dropConstrainedForeignId('region_id');
            $table->dropColumn(['approval_status', 'rejection_reason', 'published_at']);
        });

        Schema::dropIfExists('seller_profiles');

        Schema::table('users', function (Blueprint $table) {
            $table->dropConstrainedForeignId('region_id');
            $table->dropColumn('phone');
        });

        Schema::dropIfExists('regions');
    }
};
