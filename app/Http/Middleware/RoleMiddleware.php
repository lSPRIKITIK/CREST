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
            $home = $this->homeFor($userRole);

            // Avoid a redirect loop if the user is blocked from their own home page
            if ($home === null || $request->url() === $home) {
                abort(403, 'Unauthorized action. Your role (' . $userRole . ') does not have access to this module.');
            }

            return redirect($home)->with('error', 'You do not have access to that page.');
        }

        return $next($request);
    }

    private function homeFor(?string $role): ?string
    {
        return match ($role) {
            'Production Manager', 'Craftsmen' => route('production.index'),
            'Delivery Personnel'              => route('delivery.index'),
            'Sales & Record Personnel'        => route('dashboard'),
            default                           => null,
        };
    }
}
