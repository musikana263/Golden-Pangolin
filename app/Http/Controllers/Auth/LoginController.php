<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\LoginOtp;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    use AuthenticatesUsers;

    protected $redirectTo = '/home';

    public function __construct(private LoginOtp $otp)
    {
        $this->middleware('guest')->except('logout');
    }

    public function username(): string
    {
        return 'email';
    }

    /**
     * Step 1 of login: password is correct. When OTP is enabled (the default),
     * we don't actually keep the session yet — we send an OTP to the user's
     * email and bounce them to the challenge page. Step 2 happens in
     * OtpChallengeController::verify().
     *
     * Toggle via `OTP_LOGIN_ENABLED=false` in `.env` to skip the second
     * factor — useful for local testing only.
     */
    protected function sendLoginResponse(Request $request)
    {
        if (! config('auth.otp_login_enabled', true)) {
            // OTP disabled — let Laravel's stock flow finish the login.
            return $this->traitSendLoginResponse($request);
        }

        $user = $request->user();

        // Roll back the session login that AuthenticatesUsers just did —
        // we don't trust the password alone.
        Auth::guard()->logout();
        $request->session()->forget(Auth::guard()->getName());
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        // Stash the pending user, fire the OTP email.
        $request->session()->put(LoginOtp::SESSION_USER_KEY, $user->id);
        $this->otp->generateAndSend($user, $request);

        $this->clearLoginAttempts($request);

        return redirect()->route('otp.challenge');
    }

    /** Inline copy of the trait's original sendLoginResponse so we can call it
     *  when OTP is disabled. */
    private function traitSendLoginResponse(Request $request)
    {
        $request->session()->regenerate();
        $this->clearLoginAttempts($request);

        if ($response = $this->authenticated($request, $this->guard()->user())) {
            return $response;
        }

        return $request->wantsJson()
            ? new \Illuminate\Http\JsonResponse([], 204)
            : redirect()->intended($this->redirectPath());
    }
}
