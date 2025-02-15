<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        apiPrefix:'hrms',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'AuthCheck' => \App\Http\Middleware\AuthCheck::class,
            'verify_token' => \App\Http\Middleware\VerifyCsrfToken::class,
            'CheckLoginTime' => \App\Http\Middleware\CheckLoginTime::class,
            'CheckDevice' => \App\Http\Middleware\CheckDevice::class,
            'admin' => \App\Http\Middleware\AdminAuth::class,
            'hr' => \App\Http\Middleware\HrAuth::class,
            'api.auth' => \App\Http\Middleware\ApiPasswordMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
