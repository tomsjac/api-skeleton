<?php
/*
 * Configuration for App
 *
 * @author Thomas
 * @version 1.0.0
 */

return [
    /**
     * Monolog Settings
     */
    'logger' => [
        'name' => env('APP_NAME', ''),
        'level' => \Monolog\Logger::toMonologLevel(env('LOG_LEVEL', 'INFO')),
        'path' => $pathRootSettings . '/storage/logs/' . date('Y-m-d') . '.log',
        'mailLog' => env('LOG_MAIL', ''),
        'levelMail' => \Monolog\Logger::toMonologLevel(env('LOG_MAIL_LEVEL', '')),
    ],

    /**
     * Query Separator, Request Data
     * @example auth/jwt/token/id_xxx/secret_xxx OR auth/jwt/token/id::xxx/secret::xxx
     */
    'requestParamSeparator' => env('REQUEST_PARAM_SEPARATOR', '::')
];