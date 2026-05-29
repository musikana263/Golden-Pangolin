<?php

namespace Database\Seeders;

use App\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UsersTableSeeder extends Seeder
{
    /**
     * Seed the initial admin account.
     *
     * - In local / testing: keeps the documented `password` for convenience.
     * - In production: refuses to seed unless SEED_ADMIN_EMAIL +
     *   SEED_ADMIN_PASSWORD are set in the environment. We never want to ship
     *   a guessable admin to the public internet.
     */
    public function run(): void
    {
        $isProd = app()->environment('production');

        $email = env('SEED_ADMIN_EMAIL', 'info@goldenpangolin.com');
        $password = env('SEED_ADMIN_PASSWORD', $isProd ? null : 'GoldP@2026');
        $name = env('SEED_ADMIN_NAME', 'Administration');

        if ($isProd && ! $password) {
            $this->command->error(
                'Refusing to seed admin in production without SEED_ADMIN_PASSWORD.'
            );
            return;
        }

        $user = User::where('email', $email)->first();

        if ($user) {
            // Keep the canonical admin in sync with config — update password
            // + name in case they've drifted.
            $user->forceFill([
                'name'     => $name,
                'password' => Hash::make($password),
            ])->save();
            $user->makeAdmin();
            return;
        }

        $user = User::create([
            'name'     => $name,
            'email'    => $email,
            'password' => Hash::make($password),
        ]);
        $user->makeAdmin();
    }
}
