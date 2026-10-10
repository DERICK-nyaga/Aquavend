<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class EnsureUserHasRole
{
    public function handle(Request $request, Closure $next, string $role)
    {
        if (! $request->user() || $request->user()->role !== $role) {
            abort(403, 'You do not have permission to access this page.');
        }

        return $next($request);
    }
}