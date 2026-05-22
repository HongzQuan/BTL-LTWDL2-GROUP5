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
        // THÊM ĐOẠN KHAI BÁO ALIAS NÀY VÀO
        $middleware->alias([
            'admin' => \App\Http\Middleware\AdminMiddleware::class,
            'booking.owner' => \App\Http\Middleware\CheckBookingOwner::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();