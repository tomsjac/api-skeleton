<?php
namespace App\services\traits;

/**
 * Quick feature set to access response HTTP
 * 
 * $this->container must be require
 * $this->response must be require
 * 
 * @author thomas
 * @version : 1.0.0
 */
trait Response
{
    /**
     * Response Controller
     * @var \Slim\Http\Response
     */
    protected $response;


    /**
     * Get Response Controller
     * @return \Slim\Http\Response
     */
    protected function getResponse()
    {
        if ($this->response == null) {
            return $this->container->get('response');
        }
        return $this->response;
    }
}
