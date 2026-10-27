<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->web(append: [
            \App\Http\Middleware\RequirePair::class,
        ]);
        $middleware->alias([
            'portal.auth' => \App\Http\Middleware\PortalAuth::class,
            'role' => \App\Http\Middleware\RoleMiddleware::class,
        ]);
        $middleware->validateCsrfTokens(except: [
            'payment/*/callback',
            'api/*',
        ]);
        $middleware->throttleApi();
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->render(function (\Throwable $e, \Illuminate\Http\Request $request) {
            if ($request->is('api/*')) {
                $status = $e instanceof \Symfony\Component\HttpKernel\Exception\HttpException
                    ? $e->getStatusCode()
                    : 500;
                if ($status === 500 && ! config('app.debug')) {
                    return response()->json(['success' => false, 'message' => 'Terjadi kesalahan server.'], 500);
                }
            }
            return null;
        });
    })->create();
