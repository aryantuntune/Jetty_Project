<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Console\Scheduling\Schedule;


return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // Add/keep any global middleware here if you need

        // Register route middleware aliases here
        $middleware->alias([
            'role' => \App\Http\Middleware\Role::class,
             'blockRole5' => \App\Http\Middleware\BlockRole5::class,
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



    