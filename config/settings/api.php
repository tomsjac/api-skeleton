<?php
/*
 * Configuration file for API
 *
 * @author Thomas
 * @version 1.0.0
 */
return [
    /**
    * WhiteList For rate limit
    */
    'rateLimit' => [
        'whiteList' => env_array('RATELIMIT_WHITELIST'),
    ],

    /**
     * JWT configuration Token
     */
    'jwt' => [
        /**
         * Path protected by a token or not
         */
        'path' => env_array('TOKEN_PATH_PROTECTED', '\\'),
        'ignore' => env_array('TOKEN_PATH_NOPROTECTED'),
        /**
         * Configuration Token
         */
        'secure' => env('TOKEN_HTTPS',true),
        'secret' => env('TOKEN_KEY_SECRET',base64_encode(random_bytes(10))),
        'expire' => env('TOKEN_EXPIRE',3600),
    ],

    /**
     * CORS Configuration
     */
    'cors' => [
        "origin" => env_array('CORS_ORIGIN'),
        "methods" => ["GET", "POST", "PUT", "DELETE"],
        "headers.allow" => ["Authorization", "Origin", "X-Requested-With", "If-Match", "If-Unmodified-Since"],
        "headers.expose" => ["Etag"],
        "credentials" => env('CORS_CREDENTIALS',true),
        "cache" => env('CORS_CACHE',0),
    ]
];
