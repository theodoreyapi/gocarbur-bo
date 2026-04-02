<?php

namespace App\Exceptions;

use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\TooManyRequestsHttpException;
use Throwable;

class Handler extends ExceptionHandler
{
    protected $dontFlash = ['current_password', 'password', 'password_confirmation'];

    /**
     * Retourner toutes les erreurs en JSON pour les routes API
     */
    public function render($request, Throwable $e): mixed
    {
        if ($request->is('api/*') || $request->expectsJson()) {
            return $this->handleApiException($request, $e);
        }

        return parent::render($request, $e);
    }

    private function handleApiException(Request $request, Throwable $e): JsonResponse
    {
        // Erreur de validation
        if ($e instanceof ValidationException) {
            return response()->json([
                'success' => false,
                'message' => 'Données invalides.',
                'errors'  => $e->errors(),
            ], 422);
        }

        // Non authentifié
        if ($e instanceof AuthenticationException) {
            return response()->json([
                'success' => false,
                'message' => 'Non authentifié. Veuillez vous connecter.',
            ], 401);
        }

        // Accès refusé
        if ($e instanceof AccessDeniedHttpException) {
            return response()->json([
                'success' => false,
                'message' => 'Accès refusé.',
            ], 403);
        }

        // Ressource introuvable (Model ou route)
        if ($e instanceof ModelNotFoundException || $e instanceof NotFoundHttpException) {
            $model   = $e instanceof ModelNotFoundException ? class_basename($e->getModel()) : 'Ressource';
            return response()->json([
                'success' => false,
                'message' => "{$model} introuvable.",
            ], 404);
        }

        // Trop de requêtes (rate limiting)
        if ($e instanceof TooManyRequestsHttpException) {
            $retryAfter = $e->getHeaders()['Retry-After'] ?? 60;
            return response()->json([
                'success'     => false,
                'message'     => 'Trop de requêtes. Réessayez dans quelques secondes.',
                'retry_after' => $retryAfter,
            ], 429);
        }

        // Erreur serveur générique
        $debug = config('app.debug');

        return response()->json([
            'success' => false,
            'message' => $debug ? $e->getMessage() : 'Une erreur inattendue s\'est produite.',
            'trace'   => $debug ? collect($e->getTrace())->take(5)->toArray() : null,
        ], 500);
    }

    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            // Intégration Sentry ou autre service de monitoring
            // if (app()->bound('sentry')) { app('sentry')->captureException($e); }
        });
    }
}
