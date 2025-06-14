<?php

namespace App\Exceptions;

use Throwable;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Auth\AuthenticationException;

class Handler extends ExceptionHandler
{
    protected function unauthenticated($request, AuthenticationException $exception)
    {
        return response()->json([
            'status' => false,
            'message' => 'Unauthenticated.',
        ], 401);
    }

    public function render($request, Throwable $e)
    {
        return ApiExceptionHandler::handle($e);
    }
}
