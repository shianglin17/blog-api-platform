<?php

use App\Http\Responses\ApiResponse;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

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
        $exceptions->render(function (AccessDeniedHttpException $e, Request $request) {
            if ($request->is('api/*')) {
                return ApiResponse::error(
                    message: 'Access denied',
                    httpStatus: 403,
                    code: '0403'
                );
            }
        });

        $exceptions->render(function (ValidationException $e, Request $request) {
            if ($request->is('api/*')) {
                return ApiResponse::error(
                    message: $e->getMessage(),
                    httpStatus: 422,
                    code: '0422'
                );
            }
        });

        $exceptions->render(function (\Throwable $e, Request $request) {
            if ($request->is('api/*')) {
                return ApiResponse::error(
                    message: $e->getMessage(),
                    httpStatus: 500,
                    code: '0500'
                );
            }
        });
    })->create();
