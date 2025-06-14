<?php

use App\Exceptions\ApiExceptionHandler;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        api: __DIR__ . '/../routes/api.php',
        web: __DIR__ . '/../routes/web.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',

    )
    ->withMiddleware(function (Middleware $middleware) {
        //
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->render(function (AuthenticationException $e, Request $request) {
            if (in_array('sanctum', $e->guards()))
                return response()->json([
                    'error' => 'Unauthenticated',
                    'message' => 'Token is invalid or missing.',
                ]);

            return null;
        });
        $exceptions->render(function (Throwable $e) {
            return ApiExceptionHandler::handle($e);
        });
    })->create();
