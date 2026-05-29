<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (DB::connection()->getDriverName() === 'sqlite') {
            // SQLite stores pricing as text already; nothing to do.
            return;
        }

        Schema::table('destinations', function (Blueprint $table) {
            $table->string('pricing')->nullable()->change();
        });
    }

    public function down(): void
    {
        // No-op: reverting to decimal would risk data loss.
    }
};
