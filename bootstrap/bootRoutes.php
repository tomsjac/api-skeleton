<?php
/**
 * Routes API
 *
 * @author : Thomas
 * @version : 1.0.0
 */

if (is_null($app)) {
    trigger_error('The variable $app is not set, please init Slim', E_USER_ERROR);
}

/**
 * ROUTES
 */
require __DIR__.'/../config/routes.php';
