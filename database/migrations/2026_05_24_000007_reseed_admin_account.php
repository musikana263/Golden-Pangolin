<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

return new class extends Migration
{
    public function up(): void
    {
        // Remove the legacy seed admin if it's still there.
        DB::table('users')->where('email', 'samadmin@gmail.com')->delete();

        $email = 'info@goldenpangolin.com';
        $now   = now();

        $existing = DB::table('users')->where('email', $email)->first();

        if ($existing) {
            DB::table('users')
                ->where('id', $existing->id)
                ->update([
                    'name'       => 'Administration',
                    'password'   => Hash::make('GoldP@2026'),
                    'role'       => 'admin',
                    'updated_at' => $now,
                ]);
        } else {
            DB::table('users')->insert([
                'name'       => 'Administration',
                'email'      => $email,
                'password'   => Hash::make('GoldP@2026'),
                'role'       => 'admin',
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }
    }

    public function down(): void
    {
        // Intentionally a no-op — we don't restore the legacy account.
    }
};
