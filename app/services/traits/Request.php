<?php
namespace App\services\traits;

use Interop\Container\Exception\ContainerException;
use Slim\App;
use stdClass;

/**
 * Quick feature set to access requests HTTP
 * 
 * $this->container must be require
 * $this->request must be require
 * 
 * @author thomas
 * @version : 1.0.0
 */
trait Request
{
    /**
     * Request Controller
     * @var \Slim\Http\Request
     */
    protected $request;

    /**
     * Get Request Controller
     * @return \Slim\Http\Request
     */
    protected function getRequest()
    {
        if ($this->request == null) {
            return $this->container->get('request');
        }
        return $this->request;
    }

    /**
     * Equivalent to $request->getParams() With The Url Argument
     * call parameters (GET / POST / PUT ...)
     * @return stdClass
     */
    protected function getAllParams()
    {
        $request   = $this->getRequest();
        $params    = $request->getParams();
        $argParams = $this->getUrlParams(false);
        if ($argParams) {
            $params = array_merge($params, (array) $argParams);
        }
        return $this->parseParams($params);
    }

    /**
     * Return Param URL
     * call parameters (GET ...)
     * @param boolean $parse
     * @return stdClass
     */
    protected function getUrlParams(bool $parse = true)
    {
        $request   = $this->getRequest();
        $paramsUrl = $request->getAttribute('params');
        $params    = explode('/', $paramsUrl);

        if ($parse == false) {
            return $params;
        }
        return $this->parseParams($params);
    }

    /**
     * Equivalent to $request->getQueryParams()
     * call parameters (GET ...)
     * @return stdClass
     */
    protected function getQueryParams()
    {
        $request     = $this->getRequest();
        $paramsQuery = $request->getQueryParams();

        return $this->parseParams($paramsQuery);
    }

    /**
     * Equivalent to $request->getParsedBody()
     * call parameters (POST / PUT ...)
     * @return stdClass
     */
    protected function getBodyParams()
    {
        $request    = $this->getRequest();
        $paramsBody = $request->getParsedBody();

        return $this->parseParams($paramsBody);
    }

    /**
     * Process the url parameters
     * Transforms separation _ in key / value
     * Transforms the data to Object
     * @param array|null $data
     * @return stdClass
     */
    private function parseParams(?array $data)
    {
        $separatorParam = $this->container->get('settings')['requestParamSeparator'];

        $returnParam = new stdClass();
        if ($data == null) {
            return $returnParam;
        }

        foreach ($data as $key => $value) {
            if (is_null($value) === true || rtrim($value) == '') {
                continue;
            }

            //Check Seperateur Data
            $resultSeparator = explode($separatorParam, $value);
            $data            = $resultSeparator[0]; //default
            //Redefine Key / data unique
            if (isset($resultSeparator[1]) === true && is_string($key) == false) {
                $key             = $resultSeparator[0];
                $data            = $resultSeparator[1];
                $resultSeparator = array_slice($resultSeparator, 1); //Del Key
            }

            //Redefine if multi data
            if (isset($resultSeparator[1]) === true && count($resultSeparator) > 1) {
                $data = $resultSeparator;
            }

            $returnParam->{$key} = $data;
        }
        return $returnParam;
    }
}
