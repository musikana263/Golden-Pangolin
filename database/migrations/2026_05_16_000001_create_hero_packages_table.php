<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('hero_packages', function (Blueprint $table) {
            $table->id();
            $table->string('badge_text')->default('Popular Choice');
            $table->string('title');
            $table->string('image')->nullable();
            $table->string('duration')->nullable();
            $table->string('group_size')->nullable();
            $table->string('price_label')->default('From');
            $table->string('price_value')->nullable();
            $table->string('link_url')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hero_packages');
    }
};
