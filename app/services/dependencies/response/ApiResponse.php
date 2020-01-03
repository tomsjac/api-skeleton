<?php

namespace App\services\dependencies\response;

use Interop\Container\Exception\ContainerException;
use Slim\Container;
use Slim\Http\Response;

/**
 * Response Management API in JSON
 *
 * @author thomas
 */
class ApiResponse
{
    /**
     * Code status response
     * @var Int
     */
    private $status = 200;

    /**
     * Code Error
     * @var string
     */
    private $codeError = 'other';

    /**
     * Message Error
     * @var string
     */
    private $msgError;

    /**
     * If pagination, returns the information for the next call
     * @var array
     */
    private $links;

    /**
     * Data for the response
     * @var array
     */
    private $data;

    /**
     * Obj Slim container
     * @var Container $container
     */
    private $container;

    /**
     * Obj Slim response
     * @var Response $response
     */
    private $response;

    /**
     * Construct
     * @param Container $container
     * @throws ContainerException
     */
    public function __construct(Container $container)
    {
        $this->container = $container;
        $this->response = $this->container->get('response');
    }

    /**
     * Set Status response
     * @param Int $code
     * @return ApiResponse
     */
    public function setStatus(int $code = 200)
    {
        $this->status = $code;
        return $this;
    }

    /**
     * Error management
     * @param string $msg
     * @param string $code : invalid_auth / param_error / url_error / not_authorized / other
     * @return ApiResponse
     */
    public function setError(string $msg, string $code = 'other')
    {
        $this->codeError = $code;
        $this->msgError = $msg;

        return $this;
    }

    /**
     * Set array Data response
     * @param array $data
     * @return ApiResponse
     */
    public function setData(array $data)
    {
        $this->data = $data;
        return $this;
    }

    /**
     * Set information for the next call
     * @param array $arrayLink
     * @return ApiResponse
     */
    public function setLink(array $arrayLink)
    {
        $this->links = $arrayLink;
        return $this;
    }

    /**
     * Writing preformatted Json
     * @param mixed $data
     * @return Response
     * @throws ContainerException
     */
    public function write($data = null)
    {
        $arrayResponse = ['meta' => [], 'response' => ''];

        $arrayResponse['meta']['code'] = $this->status;
        $arrayResponse['meta']['generate'] = gmdate('D, d M Y H:i:s T');

        if ($this->msgError != null) {
            $arrayResponse['meta']['errorType'] = $this->codeError;
            $arrayResponse['meta']['errorDetail'] = $this->msgError;
        }

        if ($this->links != null) {
            $arrayResponse['meta']['links'] = $this->links;
        }

        if ($this->msgError == null) {
            $arrayResponse['response'] = ($data != null) ? $data : $this->data;
        }

        return $this->getFormatResponse($arrayResponse);
    }

    /**
     * Writing crude Json
     * @param array $data
     * @return Response
     * @throws ContainerException
     */
    public function writeRaw($data = null)
    {
        return $this->getFormatResponse(($data != null) ? $data : $this->data);
    }

    /**
     * Response Json formatting
     * @param array $arrayData
     * @return Response
     * @throws ContainerException
     */
    private function getFormatResponse(array $arrayData = null)
    {
        //Data
        $dataJson = json_encode($arrayData, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);

        //cache Data
        $this->getCacheResponse($dataJson);

        return $this->response
            ->withStatus($this->status)
            ->withHeader("Content-Type", "application/json")
            ->write($dataJson);
    }

    /**
     * Management HTTP cache
     * @param string $dataEtag
     * @return Void
     * @throws ContainerException
     */
    private function getCacheResponse(string $dataEtag)
    {
        //Settings For cache
        $settings = $this->container->get('settings');
        $settingCache = $settings['cacheHttp'];

        //Active Cache
        if ($settingCache['useCache'] == true && $this->msgError == null) {
            $cacheHttp = new CacheResponse($this->response);

            $cacheHttp
                ->setType($settingCache['type'])
                ->setMustRevalidate($settingCache['mustRevalidate'])
                ->setMaxAge($settingCache['maxAge'])
                ->setExpire($settingCache['expireData'])
                ->setData($dataEtag);

            $this->response = $cacheHttp->activeCache();
        }
    }
}
