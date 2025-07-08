<?php

use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Response;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;

class InvalidToken extends ExceptionHandler
{
    public function render($request, Throwable $exception)
    {
        if ($exception instanceof AuthenticationException) {
            return response()->json([
                'succes' => false,
                'message' => 'Unathenticated or invalid token',
                'data' => null,
            ], Response::HTTP_UNAUTHORIZED);
        }
        if ($exception instanceof TokenExpiredException) {
            return response()->json([
                'succes' => false,
                'message' => 'Token has expired',
                'data' => null,
            ], Response::HTTP_UNAUTHORIZED);
        }
        if ($exception instanceof TokenInvalidException) {
            return response()->json([
                'succes' => false,
                'message' => 'Token is invalid',
                'data' => null,
            ], Response::HTTP_UNAUTHORIZED);
        }
        if ($exception instanceof JWTException) {
            return response()->json([
                'succes' => false,
                'message' => 'Token is not provided',
                'data' => null,
            ], Response::HTTP_UNAUTHORIZED);
        }
        return parent::render($request, $exception);
    }
}