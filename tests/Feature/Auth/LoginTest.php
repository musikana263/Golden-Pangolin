<?php

namespace Tests\Feature\Auth;

use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LoginTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_page_can_be_rendered(): void
    {
        $response = $this->get('/login');

        $response->assertStatus(200);
    }

    public function test_correct_password_sends_otp_and_does_not_authenticate_yet(): void
    {
        $user = User::factory()->create([
            'email_verified_at' => now(),
        ]);

        \Illuminate\Support\Facades\Mail::fake();

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        // Step 1 of OTP login: password is correct, but the user is NOT
        // signed in yet — they have to clear the email OTP challenge.
        $this->assertGuest();
        $response->assertRedirect(route('otp.challenge'));

        // A pending OTP row exists for this user (the mail facade is faked so
        // we don't actually send anything, but the DB write still happens).
        $this->assertDatabaseHas('login_otps', ['user_id' => $user->id, 'used_at' => null]);
    }

    public function test_users_cannot_authenticate_with_invalid_password(): void
    {
        $user = User::factory()->create();

        $this->post('/login', [
            'email' => $user->email,
            'password' => 'wrong-password',
        ]);

        $this->assertGuest();
    }

    public function test_users_can_logout(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/logout');

        $this->assertGuest();
        $response->assertRedirect('/');
    }
}
