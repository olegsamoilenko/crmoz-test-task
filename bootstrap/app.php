<?php

use App\Http\Middleware\HandleAppearance;
use App\Http\Middleware\HandleInertiaRequests;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Middleware\AddLinkHeadersForPreloadedAssets;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Inertia\Inertia;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->encryptCookies(except: ['appearance', 'sidebar_state']);

        $middleware->web(append: [
            HandleAppearance::class,
            HandleInertiaRequests::class,
            AddLinkHeadersForPreloadedAssets::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->respond(function (Response $response, Throwable $exception, Request $request) {
            $isServerError = in_array($response->getStatusCode(), [500, 503], true);
            $isInertia = $request->headers->get('X-Inertia') === 'true';
            if ($isServerError && $isInertia) {
                $errorMessage = 'An internal error occurred, please try again. If the problem persists, please contact support.';
                if (app()->isLocal()) {
                    $errorMessage .= sprintf("\n%s: %s", get_class($exception), $exception->getMessage());
                }
                return response()->json([
                    'error_message' => $errorMessage,
                ], $response->getStatusCode());
            }

            if ($response->getStatusCode() === 419) {
                return back()->with([
                    'flash.banner' => 'The page expired, please try again.',
                ]);
            }

            return $response;
        });
    })->create();
