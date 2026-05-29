<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('activities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('destination_id')->constrained('destinations')->cascadeOnDelete();
            $table->string('name');
            $table->string('slug');
            $table->text('description');
            $table->text('content')->nullable();
            $table->string('duration')->nullable();
            $table->string('price_from')->nullable();
            $table->boolean('is_active')->default(true);
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();

            $table->index(['destination_id', 'sort_order']);
            $table->unique(['destination_id', 'slug']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('activities');
    }
};
