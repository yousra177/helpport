<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, ...$roles)
    {
        $user = Auth::user();

        if (!$user) {
            Log::warning('Unauthorized access attempt: User not authenticated', [
                'ip' => $request->ip(),
                'url' => $request->fullUrl()
            ]);
            abort(403, 'Unauthorized: User not authenticated.');
        }

        // Ensure the user has one of the required roles
        if (!in_array($user->role, $roles, true)) {
            Log::warning('Unauthorized access attempt: Insufficient role permissions', [
                'user_id' => $user->id,
                'user_role' => $user->role,
                'allowed_roles' => $roles,
                'ip' => $request->ip(),
                'url' => $request->fullUrl()
            ]);
            abort(403, 'Unauthorized: Insufficient role permissions.');
        }

        return $next($request);
    }
}
