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
            'username' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        $user = User::where('username', $credentials['username'])->first();

        // Check if account is locked before attempting login
        if ($user && $user->account_status === 'locked') {
            return back()->withErrors([
                'username' => 'Account locked due to multiple failed attempts. Please contact the General Manager.',
            ])->onlyInput('username');
        }

        // Attempt authentication
        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();

            // Reset failed attempts upon successful login
            $user->update(['failed_attempts' => 0]);

            return redirect()->intended('/dashboard');
        }

        // Handle failed attempt
        if ($user) {
            $user->increment('failed_attempts');

            if ($user->failed_attempts >= 5) {
                $user->update(['account_status' => 'locked']);
                return back()->withErrors([
                    'username' => 'Account locked due to multiple failed attempts. Please contact the General Manager.',
                ])->onlyInput('username');
            }
        }

        return back()->withErrors([
            'username' => 'The provided credentials do not match our records.',
        ])->onlyInput('username');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/login');
    }
}
