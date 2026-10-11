<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RoleMiddleware
{
public function handle(Request $request, Closure $next, ...$roles)
    {
        if (!Auth::check()) {
            return redirect('/login');
        }

        // 1. Check if the account has been deactivated
        if (Auth::user()->account_status !== 'active') {
            Auth::logout();
            return redirect('/login')->withErrors(['username' => 'Your account has been deactivated. Please contact Management.']);
        }

        $userRole = Auth::user()->employee->role;

        // SUPER ADMIN BYPASS: Founders get access to EVERYTHING
        if ($userRole === 'Founders/General Manager') {
            return $next($request);
        }

        // 2. Standard Role Check for everyone else
        if (!empty($roles) && !in_array($userRole, $roles)) {
            abort(403, 'Unauthorized action. Your role (' . $userRole . ') does not have access to this module.');
        }

        return $next($request);
    }
}
