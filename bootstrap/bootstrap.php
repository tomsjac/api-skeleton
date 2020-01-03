<?php
/**
 * BOOSTRAP API
 *
 * @author : Thomas
 * @version : 1.0.0
 */
// To help the built-in PHP dev server, check if the request was actually for
// something which should probably be served as a static file
if (PHP_SAPI === 'cli-server' && $_SERVER['SCRIPT_FILENAME'] !== __FILE__) {
    return false;
}

/**
 * AUTOLOAD
 */
#PATH : loading dotenv functions first of illuminate, bug with CakePhp
#@see https://github.com/cakephp/cakephp/issues/12669
//require __DIR__.'/../vendor/illuminate/support/helpers.php';
require __DIR__.'/../vendor/autoload.php';


/**
 * DOTENV LOAD
 */
$dotenv = \Dotenv\Dotenv::createImmutable(dirname(__DIR__));
$dotenv->load();

/**
 * CONFIG
 */
$environement = env("APP_ENV", 'production');
switch ($environement) {
    case 'local': //Dev
        error_reporting(-1);
        ini_set('display_errors', 1);
        ini_set('opcache.enable', '0');
        break;

    case 'testing':     // Preprod
    case 'production':  //Prod
        error_reporting(E_ALL ^ E_NOTICE ^ E_STRICT);
        ini_set('display_errors', 0);
        break;

    default:
        header('HTTP/1.1 503 Service Unavailable.', true, 503);
        exit('The application environment is not set correctly.');
}
