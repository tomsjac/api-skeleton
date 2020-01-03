<?php
/**
 * Declaration of routes
 *
 * @author  thomas
 * @version 1.0.0
 */
//Root
$app->any('/', \App\controller\Home::class . ':index');
$app->any('/indexTemplate', \App\controller\Home::class . ':indexTemplate');

/**
 * Declaration of routes depending on the version of the app
 */
/** @var \App\CallableResolver $routeVersion */
$routeVersion = \App\CallableResolver::addRoute($app)->withVersionGroup(substr(VERSION, 0, 3));

//Authentification
$routeVersion->withGroup('auth')->withNamespace('\\App\\controller\\auth')->load();

//your statements
//...
//Route défault Version
$routeVersion->withGroup(null)->withNamespace('\\App\\controller')->load();


/**
 * Declaration Console
 */
$app->group('/cmd', function () use ($app) {
    $app->get('/example', \App\controller\console\Example::class . ':index')->setName('example');
});


//Debug route
/*
var_dump($app->getContainer()->get('router')->getRoutes());
die();
*/