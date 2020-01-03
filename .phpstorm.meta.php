<?php
/**
 * SLIM : Services Container
 * Config\services\dependencies
 */

namespace PHPSTORM_META {
    override(\Slim\Container::get(0), map([
        '' => '@',
        //Framework
        'environment' => \Slim\Http\Environment::class,
        'request' => \Slim\Http\Request::class,
        'response' => \Slim\Http\Response::class,
        'router' => \Slim\Router::class,
        'foundHandler' => \Slim\Handlers\NotFound::class,
        'phpErrorHandler' => \Slim\Handlers\PhpError::class,
        'errorHandler' => \Slim\Handlers\Error::class,
        'notFoundHandler' => \Slim\Handlers\NotFound::class,
        'notAllowedHandler' => \Slim\Handlers\NotAllowed::class,
        'callableResolver' => \Slim\CallableResolver::class,

        //App
        'token' => \App\services\tools\auth\TokenJwt::class,
        'logger' => \App\services\dependencies\Log::class,
        'database' => \App\services\dependencies\DataBase::class,
        'view' => \App\services\dependencies\Views::class,
        'apiResponse' => \App\services\dependencies\response\ApiResponse::class,
        'rateLimit' => \App\services\dependencies\RateLimiter::class,
        'errorHandler' => \App\services\dependencies\response\ApiError::class,
        'dataFilter' => \App\services\dependencies\DataFilter::class
    ]));

    override(\App\services\traits\Container::getContainer(0), map([
        '' => '@',
        //Framework
        'environment' => \Slim\Http\Environment::class,
        'request' => \Slim\Http\Request::class,
        'response' => \Slim\Http\Response::class,
        'router' => \Slim\Router::class,
        'foundHandler' => \Slim\Handlers\NotFound::class,
        'phpErrorHandler' => \Slim\Handlers\PhpError::class,
        'errorHandler' => \Slim\Handlers\Error::class,
        'notFoundHandler' => \Slim\Handlers\NotFound::class,
        'notAllowedHandler' => \Slim\Handlers\NotAllowed::class,
        'callableResolver' => \Slim\CallableResolver::class,

        //App
        'token' => \App\services\tools\auth\TokenJwt::class,
        'logger' => \App\services\dependencies\Log::class,
        'database' => \App\services\dependencies\DataBase::class,
        'view' => \App\services\dependencies\Views::class,
        'apiResponse' => \App\services\dependencies\response\ApiResponse::class,
        'rateLimit' => \App\services\dependencies\RateLimiter::class,
        'errorHandler' => \App\services\dependencies\response\ApiError::class,
        'dataFilter' => \App\services\dependencies\DataFilter::class
    ]));
}