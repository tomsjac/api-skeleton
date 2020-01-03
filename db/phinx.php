<?php
/**
 * Config file PHINX
 */

/**
 * Composer AutoloAd
 */
require_once dirname(__DIR__).'/vendor/autoload.php';

/**
 * Load Dotenv
 *
 */
$dotenv = \Dotenv\Dotenv::createImmutable(dirname(__DIR__));
$dotenv->safeLoad();

/**
 * Generate Conf Phinx
 */
//Folder
$dataPhinxFolder = [
    'migrations' => __DIR__. '/migrations/*',
    'seeds' => __DIR__. '/seeds'
];

//Connection DB
$dataPhinxBdd = [
    'default_migration_table' => 'log_phinx_migrations',
    'default_database' => 'default',
    'default' => [
        'name'          => getenv('DB_DATABASE'),
        'adapter'       => getenv('DB_CONNECTION'),
        'host'          => getenv('DB_HOST'),
        'port'          => getenv('DB_PORT'),
        'user'          => getenv('DB_USERNAME'),
        'pass'          => getenv('DB_PASSWORD')
    ]
];

/**
 * Create folder migration no exist
 */
$pathMigrationClean = str_replace('/*', '', $dataPhinxFolder['migrations']);
$isDirEmpty = !(new \FilesystemIterator($pathMigrationClean))->valid();
if ($isDirEmpty === true) {
    $pathMigrationDefault = str_replace('/*', '/0.0.0', $dataPhinxFolder['migrations']);
    mkdir($pathMigrationDefault, 0770, true);
}

if (!is_dir($dataPhinxFolder['seeds'])) {
    mkdir($dataPhinxFolder['seeds'], 0770, false);
}

/**
 * Return Conf Phinx
 */
return [
    'paths' => $dataPhinxFolder,
    'environments' => $dataPhinxBdd
];
