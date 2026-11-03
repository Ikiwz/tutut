<?php

use App\Http\Middleware\PreventBackForwardCache;
use App\Http\Middleware\SecurityHeaders;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Exceptions\ThrottleRequestsException;
use Illuminate\Http\Request;
use Illuminate\Session\TokenMismatchException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Trust reverse proxies (Hostinger / Cloudflare / Nginx load balancers)
        $middleware->trustProxies(at: '*');

        // Add standard security headers
        $middleware->append(SecurityHeaders::class);

        // Alias custom middleware
        $middleware->alias([
            'prevent.back-cache' => PreventBackForwardCache::class,
        ]);

        // Redirect unauthenticated guests accessing protected routes
        $middleware->redirectGuestsTo(function (Request $request) {
            if ($request->is('admin*')) {
                return route('admin.login');
            }

            return route('student.login');
        });

        // Redirect authenticated users accessing guest-only routes (e.g. login, register)
        $middleware->redirectUsersTo(function (Request $request) {
            if (auth()->check() && auth()->user()->isAdmin()) {
                return route('admin.dashboard');
            }

            return route('student.dashboard');
        });
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*'),
        );

        // Graceful handling of CSRF session expiration (419) with friendly toast
        $exceptions->render(function (TokenMismatchException $e, Request $request) {
            return back()->with('error', 'Your session has expired. Please try again.');
        });

        // Graceful handling of Rate Limit / Throttling exceptions (429) with friendly toast
        $exceptions->render(function (ThrottleRequestsException $e, Request $request) {
            $retryAfter = $e->getHeaders()['Retry-After'] ?? 60;

            return back()->with('error', "Too many requests. Please wait {$retryAfter} seconds before trying again.");
        });
    })->create();
