<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Adds a `tiers` JSON column to `packages` listing which traveller tiers
     * the package is offered to. NULL or empty array = available to everyone
     * (the legacy / safe default for existing rows).
     *
     * Stored as a JSON array of tier slugs from TravellerTier::TIERS, e.g.
     *   ["local","african","international"]
     *   ["local"]                   (locals-only)
     *   ["african","international"] (no Zim residents)
     */
    public function up(): void
    {
        Schema::table('packages', function (Blueprint $table) {
            $table->json('tiers')->nullable()->after('is_hero');
        });
    }

    public function down(): void
    {
        Schema::table('packages', function (Blueprint $table) {
            $table->dropColumn('tiers');
        });
    }
};
