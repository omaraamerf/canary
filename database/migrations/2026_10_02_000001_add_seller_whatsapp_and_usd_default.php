<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Sellers now choose USD or SYP per listing; existing rows keep their currency.
        Schema::table('birds', function (Blueprint $table) {
            $table->string('currency', 3)->default('USD')->change();
        });

        Schema::table('seller_profiles', function (Blueprint $table) {
            $table->string('whatsapp', 30)->nullable()->after('bio');
        });
    }

    public function down(): void
    {
        Schema::table('seller_profiles', function (Blueprint $table) {
            $table->dropColumn('whatsapp');
        });

        Schema::table('birds', function (Blueprint $table) {
            $table->string('currency', 3)->default('SAR')->change();
        });
    }
};
