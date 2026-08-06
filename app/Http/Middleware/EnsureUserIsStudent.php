<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class EnsureUserIsStudent
{
    public function handle(Request $request, Closure $next)
    {
        if (!auth()->check() || !auth()->user()->isStudent()) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Unauthorized'], 403);
            }
            return redirect()->route('student.login');
        }

        return $next($request);
    }
}
