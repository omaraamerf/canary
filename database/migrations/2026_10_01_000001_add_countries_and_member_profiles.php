<?php

use Database\Seeders\LocationSeeder;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('countries', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('name_en')->nullable();
            $table->string('code', 2)->unique();
            $table->boolean('active')->default(true);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::table('regions', function (Blueprint $table) {
            $table->foreignId('country_id')->nullable()->after('id')->constrained()->nullOnDelete();
        });

        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('country_id')->nullable()->after('status')->constrained()->nullOnDelete();
            $table->text('bio')->nullable()->after('phone');
            $table->text('avatar_url')->nullable()->after('bio');
            $table->string('avatar_public_id')->nullable()->after('avatar_url');
        });

        Schema::table('posts', function (Blueprint $table) {
            $table->foreignId('country_id')->nullable()->after('breed_id')->constrained()->nullOnDelete();
        });

        // Deployments only run migrations, so the location data is seeded here as well.
        (new LocationSeeder)->run();
    }

    public function down(): void
    {
        $codes = collect(LocationSeeder::COUNTRIES)->where('code', '!=', 'SY')->pluck('code')->map(fn (string $code) => strtolower($code).'-%');

        DB::table('regions')->where(function ($query) use ($codes) {
            foreach ($codes as $pattern) {
                $query->orWhere('slug', 'like', $pattern);
            }
        })
            ->whereNotExists(fn ($query) => $query->selectRaw('1')->from('birds')->whereColumn('birds.region_id', 'regions.id'))
            ->whereNotExists(fn ($query) => $query->selectRaw('1')->from('seller_profiles')->whereColumn('seller_profiles.region_id', 'regions.id'))
            ->whereNotExists(fn ($query) => $query->selectRaw('1')->from('orders')->whereColumn('orders.buyer_region_id', 'regions.id'))
            ->delete();

        Schema::table('posts', fn (Blueprint $table) => $table->dropConstrainedForeignId('country_id'));
        Schema::table('users', function (Blueprint $table) {
            $table->dropConstrainedForeignId('country_id');
            $table->dropColumn(['bio', 'avatar_url', 'avatar_public_id']);
        });
        Schema::table('regions', fn (Blueprint $table) => $table->dropConstrainedForeignId('country_id'));
        Schema::dropIfExists('countries');
    }
};
