<?php
/**
 * Management of project dependencies
 * Using Features
 *
 * @author Thomas
 * @version 1.0.0
 */
use Slim\Container;

$container = $app->getContainer();

//Token JWT : Init
$container['token'] =  function () {
    return new \App\services\tools\auth\TokenJwt();
};

// Logger : Monolog
$container['logger'] = function ($c) {
    return new \App\services\dependencies\Log($c);
};

//Database
$container['database'] = function ($c) {
   return new \App\services\dependencies\DataBase($c);
};

//Template
$container['view'] = function ($c) {
    return new \App\services\dependencies\Views($c);
};

//improve response
$container['apiResponse'] = function ($c) {
    return new \App\services\dependencies\response\ApiResponse($c);
};

//Rate Limit
$container['rateLimit'] = function ($c) {
    return new \App\services\dependencies\RateLimiter($c);
};

// Handlers : Error Slim to JSON
$container["errorHandler"] = function ($c) {
    return new \App\services\dependencies\response\ApiError($c);
};

//Filter Request Data
$container["dataFilter"] =  function ($c) {
    return new \App\services\dependencies\DataFilter($c);
};

