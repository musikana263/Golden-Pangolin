<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('activities', function (Blueprint $table) {
            // Three nullable tier-specific prices. An activity is "available"
            // to a tier only if that tier's column is NOT NULL — the
            // Activity::availableTo() scope enforces this.
            $table->decimal('price_local', 10, 2)->nullable()->after('price_from');
            $table->decimal('price_african', 10, 2)->nullable()->after('price_local');
            $table->decimal('price_international', 10, 2)->nullable()->after('price_african');
        });

        // Backfill: treat the legacy free-text `price_from` as the
        // international rate when it parses cleanly as a number, so every
        // existing activity stays visible to international visitors after
        // this migration runs. We deliberately keep `price_from` for now.
        DB::table('activities')
            ->whereNotNull('price_from')
            ->whereNull('price_international')
            ->orderBy('id')
            ->each(function ($row) {
                if (preg_match('/(\d+(?:\.\d+)?)/', (string) $row->price_from, $m)) {
                    DB::table('activities')
                        ->where('id', $row->id)
                        ->update(['price_international' => (float) $m[1]]);
                }
            });
    }

    public function down(): void
    {
        Schema::table('activities', function (Blueprint $table) {
            $table->dropColumn(['price_local', 'price_african', 'price_international']);
        });
    }
};
