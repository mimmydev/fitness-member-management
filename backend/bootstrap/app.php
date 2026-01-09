<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        //
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // Handle API exceptions with consistent JSON responses
        $exceptions->render(function (Throwable $e, $request) {
            // Only handle API requests
            if (!$request->is('api/*')) {
                return null;
            }

            $statusCode = 500;
            $message = 'An error occurred while processing your request.';
            $errors = null;

            // Validation errors (422)
            if ($e instanceof \Illuminate\Validation\ValidationException) {
                $statusCode = 422;
                $message = 'The given data was invalid.';
                $errors = $e->errors();
            }
            // Model not found (404)
            elseif ($e instanceof \Illuminate\Database\Eloquent\ModelNotFoundException) {
                $statusCode = 404;
                $message = 'Resource not found.';
            }
            // Authentication errors (401)
            elseif ($e instanceof \Illuminate\Auth\AuthenticationException) {
                $statusCode = 401;
                $message = 'Unauthenticated.';
            }
            // HTTP exceptions (various)
            elseif ($e instanceof \Symfony\Component\HttpKernel\Exception\HttpException) {
                $statusCode = $e->getStatusCode();
                $message = $e->getMessage() ?: $message;
            }
            // Development environment: show actual error message
            elseif (config('app.debug')) {
                $message = $e->getMessage();
                $errors = [
                    'exception' => get_class($e),
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                    'trace' => collect($e->getTrace())->take(5)->toArray(),
                ];
            }

            return response()->json([
                'message' => $message,
                'errors' => $errors,
            ], $statusCode);
        });
    })->create();
