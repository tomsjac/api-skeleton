<?php

namespace App\services\dependencies\response;

use Slim\Http\Response;
use Slim\HttpCache\CacheProvider;

/**
 * Managing HTTP caching for response
 * @author thomas
 */
class CacheResponse
{
    /**
     * Object CacheProvider
     * @var CacheProvider
     */
    protected $cacheProvider;

    /**
     * @var Response
     */
    protected $response;

    /**
     * Type cache
     * @var string
     */
    protected $type;

    /**
     * @var int
     */
    protected $maxAge;

    /**
     * @var bool
     */
    protected $mustRevalidate;

    /**
     * @var int
     */
    protected $expire;

    /**
     * @var string
     */
    protected $dataResponse;

    /**
     * Construct
     * @param Response $response
     */
    public function __construct(Response $response)
    {
        $this->cacheProvider = new CacheProvider();
        $this->response = $response;
    }

    /**
     * Set Type cache
     * @param string $type
     * @return CacheResponse
     */
    public function setType(string $type = 'public')
    {
        $this->type = $type;
        return $this;
    }

    /**
     * Set Max Age For cache
     * @param int $age
     * @return CacheResponse
     */
    public function setMaxAge(int $age = 86400)
    {
        $this->maxAge = $age;
        return $this;
    }

    /**
     * Set Must Revalidate Cache
     * @param bool $revalidate
     * @return CacheResponse
     */
    public function setMustRevalidate(bool $revalidate = false)
    {
        $this->mustRevalidate = $revalidate;
        return $this;
    }

    /**
     * Set Expire Data
     * @param int $expire
     * @return CacheResponse
     */
    public function setExpire(int $expire = 3600)
    {
        $this->expire = $expire;
        return $this;
    }

    /**
     * Set Data Response for Etag (Format Json)
     * @param string $data
     * @return CacheResponse
     */
    public function setData(string $data)
    {
        $this->dataResponse = $data;
        return $this;
    }

    /**
     * Active cache HTTP
     * @return Response
     */
    public function activeCache()
    {
        //Activate Cache
        $this->response = $this->cacheProvider->allowCache($this->response, $this->type, $this->maxAge,
            $this->mustRevalidate
        );

        $expireData = time() + $this->expire;
        //$lastModifiedData = time() - $this->expire;
        $etag = md5($this->dataResponse);

        $this->response = $this->cacheProvider->withEtag($this->response, $etag);
        $this->response = $this->cacheProvider->withExpires($this->response, $expireData);
        //$this->response = $cacheHttp->withLastModified($this->response, $lastModifiedData);
        return $this->response;
    }
}