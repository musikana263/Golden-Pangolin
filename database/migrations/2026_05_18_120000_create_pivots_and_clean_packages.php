<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // Create destination_package pivot
        if (! Schema::hasTable('destination_package')) {
            Schema::create('destination_package', function (Blueprint $table) {
                $table->id();
                $table->foreignId('package_id')->constrained('packages')->cascadeOnDelete();
                $table->foreignId('destination_id')->constrained('destinations')->cascadeOnDelete();
                $table->unsignedSmallInteger('sort_order')->default(0);
                $table->timestamps();
                $table->unique(['destination_id', 'package_id']);
            });
        }

        // Create activity_package pivot
        if (! Schema::hasTable('activity_package')) {
            Schema::create('activity_package', function (Blueprint $table) {
                $table->id();
                $table->foreignId('activity_id')->constrained('activities')->cascadeOnDelete();
                $table->foreignId('package_id')->constrained('packages')->cascadeOnDelete();
                $table->unsignedSmallInteger('sort_order')->default(0);
                $table->timestamps();
                $table->unique(['activity_id', 'package_id']);
            });
        }

        // Drop old unused columns from packages
        Schema::table('packages', function (Blueprint $table) {
            foreach (['description', 'itinerary', 'transfers', 'inclusions', 'exclusions'] as $col) {
                if (Schema::hasColumn('packages', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('activity_package');
        Schema::dropIfExists('destination_package');

        // Restore old columns if needed
        Schema::table('packages', function (Blueprint $table) {
            $table->text('description')->nullable();
            $table->text('itinerary')->nullable();
            $table->text('transfers')->nullable();
            $table->json('inclusions')->nullable();
            $table->json('exclusions')->nullable();
        });
    }
};
