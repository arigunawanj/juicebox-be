<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Response;

class ResponseMacroServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        Response::macro('success', function ($data = [], $message = '', $settings = []) {
            return Response::make([
                'status_code' => 200,
                'data' => $data,
                'message' => $message,
                'settings' => $settings
            ], 200);
        });

        Response::macro('created', function ($data = [], $message = '', $settings = []) {
            return Response::make([
                'status_code' => 201,
                'data' => $data,
                'message' => $message,
                'settings' => $settings
            ], 201);
        });

        Response::macro('noContent', function ($message = '', $settings = []) {
            return Response::make([
                'status_code' => 204,
                'message' => $message,
                'settings' => $settings
            ], 204, []);
        });

        Response::macro('badRequest', function ($error = [], $settings = []) {
            return Response::make([
                'status_code' => 400,
                'errors' => is_array($error) ? $error : [$error],
                'settings' => $settings
            ], 400);
        });

        Response::macro('unauthorized', function ($error = ['Unauthorized'], $settings = []) {
            return Response::make([
                'status_code' => 401,
                'errors' => is_array($error) ? $error : [$error],
                'settings' => $settings
            ], 401);
        });

        Response::macro('forbidden', function ($error = ['Forbidden'], $settings = []) {
            return Response::make([
                'status_code' => 403,
                'errors' => is_array($error) ? $error : [$error],
                'settings' => $settings
            ], 403);
        });

        Response::macro('notFound', function ($error = ['Not Found'], $settings = []) {
            return Response::make([
                'status_code' => 404,
                'errors' => is_array($error) ? $error : [$error],
                'settings' => $settings
            ], 404);
        });

        Response::macro('methodNotAllowed', function ($error = ['Method Not Allowed'], $settings = []) {
            return Response::make([
                'status_code' => 405,
                'errors' => is_array($error) ? $error : [$error],
                'settings' => $settings
            ], 405);
        });

        Response::macro('conflict', function ($error = ['Conflict'], $settings = []) {
            return Response::make([
                'status_code' => 409,
                'errors' => is_array($error) ? $error : [$error],
                'settings' => $settings
            ], 409);
        });

        Response::macro('unprocessableEntity', function ($error = [], $settings = []) {
            if (is_array($error)) {
                $arrError = $error;
            } else {
                $arrError = [];
                $tmpError = (array) $error;
                foreach ($tmpError as $val) {
                    foreach ((array) $val as $v) {
                        if ($v !== ':message') {
                            $arrError[] = $v;
                        }
                    }
                }
            }

            return Response::make([
                'status_code' => 422,
                'errors' => $arrError,
                'settings' => $settings
            ], 422);
        });

        Response::macro('tooManyRequests', function ($error = ['Too Many Requests'], $settings = []) {
            return Response::make([
                'status_code' => 429,
                'errors' => is_array($error) ? $error : [$error],
                'settings' => $settings
            ], 429);
        });

        Response::macro('internalServerError', function ($error = ['Internal Server Error'], $settings = []) {
            return Response::make([
                'status_code' => 500,
                'errors' => is_array($error) ? $error : [$error],
                'settings' => $settings
            ], 500);
        });

        Response::macro('serviceUnavailable', function ($error = ['Service Unavailable'], $settings = []) {
            return Response::make([
                'status_code' => 503,
                'errors' => is_array($error) ? $error : [$error],
                'settings' => $settings
            ], 503);
        });

        // Keep the original failed method for backward compatibility
        Response::macro('failed', function ($error = [], $httpCode = 422, $settings = []) {
            if (is_array($error)) {
                $arrError = $error;
            } else {
                $arrError = [];
                $tmpError = (array) $error;
                foreach ($tmpError as $val) {
                    foreach ((array) $val as $v) {
                        if ($v !== ':message') {
                            $arrError[] = $v;
                        }
                    }
                }
            }

            return Response::make([
                'status_code' => $httpCode,
                'errors' => $arrError,
                'settings' => $settings
            ], $httpCode);
        });
    }
}
