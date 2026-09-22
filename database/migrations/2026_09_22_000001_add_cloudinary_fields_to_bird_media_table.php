<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bird_media', function (Blueprint $table) {
            $table->string('provider')->nullable()->after('type');
            $table->string('public_id')->nullable()->after('provider')->index();
        });
    }

    public function down(): void
    {
        Schema::table('bird_media', function (Blueprint $table) {
            $table->dropIndex(['public_id']);
            $table->dropColumn(['provider', 'public_id']);
        });
    }
};
