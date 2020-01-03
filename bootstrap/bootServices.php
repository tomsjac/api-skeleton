<?php
/**
 * Load Service for APP
 *
 * @author Thomas
 * @version 1.0.0
 */

if (is_null($app)) {
    trigger_error('The variable $app is not set, please init Slim', E_USER_ERROR);
}

/**
 * DEPENDANCES / MIDDLEWARE
 */

/**
 * Load dependencies App
 */
require __DIR__.'/../config/services/dependencies.php';

/**
 * Load MiddleWare App
 */
require __DIR__.'/../config/services/middlewares.php';

/**
 * Link Service
 */
require __DIR__.'/../config/serviceLink.php';

