<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class EnsureUserIsStudent
{
    public function handle(Request $request, Closure $next)
    {
        if (! auth()->check()) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Unauthorized'], 401);
            }

            return redirect()->route('student.login');
        }

        if (! auth()->user()->isStudent()) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Unauthorized. Student access required.'], 403);
            }

            return redirect()->route('admin.dashboard');
        }

        return $next($request);
    }
}
