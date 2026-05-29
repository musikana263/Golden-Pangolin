<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Three pricing modes for activities:
     *
     *   - 'tiered' (default, legacy): price_local / price_african /
     *     price_international are each set independently. A visitor only sees
     *     the activity if THEIR tier column is non-null.
     *
     *   - 'fixed': one price for everyone, stored in `unit_price`. Activity
     *     shows for all tiers.
     *
     *   - 'from': an approximate text label like "From $50 per person",
     *     stored in the existing `price_from` text field. Activity shows
     *     for all tiers.
     *
     * Backfill rule:
     *   - If at least one tier column is set → 'tiered' (unchanged behaviour)
     *   - Else if price_from has text          → 'from'
     *   - Else                                  → 'tiered' (safe default; admin
     *     will see the activity hidden until they fill in something)
     */
    public function up(): void
    {
        Schema::table('activities', function (Blueprint $table) {
            $table->string('price_mode', 16)->default('tiered')->after('price_international');
            $table->decimal('unit_price', 10, 2)->nullable()->after('price_mode');
        });

        // Backfill: rows that look like "from" pricing get marked accordingly
        // so the public site keeps showing them after the upgrade.
        DB::statement("
            UPDATE activities
            SET price_mode = 'from'
            WHERE price_local IS NULL
              AND price_african IS NULL
              AND price_international IS NULL
              AND price_from IS NOT NULL
              AND price_from <> ''
        ");
    }

    public function down(): void
    {
        Schema::table('activities', function (Blueprint $table) {
            $table->dropColumn(['price_mode', 'unit_price']);
        });
    }
};
