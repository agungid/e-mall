<?php

namespace App\Exceptions;

use App\Services\ResponseService;
use BadMethodCallException;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Exception\RouteNotFoundException;
use Throwable;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\UserNotDefinedException;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<Throwable>>
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     *
     * @return void
     */
    public function register()
    {
        $this->renderable(function (NotFoundHttpException $e) {
            return ResponseService::toJson(
                false,
                'The page are looking was not found', 404
            );
        });

        $this->renderable(function (BadMethodCallException $e) {
            return ResponseService::toJson(
                false,
                $e->getMessage()
            );
        });

        $this->renderable(function (BindingResolutionException $e) {
            return ResponseService::toJson(
                false,
                $e->getMessage()
            );
        });

        $this->renderable(function (RouteNotFoundException $e) {
            return ResponseService::toJson(
                false,
                $e->getMessage(), 401
            );
        });

        $this->renderable(function(UserNotDefinedException $e) {
            return ResponseService::toJson(
                false,
                'Please login before', 401
            );
        });

        $this->renderable(function(JWTException $e) {
            return ResponseService::toJson(
                false,
                $e->getMessage(), 401
            );
        });

        $this->renderable(function(QueryException $e) {
            return ResponseService::toJson(
                false,
                $e->getMessage()
            );
        });

        $this->renderable(function(MethodNotAllowedHttpException $e) {
            return ResponseService::toJson(
                false,
                $e->getMessage(), 405
            );
        });
    }
}
