<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // 'local' | 'african' | 'international' — set by the visitor via
            // the cookie consent banner. Nullable so legacy users default
            // to "unknown" and get the international tier from the resolver.
            $table->string('traveller_tier', 20)->nullable()->after('role');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('traveller_tier');
        });
    }
};
