<?php

use App\Http\Middleware\AdminMiddleware;
use App\Http\Middleware\EnsureAccountIsActive;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )

    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->trustProxies(at: '*');

        // Định tuyến lại khi đã đăng nhập: user thường về trang chủ, admin về dashboard
        $middleware->redirectUsersTo(function (Request $request) {
            if ($request->user() && $request->user()->isAdmin()) {
                return route('admin.dashboard');
            }

            return route('home');
        });

        $middleware->alias([
            'admin' => AdminMiddleware::class,
        ]);

        $middleware->appendToGroup('web', [
            EnsureAccountIsActive::class,
        ]);
    })

    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*') || $request->expectsJson(),
        );
    })->create();
