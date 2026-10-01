<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Listing page views (once per visitor session), shown to the seller as listing performance.
        Schema::table('birds', function (Blueprint $table) {
            $table->unsignedInteger('views_count')->default(0)->after('featured');
        });
    }

    public function down(): void
    {
        Schema::table('birds', function (Blueprint $table) {
            $table->dropColumn('views_count');
        });
    }
};
