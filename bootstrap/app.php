<?php

use App\Console\Commands\RecalculerPapaCommand;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpException;

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

        // 403 — Accès non autorisé : remplace le message générique
        $exceptions->render(function (AuthorizationException $e, $request) {
            $message = ($e->getMessage() && $e->getMessage() !== 'This action is unauthorized.')
                ? $e->getMessage()
                : 'Vous n\'avez pas les droits requis pour cette action.';

            if ($request->expectsJson()) {
                return response()->json(['message' => $message], 403);
            }

            $httpException = new \Symfony\Component\HttpKernel\Exception\HttpException(403, $message);

            return response()->view('errors.403', ['exception' => $httpException], 403);
        });

        // 404 — Modèle non trouvé : message clair
        $exceptions->render(function (ModelNotFoundException $e, $request) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Ressource introuvable.'], 404);
            }
            abort(404, 'La ressource demandée est introuvable.');
        });

        // 422 — Erreur de validation AJAX
        $exceptions->render(function (ValidationException $e, $request) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Les données soumises sont invalides.',
                    'errors'  => $e->errors(),
                ], 422);
            }
        });

        // Pour les requêtes non-AJAX, rediriger vers une page flash lisible
        // si l'erreur HTTP survient dans un contexte de formulaire
        $exceptions->render(function (HttpException $e, $request) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => $e->getMessage() ?: 'Erreur HTTP ' . $e->getStatusCode(),
                ], $e->getStatusCode());
            }

            // Laisser Laravel utiliser les vues errors/{code}.blade.php
            return null;
        });

    })->create();
