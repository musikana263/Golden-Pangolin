<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // The admin form no longer exposes the long-form `content` field —
        // public destination pages show only the short description. Make the
        // column nullable so new INSERTs that skip it don't fail with
        // "Field 'content' doesn't have a default value".
        //
        // Requires doctrine/dbal historically, but Laravel 11 + MySQL handles
        // this natively via Schema::change(). Old content on existing rows
        // is preserved untouched.
        Schema::table('destinations', function (Blueprint $table) {
            $table->text('content')->nullable()->change();
        });
    }

    public function down(): void
    {
        // Going back to NOT NULL would fail on rows currently holding NULL,
        // so backfill with empty string first.
        \DB::table('destinations')->whereNull('content')->update(['content' => '']);

        Schema::table('destinations', function (Blueprint $table) {
            $table->text('content')->nullable(false)->change();
        });
    }
};
