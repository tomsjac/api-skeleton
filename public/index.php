<?php
/**
 * Launch APP Api
 * System boot and launch of Slim
 * Over here requests
 *
 * @author : Thomas
 * @version : 1.0.1
 */
define('TIMER_START_APP', microtime(true));

/**
 * Version APP
 */
define('VERSION', '2.0.0');

/**
 * Add bootstrap File
 */
require_once __DIR__.'/../bootstrap/bootstrap.php';

/**
 * INIT SLIM
 */
$settings = require __DIR__.'/../bootstrap/bootSettings.php';
$app             = new \Slim\App($settings);


/**
 * DEPENDANCES / MIDDLEWARE
 */
/**
 * Add Services File
 */
require __DIR__.'/../bootstrap/bootServices.php';

/**
 * Add Routes File
 */
require __DIR__.'/../bootstrap/bootRoutes.php';

/**
 * RUN, Go !
 */
try {
    $app->run();
} catch (\Slim\Exception\MethodNotAllowedException $e) {
    trigger_error($e->getMessage()." on line" .$e->getLine() ." of " .$e->getFile(), E_USER_ERROR);
} catch (\Slim\Exception\NotFoundException $e) {
    trigger_error($e->getMessage()." on line" .$e->getLine() ." of " .$e->getFile(), E_USER_ERROR);
} catch (Exception $e) {
    trigger_error($e->getMessage()." on line" .$e->getLine() ." of " .$e->getFile(), E_USER_ERROR);
}

