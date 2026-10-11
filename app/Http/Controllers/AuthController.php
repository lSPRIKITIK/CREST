<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use App\Models\User;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        // Create a unique strike counter key based on the entered username
        $throttleKey = 'login_strikes_' . strtolower($request->username);

        if (Auth::attempt($credentials, $request->has('remember'))) {
            // SUCCESS: Clear their strike counter immediately
            RateLimiter::clear($throttleKey);

            // SECURITY CHECK: Intercept deactivated users immediately
            if (Auth::user()->account_status !== 'active') {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                return back()->withErrors([
                    'username' => 'Your account has been deactivated. Please contact Management.',
                ])->onlyInput('username');
            }

            $request->session()->regenerate();
            return redirect($this->homeFor(Auth::user()));
        }

// FAILED LOGIN: Check if the username actually exists and is active
        $user = User::where('username', $request->username)->first();
        if ($user && $user->account_status === 'active') {

            // SECURITY EXCEPTION: Founder gets unlimited attempts
            $isAdmin = $user->employee?->role === 'Founders/General Manager';

            if (!$isAdmin) {
                // Record a strike for normal employees (lasts for 24 hours)
                RateLimiter::hit($throttleKey, 86400);

                if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
                    // 5th Strike: Lock the account and clear the tracker
                    $user->update(['account_status' => 'deactivated']);
                    RateLimiter::clear($throttleKey);

                    return back()->withErrors([
                        'username' => 'SECURITY ALERT: Account locked due to 5 failed login attempts. Please contact the Founder to reactivate your account.',
                    ])->onlyInput('username');
                }

                $attemptsLeft = 5 - RateLimiter::attempts($throttleKey);
                return back()->withErrors([
                    'username' => "Incorrect password. You have $attemptsLeft attempt(s) left before your account is locked.",
                ])->onlyInput('username');
            }

            // If it IS the Founder/Admin, just return a standard error with NO strike penalty
            return back()->withErrors([
                'username' => 'Incorrect password.',
            ])->onlyInput('username');
        }

        // Generic error if username doesn't exist or is already deactivated
        return back()->withErrors([
            'username' => 'The provided credentials do not match our records or your account is disabled.',
        ])->onlyInput('username');
    }
    /**
     * Landing page per role with explicit routing rules.
     */
    private function homeFor(User $user): string
    {
        return match ($user->employee?->role) {
            'Production Manager', 'Craftsmen'                       => route('production.index'),
            'Delivery Personnel'                                    => route('delivery.index'),
            'Sales & Record Personnel', 'Founders/General Manager'  => route('dashboard'),
            default                                                 => route('dashboard'),
        };
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/login');
    }
}
