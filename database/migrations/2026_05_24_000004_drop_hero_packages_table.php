<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // The hero featured slot is now driven by `packages.is_hero` — the
        // standalone hero_packages table is obsolete.
        Schema::dropIfExists('hero_packages');
    }

    public function down(): void
    {
        // Re-create the minimal shape if you ever need to roll back. Fields
        // are best-effort — the original migration may have had more columns.
        Schema::create('hero_packages', function (Blueprint $table) {
            $table->id();
            $table->string('badge_text')->nullable();
            $table->string('title')->nullable();
            $table->string('image')->nullable();
            $table->string('duration')->nullable();
            $table->string('group_size')->nullable();
            $table->string('price_label')->nullable();
            $table->string('price_value')->nullable();
            $table->string('link_url')->nullable();
            $table->boolean('is_active')->default(false);
            $table->timestamps();
        });
    }
};
