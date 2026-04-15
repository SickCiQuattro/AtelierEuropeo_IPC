<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'admin' => App\Http\Middleware\isAdminMiddleware::class,
            'isAdmin' => App\Http\Middleware\isAdminMiddleware::class,
            'isRegisteredUser' => App\Http\Middleware\isRegisteredUserMiddleware::class,
            'checkProjectAccess' => App\Http\Middleware\CheckProjectStatusAccess::class,
            'setLocale' => App\Http\Middleware\SetLocale::class,
        ]);
        
        // Applica il middleware di localizzazione a tutte le route web
        $middleware->web(append: [
            App\Http\Middleware\SetLocale::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
