<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\ResetsPasswords;
use Illuminate\Http\Request;

class ResetPasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset requests
    | and uses a simple trait to include this behavior. You're free to
    | explore this trait and override any methods you wish to tweak.
    |
    */

    use ResetsPasswords;

    /**
     * Where to redirect users after resetting their password.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::HOME;

    /**
     * Show the reset-password form.
     *
     * Accepts the visitor's email via either:
     *  - `?em=<base64url>` (current — what we email out, keeps the address
     *    out of the URL bar so phishing heuristics don't flag the link), or
     *  - `?email=<address>` (legacy — still honoured for any links sent
     *    before this change).
     */
    public function showResetForm(Request $request, $token = null)
    {
        $email = $request->query('email');

        if (! $email && $em = $request->query('em')) {
            $padded = strtr($em, '-_', '+/');
            $padded .= str_repeat('=', (4 - strlen($padded) % 4) % 4);
            $decoded = base64_decode($padded, true);
            if ($decoded && filter_var($decoded, FILTER_VALIDATE_EMAIL)) {
                $email = $decoded;
            }
        }

        return view('auth.passwords.reset')->with([
            'token' => $token,
            'email' => $email,
        ]);
    }
}
