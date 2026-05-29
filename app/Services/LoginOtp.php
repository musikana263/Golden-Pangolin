<?php

namespace App\Services;

use App\Mail\LoginOtpMail;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

/**
 * Email one-time-password challenge for login.
 *
 * Flow:
 *   1. Password is validated by Laravel's normal login flow.
 *   2. Instead of logging the user in, we call {@see generateAndSend()}
 *      which creates a fresh 6-digit code, hashes it into login_otps, and
 *      emails the plaintext code to the user.
 *   3. The user is bounced to the OTP challenge view. They type the code.
 *   4. {@see verify()} hashes the candidate, compares against the latest
 *      unexpired, unused record, and reports success/failure. On success
 *      the record is marked `used_at` so it can't be replayed.
 */
class LoginOtp
{
    public const CODE_TTL_MINUTES = 10;
    public const MAX_ATTEMPTS = 5;
    public const SESSION_USER_KEY = 'pending_otp_user_id';

    /** Generate, persist, and email a fresh OTP to the given user. */
    public function generateAndSend(User $user, Request $request): void
    {
        // Invalidate any older outstanding codes for this user — only the
        // most recent one is valid.
        DB::table('login_otps')
            ->where('user_id', $user->id)
            ->whereNull('used_at')
            ->update(['used_at' => now()]);

        $code = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        DB::table('login_otps')->insert([
            'user_id'    => $user->id,
            'code_hash'  => hash('sha256', $code),
            'expires_at' => now()->addMinutes(self::CODE_TTL_MINUTES),
            'ip_address' => $request->ip(),
            'user_agent' => substr((string) $request->userAgent(), 0, 500),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        Mail::to($user->email)->send(new LoginOtpMail($user, $code, self::CODE_TTL_MINUTES));
    }

    /**
     * Verify the candidate code against the most recent OTP for the user.
     * Returns true on success (and marks the row used). Increments the
     * attempt counter on failure.
     */
    public function verify(User $user, string $candidate): bool
    {
        $candidate = preg_replace('/\D/', '', $candidate) ?? '';
        if (strlen($candidate) !== 6) {
            return false;
        }

        $row = DB::table('login_otps')
            ->where('user_id', $user->id)
            ->whereNull('used_at')
            ->orderByDesc('id')
            ->first();

        if (! $row) {
            return false;
        }

        if (Carbon::parse($row->expires_at)->isPast()) {
            return false;
        }

        if ($row->attempts >= self::MAX_ATTEMPTS) {
            // Burn it — too many guesses, force a fresh send.
            DB::table('login_otps')->where('id', $row->id)->update(['used_at' => now()]);
            return false;
        }

        if (! hash_equals($row->code_hash, hash('sha256', $candidate))) {
            DB::table('login_otps')->where('id', $row->id)->increment('attempts');
            return false;
        }

        DB::table('login_otps')->where('id', $row->id)->update(['used_at' => now()]);
        return true;
    }
}
