<?php
namespace App\services\middleware\api;

use App\services\dependencies\response\ApiResponse;
use Closure;
use Exception;
use Interop\Container\Exception\ContainerException;
use Slim\Container;
use Tuupola\Middleware\CorsMiddleware;

/**
 * Declaration Cors
 * @author thomas
 */
class Cors
{
    /**
     * Options pour le controle JWT
     * @var array
     */
    protected $options = [];

    /**
     * Cors constructor.
     * @param Container $container
     * @throws ContainerException
     * @throws Exception
     */
    public function __construct(Container $container)
    {
        $settings    = $container['settings'];

        if (isset($settings['cors']) == false or empty($settings['cors']) == true) {
            throw new Exception('The configuration is not available for CORS in the configuration files');
        }

        //Option ++
        $option = [
            "logger" => $container->get('logger'),
            "error" => $this->getMsgError($container),
        ];

        $this->options = array_merge($settings['cors'], $option);
    }

    /**
     * Return final MiddleWare to Add Slim
     * @return CorsMiddleware
     */
    public function getMiddleware()
    {
        return new CorsMiddleware($this->options);
    }

    /**
     * Generates the error message
     * @param Container $container $container
     * @return Closure
     */
    protected function getMsgError(Container $container)
    {
        return function ($request, $response, $arguments) use ($container) {
            /* @var $apiResponse ApiResponse */
            $apiResponse = $container->get('apiResponse');

            return $apiResponse
                    ->setStatus(401)
                    ->setError($arguments["message"])
                    ->write();
        };
    }
}