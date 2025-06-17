<?php

namespace App\Exceptions;

use Throwable;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Illuminate\Auth\AuthenticationException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;


class ApiExceptionHandler
{
    public static function handle(Throwable $e)
    {
        if ($e instanceof ValidationException) {
            return response()->json([
                'status' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors(),
            ], 422);
        }


        if ($e instanceof ModelNotFoundException) {
            return response()->json([
                'status' => false,
                'message' => 'The requested resource was not found.',
            ], 404);
        }

        if ($e instanceof NotFoundHttpException) {
            return response()->json([
                'status' => false,
                'message' => 'The requested endpoint does not exist.',
            ], 404);
        }

        if ($e instanceof QueryException) {
            return response()->json([
                'status' => false,
                'message' => 'SQL Error: ' . $e->getMessage(),
                'query' => $e->getSql(),
                'bindings' => $e->getBindings(),
            ], 500);
        }
        if ($e instanceof AuthenticationException) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage() ?? 'Unauthenticated',
            ], 401);
        }
        if ($e instanceof AccessDeniedHttpException) {
            return response()->json([
                'status' => false,
                'message' => 'Unauthorized',
            ], 403);
        }
        return response()->json([
            'status' => false,
            'type' => get_class($e),
            'message' => $e->getMessage(),
            'file' => $e->getFile(),
            'line' => $e->getLine(),
        ], 500);
    }

}
