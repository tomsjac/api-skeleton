<?php
/**
 * Config APP
 *
 * @author : Thomas
 * @version : 1.0.0
 */

/**
 * Function for array datas in DotEnv
 */
if (!function_exists('env_array')) {
    function env_array(string $key, $default = null)
    {
        return array_filter(explode(',', env($key, $default)), 'strlen');
    }
}

/*
 * Path Root, declaration into Settinds
 */
$pathRootSettings = dirname(__DIR__);

/**
 * Configuration
 */
$settings = [
    'settings' => array_merge(
        [
            'mode' => env('APP_ENV', 'production'),     //'local', 'testing', and 'production'
            'debug' => env('APP_DEBUG', false),
            /**
             * Slim Setting
             * https://www.slimframework.com/docs/v3/objects/application.html#slim-default-settings
             */
            'displayErrorDetails' => env('APP_DEBUG', false),
            'addContentLengthHeader' => false,
        ],
        require $pathRootSettings . '/config/settings/api.php',
        require $pathRootSettings . '/config/settings/app.php',
        require $pathRootSettings . '/config/settings/cache.php',
        require $pathRootSettings . '/config/settings/databases.php',
        require $pathRootSettings . '/config/settings/views.php'
    )
];

//echo json_encode($settings);
return $settings;
