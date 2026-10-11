<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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

        if (Auth::attempt($credentials, $request->has('remember'))) {

            // SECURITY CHECK: Intercept deactivated users immediately
            if (Auth::user()->account_status !== 'active') {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                return back()->withErrors([
                    'username' => 'Your account has been deactivated. Please contact Management.',
                ])->onlyInput('username');
            }

            // If active, proceed normally
            $request->session()->regenerate();

            // Send each role to a page it is allowed to open
            return redirect($this->homeFor(Auth::user()));
        }

        return back()->withErrors([
            'username' => 'The provided credentials do not match our records.',
        ])->onlyInput('username');
    }

    /**
     * Landing page per role. Dashboard is restricted to Sales + Founders.
     */
/**
     * Landing page per role with explicit routing rules.
     */
    private function homeFor(User $user): string
    {
        return match ($user->employee?->role) {
            'Production Manager', 'Craftsmen'                       => route('production.index'),
            'Delivery Personnel'                                    => route('delivery.index'),
            'Sales & Record Personnel', 'Founders/General Manager' => route('dashboard'),
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
