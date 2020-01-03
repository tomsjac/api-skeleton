<?php
/*
 * Configuration For Views with Twig
 *
 * @author Thomas
 * @version 1.0.0
 */

return [
    /**
     * Views Conf
     */
    'views' => [
        'path' => $pathRootSettings.'/app/templates',
        'cache' => empty(env('TEMPLATE_CACHE')) ? false : $pathRootSettings.'/storage/templates',
    ]
];
