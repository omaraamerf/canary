<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('role')->default('seller')->after('password');
            $table->string('status')->default('active')->after('role');
        });

        Schema::create('breeds', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->boolean('active')->default(true);
            $table->timestamps();
        });

        Schema::create('birds', function (Blueprint $table) {
            $table->id();
            $table->foreignId('seller_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('breed_id')->constrained()->restrictOnDelete();
            $table->string('title');
            $table->string('slug')->unique();
            $table->string('sex');
            $table->unsignedSmallInteger('hatch_year')->nullable();
            $table->string('color');
            $table->string('molt_status')->nullable();
            $table->boolean('breeding_ready')->nullable();
            $table->string('singing_status')->nullable();
            $table->string('ring_number')->nullable();
            $table->decimal('price', 10, 2);
            $table->string('currency', 3)->default('SAR');
            $table->string('city');
            $table->string('delivery_type');
            $table->text('description')->nullable();
            $table->string('status')->default('available');
            $table->boolean('featured')->default(false);
            $table->timestamps();
            $table->softDeletes();

            $table->index(['status', 'breed_id']);
            $table->index(['city', 'status']);
        });

        Schema::create('bird_media', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bird_id')->constrained()->cascadeOnDelete();
            $table->string('type');
            $table->text('url');
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('reference')->unique();
            $table->foreignId('bird_id')->constrained()->restrictOnDelete();
            $table->string('buyer_name');
            $table->string('phone');
            $table->string('city');
            $table->string('delivery_method');
            $table->text('notes')->nullable();
            $table->string('status')->default('pending');
            $table->decimal('price_snapshot', 10, 2);
            $table->string('currency_snapshot', 3);
            $table->timestamps();

            $table->index(['status', 'created_at']);
            $table->index(['bird_id', 'status']);
        });

        Schema::create('order_status_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->string('old_status')->nullable();
            $table->string('new_status');
            $table->text('note')->nullable();
            $table->foreignId('changed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('settings');
        Schema::dropIfExists('order_status_logs');
        Schema::dropIfExists('orders');
        Schema::dropIfExists('bird_media');
        Schema::dropIfExists('birds');
        Schema::dropIfExists('breeds');

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['role', 'status']);
        });
    }
};
