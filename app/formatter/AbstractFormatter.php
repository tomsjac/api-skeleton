<?php
namespace App\formatter;

use App\services\dependencies\DataBase;
use App\services\traits\Container;
use Interop\Container\Exception\ContainerException;
use Slim\Http\Response;

/**
 * Main Formatter
 *
 * @author thomas
 * @version : 1.0.0
 */
abstract class AbstractFormatter
{
    /**
     * Load Trait
     */
    use Container;
    
    /**
     * @var DataBase ORM (Eloquent)
     */
    protected $orm;
    
    /**
     * @var array
     */
    protected $args;
    
    /**
     * Error management
     * @var array
     */
    protected $error = [];

    /**
     * Constructor
     *
     * @param \Slim\Container $container
     * @param object|null $args
     * @throws ContainerException
     */
    public function __construct(\Slim\Container $container, ?object $args)
    {
        $this->container = $container;
        //Init Database contenair
        $this->orm      = $this->container->get('database');

        //Data
        $this->args = $args;
    }

    /**
     * Generates the response for the api
     * @param mixed $content
     * @return Response
     * @throws ContainerException
     */
    protected function getFormatResponseOk($content)
    {
        return $this->getApiResponse()->write($content);
    }

    /**
     * Generates the response for the api in case of error
     * @param string $errorType
     * @param string $errorMsg
     * @return Response
     * @throws ContainerException
     */
    protected function getFormatResponseError(string $errorType = null, string $errorMsg = null)
    {
        $errorDefault = $this->getError();

        $errorType = $errorType ?? $errorDefault['type'];
        $errorMsg  = $errorMsg ?? $errorDefault['msg'];

        $this->getApiResponse()->setError($errorMsg, $errorType);
        return $this->getApiResponse()->write('');
    }

    /**
     * Set error
     * @param string $type
     * @param string $content
     */
    protected function setError(string $type, string $content = '')
    {
        $this->error['type'] = $type;
        $this->error['msg']  = (!empty($content)) ? $content : '-';
    }

    /**
     * Get error
     * @return array
     */
    protected function getError()
    {
        return $this->error;
    }

    /**
     * Checks for errors
     * @return bool
     */
    protected function hasError()
    {
        return !empty($this->error);
    }

    /**
     * Get arguments
     * @return array|null
     */
    protected function getArgs()
    {
        return $this->args;
    }
}
