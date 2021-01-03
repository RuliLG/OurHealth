<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class SuperAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if (!auth()->user() || !in_array(auth()->user()->role, ['superadmin'])) {
            if ($request->expectsJson()) {
                return response()->json([
                    'error' => 'Not enough permissions'
                ], 403);
            }

            abort(403);
        }

        return $next($request);
    }
}
