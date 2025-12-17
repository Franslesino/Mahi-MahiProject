<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use App\Http\Middleware\CheckRole;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        api: __DIR__ . '/../routes/api.php',
        web: __DIR__ . '/../routes/web.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // Register middleware alias
        $middleware->alias([
            'role' => CheckRole::class,
            'nocache' => \App\Http\Middleware\NoCacheHeaders::class,
        ]);

        // Atau gunakan middleware groups
        $middleware->web(append: [
            // tambahkan middleware web di sini jika perlu
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
