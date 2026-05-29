<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('destinations', function (Blueprint $table) {
            foreach (['pricing', 'duration', 'group_size', 'tour_type', 'category_id'] as $col) {
                if (Schema::hasColumn('destinations', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }

    public function down(): void
    {
        Schema::table('destinations', function (Blueprint $table) {
            $table->string('pricing')->nullable();
            $table->string('duration')->nullable();
            $table->string('group_size')->nullable();
            $table->string('tour_type')->nullable();
            $table->unsignedBigInteger('category_id')->nullable();
        });
    }
};
