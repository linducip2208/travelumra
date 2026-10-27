<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, ...$roles)
    {
        if (! auth()->check()) {
            abort(403);
        }
        if (! in_array(auth()->user()->role, $roles, true)) {
            \Illuminate\Support\Facades\Log::warning('Forbidden role access', [
                'user_id' => auth()->id(),
                'role' => auth()->user()->role,
                'path' => $request->path(),
            ]);
            abort(403, 'Unauthorized.');
        }
        return $next($request);
    }
}
