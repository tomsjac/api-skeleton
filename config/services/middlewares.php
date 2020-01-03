<?php
/**
 * Management of project middlewares
 * Using Features
 *
 * @author Thomas
 * @version 1.0.0
 */

use Slim\Container;

/** @var \Slim\Container $container */
$container = $app->getContainer();


//Check Cache Http, Use in \App\services\dependencies\response\ApiResponse
$app->add(new \Slim\HttpCache\Cache());


//CORS
try {
    $corsMiddleware = new \App\services\middleware\api\Cors($container);
    $app->add($corsMiddleware->getMiddleware());
} catch (Exception $e) {
    return $container->get('apiResponse')
        ->setStatus(500)
        ->setError('Message : ' . $e->getMessage())
        ->write();
}

//Authentification
try {
    $jwtAuthMiddleWare = new \App\services\middleware\api\Jwt($container);
    $app->add($jwtAuthMiddleWare->getMiddleware());
} catch (Exception $e) {
    return $container->get('apiResponse')
        ->setStatus(500)
        ->setError('Message : ' . $e->getMessage())
        ->write();
}

//Negotiation
$app->add(
    new \Gofabian\Negotiation\NegotiationMiddleware([
        'accept' => ['application/json'],
        'accept-encoding' => ['gzip'],
        'accept-charset' => ['utf-8']
    ])
);

//Console Slim
$app->add(new \pavlakis\cli\CliRequest());
