<?php

use App\Console\Commands\RecalculerPapaCommand;
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
    ->withMiddleware(function (Middleware $middleware): void {
        //
    })
    ->withSchedule(function (Schedule $schedule): void {
        // Recalcul quotidien à 6h du matin (heure de Libreville UTC+1)
        $schedule->command(RecalculerPapaCommand::class, ['--alertes'])
                 ->dailyAt('05:00') // 05:00 UTC = 06:00 WAT
                 ->withoutOverlapping()
                 ->runInBackground()
                 ->appendOutputTo(storage_path('logs/recalcul-papa.log'));

        // Vérification des alertes toutes les heures en heures ouvrables
        $schedule->command(RecalculerPapaCommand::class, ['--alertes', '--queue'])
                 ->hourlyAt(30)
                 ->weekdays()
                 ->between('07:00', '18:00')
                 ->withoutOverlapping();
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
