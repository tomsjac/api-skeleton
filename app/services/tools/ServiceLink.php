<?php

namespace App\services\tools;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Psr7\Response;
use Kevinrob\GuzzleCache\CacheMiddleware;
use League\Flysystem\Adapter\Local;
use Kevinrob\GuzzleCache\Strategy\PrivateCacheStrategy;
use Kevinrob\GuzzleCache\Storage\FlysystemStorage;
use Monolog\Logger;
use Psr\Log\LoggerInterface;

/**
 * Links container for calls to web services and external API
 * @uses guzzle/guzzle https://github.com/guzzle/guzzle
 * @author thomas
 */
class ServiceLink
{
    /**
     * link Service
     * @var array
     */
    public static $arrLink;

    /**
     * Configuration App
     * @var array
     */
    public static $configApp;

    /**
     * Monolog
     * @var Logger
     */
    public static $logger;

    /**
     * Header Cache Guzzle
     * @var HandlerStack
     */
    public static $stackCache;

    /**
     * Default Option For Guzzle
     * @var array
     */
    public static $defaultOption = [
        'allow_redirects' => false,
        'timeout' => '2.0',
        'headers' => [
            'Accept-Encoding' => 'gzip',
            'Accept' => 'application/json',
            'Cache-Control' => 'public, max-age=60', //Reload cache all X seconds
            'Pragma' => 'cache',
        ]
    ];

    /**
     * Set Repsonse Api
     * @param LoggerInterface $logger
     */
    public static function setLogger(LoggerInterface $logger)
    {
        self::$logger = $logger;
    }

    /**
     * Set Conf App
     * @param array $data
     */
    public static function setConfApp(array $data)
    {
        self::$configApp = $data;
    }

    /**
     * Return Link
     * @param string $key Key seperate with ::
     * @param array|null $tagReplace
     * @return string|null
     */
    public static function get(string $key, ?array $tagReplace = null)
    {
        if (isset(self::$arrLink[$key])) {
            $link = self::$arrLink[$key];
            if ($tagReplace != null && is_array($tagReplace) == true) {
                $keys = array_keys($tagReplace);
                $values = array_values($tagReplace);
                $link = str_replace($keys, $values, $link);
            }
            return $link;
        }

        return null;
    }

    /**
     * Set Link
     * @param string $key Key seperate with ::
     * @param string $link
     */
    public static function setList(string $key, string $link)
    {
        self::$arrLink[$key] = $link;
    }

    /**
     * Call WebService/Api and Return Class Response
     * @param string $url Url of call
     * @param string $method POST / GET / PUT ...
     * @param array $data
     * @param array $options Option For guzzle
     * @return Response
     * @throws GuzzleException
     */
    public static function callWithResponse(string $url, string $method = 'GET', array $data = [], array $options = [])
    {
        //Init Guzzle
        $optionsGuzzle = array_replace_recursive(self::$defaultOption, $options);
        $optionsGuzzle['handler'] = self::getStackCache();
        $client = new Client($optionsGuzzle);

        //Sent data according to method
        if (in_array(strtoupper($method), ['GET', 'HEAD', 'OPTIONS']) == true) {
            $dataSent = ['query' => $data];
        } else {
            $dataSent = ['form_params' => $data];
        }

        return $client->request($method, $url, $dataSent);
    }

    /**
     * Call WebService/Api and Return Just data JSON
     *
     * @param string $url Url of call
     * @param string $method POST / GET / PUT ...
     * @param array $data
     * @param array $options Option For guzzle
     * @return string|bool
     * @throws GuzzleException
     */
    public static function callWithData(string $url, string $method = 'GET', array $data = [], array $options = [])
    {
        try {
            $response = self::callWithResponse($url, $method, $data, $options);
        } catch (RequestException  $e) {
            if (is_null($e->getResponse()) == true) {
                if (is_null(self::$logger) == false) {
                    self::$logger->emergency($e->getMessage() . " - URL : " . $url . " - Data : " . json_encode($data));
                }
                return null;
            }
            return $e->getResponse()->getBody()->getContents();
        }
        return $response->getBody()->getContents();
    }

    /**
     * Call WebService/Api and Return Data Json with Meta
     *
     * @param string $url Url of call
     * @param string $method POST / GET / PUT ...
     * @param array $data
     * @param array $options Option For guzzle
     * @return string|bool
     * @throws GuzzleException
     */
    public static function callWithDataAndMeta(string $url, string $method = 'GET', array $data = [], array $options = [])
    {
        try {
            $response = self::callWithResponse($url, $method, $data, $options);
            $contentJson = $response->getBody()->getContents();
            $code = $response->getStatusCode();
        } catch (RequestException  $e) {
            if (is_null($e->getResponse()) == true) {
                if (is_null(self::$logger) == false) {
                    self::$logger->emergency($e->getMessage());
                }
                return null;
            }
            $code = $e->getResponse()->getStatusCode();
            $contentJson = $e->getResponse()->getBody()->getContents();
        }

        $arrayResponse = ['meta' => [], 'response' => ''];
        $arrayResponse['meta']['code'] = $code;
        $arrayResponse['meta']['generate'] = gmdate('D, d M Y H:i:s T');
        $arrayResponse['response'] = json_decode($contentJson, true);

        return json_encode($arrayResponse);
    }


    /**
     * Create Cache and Return
     * @return HandlerStack
     */
    protected static function getStackCache()
    {
        if (self::$stackCache == null) {
            //Folder save Cache response
            if (isset(self::$configApp['cacheHttp']) && isset(self::$configApp['cacheHttp']['folderCache'])) {
                $folderCache = self::$configApp['cacheHttp']['folderCache'];
            } else {
                $folderCache = __DIR__ . '/../../storage/cacheHttp';
            }
            //Create folder
            if (is_dir($folderCache) == false) {
                mkdir($folderCache, 0777, true);
                chmod($folderCache, 0777);
            }

            $localStorage = new Local($folderCache);
            $systemStorage = new FlysystemStorage($localStorage);
            $cacheStrategy = new PrivateCacheStrategy($systemStorage);

            $stack = HandlerStack::create();
            $stack->push(new CacheMiddleware($cacheStrategy), 'cache');
            self::$stackCache = $stack;
        }
        return self::$stackCache;
    }
}
