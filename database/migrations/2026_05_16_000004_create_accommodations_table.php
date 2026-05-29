<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('accommodations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('accommodation_type_id')
                ->nullable()
                ->constrained('accommodation_types')
                ->nullOnDelete();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('country')->default('Zimbabwe');
            $table->string('location')->nullable();
            $table->text('description');
            $table->text('content')->nullable();
            $table->string('price_from')->nullable();
            $table->json('amenities')->nullable();
            $table->boolean('is_active')->default(true);
            $table->softDeletes();
            $table->timestamps();

            $table->index('country');
            $table->index('is_active');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('accommodations');
    }
};
