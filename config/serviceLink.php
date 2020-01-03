<?php
/**
 * conf file for external call links
 * @uses With \App\services\tools\ServiceLink
 * @author thomas
 */


/** @var \Slim\Collection $settings */
$settings = $app->getContainer()->get('settings');

//Set contenair & settings
\App\services\tools\ServiceLink::setConfApp($settings->all());
\App\services\tools\ServiceLink::setLogger($app->getContainer()->get('logger'));


/**
 * example with twitter
 */
$urlTwitter = 'https://api.twitter.com/1.1/';

//GET
\App\services\tools\ServiceLink::setList(
    'twitter::lists', $urlTwitter.'lists/list.json?screen_name=[screen_name]'
);
/*
 * Call :
 * $url = \App\services\tools\ServiceLink::get('twitter::lists',
 *   array(
 *      '[screen_name]' => 'phpfig'
 *   )
 * );
 * \App\services\tools\ServiceLink::call($url, 'GET');
 */
