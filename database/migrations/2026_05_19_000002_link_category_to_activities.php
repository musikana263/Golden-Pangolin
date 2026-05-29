<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('activities', function (Blueprint $table) {
            if (! Schema::hasColumn('activities', 'category_id')) {
                $table->foreignId('category_id')
                    ->nullable()
                    ->after('destination_id')
                    ->constrained('categories')
                    ->nullOnDelete();
            }

            if (Schema::hasColumn('activities', 'content')) {
                $table->dropColumn('content');
            }
        });
    }

    public function down(): void
    {
        Schema::table('activities', function (Blueprint $table) {
            if (Schema::hasColumn('activities', 'category_id')) {
                $table->dropConstrainedForeignId('category_id');
            }
            $table->text('content')->nullable();
        });
    }
};
