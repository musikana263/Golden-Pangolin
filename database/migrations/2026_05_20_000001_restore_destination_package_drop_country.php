<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('destination_package')) {
            Schema::create('destination_package', function (Blueprint $table) {
                $table->id();
                $table->foreignId('destination_id')->constrained('destinations')->cascadeOnDelete();
                $table->foreignId('package_id')->constrained('packages')->cascadeOnDelete();
                $table->unsignedSmallInteger('sort_order')->default(0);
                $table->timestamps();
                $table->unique(['destination_id', 'package_id']);
            });
        }

        if (Schema::hasColumn('packages', 'country')) {
            Schema::table('packages', function (Blueprint $table) {
                $table->dropIndex(['country']);
            });
            Schema::table('packages', function (Blueprint $table) {
                $table->dropColumn('country');
            });
        }
    }

    public function down(): void
    {
        Schema::table('packages', function (Blueprint $table) {
            $table->string('country')->default('Zimbabwe');
            $table->index('country');
        });

        Schema::dropIfExists('destination_package');
    }
};
