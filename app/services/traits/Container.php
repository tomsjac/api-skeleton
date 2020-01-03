<?php

namespace App\services\traits;

use App\services\dependencies\DataBase;
use App\services\dependencies\response\ApiResponse;
use App\services\tools\auth\TokenJwt;
use Interop\Container\Exception\ContainerException;
use Monolog\Logger;
use Slim\Views\Twig;

/**
 * Quick feature set to access slim container data
 * $this->container must be require
 *
 * @author thomas
 * @version : 1.0.0
 */
trait Container
{
    /**
     * @var \Slim\Container Slim DI Container
     */
    protected $container;

    /**
     * Return a service, or the contenair
     * @param string|null $name
     * @return \Slim\Container Container
     * @throws ContainerException
     */
    protected function getContainer(string $name = null)
    {
        if (!is_null($name) && $this->container->has($name)) {
            return $this->container->get($name);
        }
        return $this->container;
    }

    /**
     * @return TokenJwt|\Slim\Container
     * @throws ContainerException
     */
    protected function getToken()
    {
        return $this->getContainer('token');
    }

    /**
     * @return Logger|\Slim\Container
     * @throws ContainerException
     */
    protected function getLogger()
    {
        return $this->getContainer('logger');
    }

    /**
     * @return ApiResponse|\Slim\Container
     * @throws ContainerException
     */
    protected function getApiResponse()
    {
        return $this->getContainer('apiResponse');
    }

    /**
     * @return \Slim\Container|Twig|null
     * @throws ContainerException
     */
    protected function getView()
    {
        return $this->getContainer('view') ?? null;
    }

    /**
     * Get database, for use without model
     * @param string|null $nameConnection Name database select
     * @return DataBase connection
     * @throws ContainerException
     */
    protected function getDB(?string $nameConnection = null)
    {
        $orm = $this->getContainer('database');

        if ($nameConnection == null) {
            $nameConnection = $orm->getDefaultDb();
        }
        return $orm::connection($nameConnection);
    }

}
