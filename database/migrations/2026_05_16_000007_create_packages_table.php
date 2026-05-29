<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('packages', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('country')->default('Zimbabwe');
            $table->string('summary');
            $table->text('description')->nullable();
            $table->text('itinerary')->nullable();
            $table->text('transfers')->nullable();
            $table->json('inclusions')->nullable();
            $table->json('exclusions')->nullable();
            $table->string('duration')->nullable();
            $table->string('group_size')->nullable();
            $table->string('price_from')->nullable();
            $table->foreignId('accommodation_id')->nullable()->constrained('accommodations')->nullOnDelete();
            $table->boolean('is_active')->default(true);
            $table->boolean('is_featured')->default(false);
            $table->softDeletes();
            $table->timestamps();

            $table->index('country');
            $table->index(['is_active', 'is_featured']);
        });

        Schema::create('destination_package', function (Blueprint $table) {
            $table->id();
            $table->foreignId('destination_id')->constrained('destinations')->cascadeOnDelete();
            $table->foreignId('package_id')->constrained('packages')->cascadeOnDelete();
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();

            $table->unique(['destination_id', 'package_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('destination_package');
        Schema::dropIfExists('packages');
    }
};
