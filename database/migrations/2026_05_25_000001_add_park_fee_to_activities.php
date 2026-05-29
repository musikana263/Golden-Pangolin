<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('activities', function (Blueprint $table) {
            // Nullable on purpose — only activities that actually charge a
            // park / conservation / entrance fee get one. Null means "no fee
            // shown" and the UI hides that line entirely. Stored as USD
            // (same currency as the tier prices) so we can render it inline
            // without conversion.
            $table->decimal('park_fee', 10, 2)->nullable()->after('price_international');

            // Optional label override. Defaults to "Park fee" in the UI when
            // unset. Lets admins pick "Conservation fee", "Entry fee", etc.
            // for activities where that wording fits better.
            $table->string('park_fee_label', 60)->nullable()->after('park_fee');
        });
    }

    public function down(): void
    {
        Schema::table('activities', function (Blueprint $table) {
            $table->dropColumn(['park_fee', 'park_fee_label']);
        });
    }
};
