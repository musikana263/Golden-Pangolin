<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('packages', function (Blueprint $table) {
            // Only ONE package should be is_hero at any time — uniqueness is
            // enforced in the controller, not in the schema (since a partial
            // unique index isn't portable across MySQL versions).
            $table->boolean('is_hero')->default(false)->after('is_featured');
            $table->index('is_hero');
        });
    }

    public function down(): void
    {
        Schema::table('packages', function (Blueprint $table) {
            $table->dropIndex(['is_hero']);
            $table->dropColumn('is_hero');
        });
    }
};
