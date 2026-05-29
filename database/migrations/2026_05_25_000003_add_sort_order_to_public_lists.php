<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Adds a `sort_order` column to each table whose rows are surfaced on the
     * public site and need an admin-controlled display order. `activities`
     * already had one (see 2026_05_18_000001_create_activities_table.php) so
     * it's skipped here.
     *
     * Default 0 + we backfill so existing rows get a deterministic initial
     * order (oldest first) — admins can then drag to refine.
     */
    public function up(): void
    {
        foreach (['destinations', 'packages', 'accommodations', 'blogs'] as $table) {
            if (! Schema::hasColumn($table, 'sort_order')) {
                Schema::table($table, function (Blueprint $t) {
                    $t->unsignedSmallInteger('sort_order')->default(0)->after('id');
                    $t->index('sort_order');
                });

                // Backfill: number existing rows 1..N by id so each gets a
                // unique starting position. Without this every row would
                // start at 0 and drag-reorder would behave oddly.
                DB::table($table)
                    ->orderBy('id')
                    ->pluck('id')
                    ->each(function ($id, $i) use ($table) {
                        DB::table($table)->where('id', $id)->update(['sort_order' => $i + 1]);
                    });
            }
        }
    }

    public function down(): void
    {
        foreach (['destinations', 'packages', 'accommodations', 'blogs'] as $table) {
            if (Schema::hasColumn($table, 'sort_order')) {
                Schema::table($table, function (Blueprint $t) {
                    $t->dropIndex([$t->getTable().'_sort_order_index']);
                    $t->dropColumn('sort_order');
                });
            }
        }
    }
};
