<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Console\Scheduling\Schedule;


return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // Add/keep any global middleware here if you need
        $middleware->api(prepend: [
            \Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful::class,
        ]);
        // Register route middleware aliases here
        $middleware->alias([
            'role' => \App\Http\Middleware\Role::class,
            'blockRole5' => \App\Http\Middleware\BlockRole5::class,
            'auth' => \App\Http\Middleware\Authenticate::class,
            'customer.guest' => \App\Http\Middleware\RedirectIfCustomerAuthenticated::class,
            'admin.guest' => \App\Http\Middleware\RedirectIfAdminAuthenticated::class,
            'guest' => \App\Http\Middleware\RedirectIfAuthenticated::class,
            // keep other aliases if you have them
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //

    })

    ->withSchedule(function (Schedule $schedule) {
        // Run daily at 23:59 IST
        $schedule->command('reports:send-daily-tickets')
            ->dailyAt('23:59')
            ->timezone('Asia/Kolkata')
            ->withoutOverlapping()
            ->onOneServer();
    })->create();
