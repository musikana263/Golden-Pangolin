<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\LoginOtp;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OtpChallengeController extends Controller
{
    public function __construct(private LoginOtp $otp) {}

    public function show(Request $request)
    {
        $userId = $request->session()->get(LoginOtp::SESSION_USER_KEY);
        if (! $userId) {
            return redirect()->route('login');
        }

        $user = User::find($userId);
        if (! $user) {
            $request->session()->forget(LoginOtp::SESSION_USER_KEY);
            return redirect()->route('login');
        }

        // Mask the email so it's recognisable but not fully disclosed.
        $email = (string) $user->email;
        [$local, $domain] = array_pad(explode('@', $email, 2), 2, '');
        $maskedEmail = strlen($local) > 2
            ? substr($local, 0, 2).str_repeat('*', max(1, strlen($local) - 2)).'@'.$domain
            : '***@'.$domain;

        return view('auth.otp', [
            'maskedEmail' => $maskedEmail,
        ]);
    }

    public function verify(Request $request)
    {
        $request->validate([
            'code' => 'required|string',
        ]);

        $userId = $request->session()->get(LoginOtp::SESSION_USER_KEY);
        if (! $userId) {
            return redirect()->route('login')->withErrors([
                'code' => 'Your sign-in session expired. Please start again.',
            ]);
        }

        $user = User::find($userId);
        if (! $user) {
            $request->session()->forget(LoginOtp::SESSION_USER_KEY);
            return redirect()->route('login');
        }

        if (! $this->otp->verify($user, $request->input('code'))) {
            return back()->withErrors([
                'code' => 'That code is invalid or has expired. Try again or request a new one.',
            ]);
        }

        // Finalize login.
        $request->session()->forget(LoginOtp::SESSION_USER_KEY);
        Auth::login($user, $request->boolean('remember'));
        $request->session()->regenerate();

        return redirect()->intended(route('home'));
    }

    public function resend(Request $request)
    {
        $userId = $request->session()->get(LoginOtp::SESSION_USER_KEY);
        if (! $userId) {
            return redirect()->route('login');
        }

        $user = User::find($userId);
        if (! $user) {
            $request->session()->forget(LoginOtp::SESSION_USER_KEY);
            return redirect()->route('login');
        }

        $this->otp->generateAndSend($user, $request);

        return back()->with('status', 'A new code has been sent to your email.');
    }

    public function cancel(Request $request)
    {
        $request->session()->forget(LoginOtp::SESSION_USER_KEY);
        return redirect()->route('login');
    }
}
