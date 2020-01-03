<?php
/*
 * Configuration file For Database
 *
 * @author Thomas
 * @version 1.0.0
 */

return [
    /**
     * DataBase Conf
     */
    'database' => [
        // Default DataBase, if multi db
        'default' => 'database_one',
        'connections' => [
            'database_one' => [
                'driver' => env('DB_CONNECTION'),
                'host' => env('DB_HOST'),
                'port' => env('DB_PORT', '3306'),
                'database' => env('DB_DATABASE'),
                'username' => env('DB_USERNAME'),
                'password' => env('DB_PASSWORD'),
                'charset' => 'utf8',
                'collation' => 'utf8_unicode_ci',
                'prefix' => ''
            ]
        ]
    ]
];
