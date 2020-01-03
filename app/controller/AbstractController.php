<?php

namespace App\controller;

use App\services\tools\auth\TokenJwt;
use App\services\traits\Container;
use App\services\traits\Request;
use App\services\traits\Response;
use Interop\Container\Exception\ContainerException;

/**
 * Main controller
 *
 * @author thomas
 * @version : 1.0.0
 */
abstract class AbstractController
{
    /**
     * Load Trait
     */
    use Container;
    use Request;
    use Response;

    /**
     * Construct
     * @param \Slim\Container $container
     * @param \Slim\Http\Request $request
     * @param \Slim\Http\Response $response
     * @throws ContainerException
     */
    public function __construct(
        \Slim\Container $container, \Slim\Http\Request $request = null, \Slim\Http\Response $response = null)
    {
        $this->container = $container;
        $this->request = $request ?? $container->get('request');
        $this->response = $response ?? $container->get('response');

        //Init Database contenair
        $this->container->get('database');


    }

    /**
     * Check access to the data (Token scope / Method call)
     * @param array|null $scope
     * @param array|null $method
     * @return bool|\Slim\Http\Response
     * @throws ContainerException
     */
    protected function checkAccess(array $scope = null, array $method = null)
    {
        $response = $this->getApiResponse();

        //we checked if the calling method is correct
        if ($method != null && $this->isMethodRequestAvailable($method) == false) {
            $response
                ->setStatus(401)
                ->setError('The method call is not allowed', 'not_authorized');
            return $response->write();
        }

        //we checked if the scope is ok
        if ($scope != null && $this->isScopeAvailable($scope) == false) {
            $response
                ->setStatus(401)
                ->setError('You are not allowed to see his data', 'not_authorized');
            return $response->write();
        }
        return true;
    }


    /**
     * Check rate Limit
     * @param int $maxRequest
     * @param int $perMinute
     * @param bool $allApi
     * @return bool|\Slim\Http\Response
     * @throws ContainerException
     */
    protected function checkRateLimit(int $maxRequest = 100, int $perMinute = 60, bool $allApi = false)
    {
        //data request
        $dataRequest = $this->getAllParams();

        //Get Bdd PDO
        $dataBase = $this->getContainer('database');
        $pdo = $dataBase::connection()->getPdo();

        //Get Limit rate
        $rateLimit = $this->getContainer('rateLimit');
        $rateLimit->setPdo($pdo);

        //Ip supplies by the call
        if (isset($dataRequest->ipClientForRate) && empty($dataRequest->ipClientForRate) === false) {
            $rateLimit->setRemoteIp($dataRequest->ipClientForRate);
        }

        //Check quota
        $overQuota = $rateLimit->checkRate($maxRequest, $perMinute, $allApi);

        if ($overQuota == true) {
            $response = $this->getApiResponse();

            $response
                ->setStatus(429)
                ->setError('You are not allowed to see his data', 'rateLimit_quota');
            return $response->write()->withHeader('RateLimit-Limit', $maxRequest);
        }
        return true;
    }

    /**
     * Calling a formatter for displaying data
     * @param string $formatter
     * @param array|null $args
     * @return mixed
     * @throws ContainerException
     */
    protected function callFormatter($formatter, array $args = null)
    {
        $args = $args ?? $this->getAllParams();
        return new $formatter($this->getContainer(), $args);
    }


    /**
     * Checks if the calling method is allowed
     * @param array $methodAllowed
     * @return bool
     * @throws ContainerException
     */
    private function isMethodRequestAvailable(array $methodAllowed)
    {
        $requestMethode = $this->container->get('request')->getMethod();
        if (empty($methodAllowed) == false && in_array($requestMethode, $methodAllowed) === false) {
            return false;
        }
        return true;
    }

    /**
     * Checks if the access to the data is correct
     * @param array $scopeAllowed
     * @return boolean
     * @throws ContainerException
     */
    private function isScopeAvailable(array $scopeAllowed)
    {
        /* @var $token TokenJwt */
        $token = $this->getContainer('token');
        /**
         * If the scope is empty, everything passes
         * If the token has good scope that is ok
         */
        if (empty($scopeAllowed) == true || ($token->isInit() == true && $token->hasScope($scopeAllowed) == true)) {
            return true;
        }

        return false;
    }
}
