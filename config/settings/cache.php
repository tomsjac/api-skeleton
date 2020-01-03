<?php
/*
 * Configuration file For Cache App
 *
 *
 * @author Thomas
 * @version 1.0.0
 */

return [
    /**
     * Cache HTTP
     * Use in \App\services\dependencies\response\CacheResponse
     * Use in \App\services\tools\ServiceLink
     */
    'cacheHttp' => [
        'useCache' => env('CACHE_HTTP', false),
        'type' => 'public',
        'maxAge' => env('CACHE_HTTP_MAXAGE', 3600),
        'mustRevalidate' => env('CACHE_HTTP_MUSTREVALIDATE', false),
        'expireData' => env('CACHE_HTTP_EXPIRE', 3600),
        'folderCache' => $pathRootSettings . '/storage/cacheHttp'
    ],

];
