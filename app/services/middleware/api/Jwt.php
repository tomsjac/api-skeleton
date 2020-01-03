<?php

namespace App\services\middleware\api;

use App\services\dependencies\response\ApiResponse;
use Closure;
use Exception;
use Slim\Container;
use Tuupola\Middleware\JwtAuthentication;

/**
 * Declaration Authentication JWT
 * Class call in the file : \config\services\middlewares
 * @author thomas
 */
class Jwt
{
    /**
     * Options pour le controle JWT
     * @var array
     */
    protected $options = [];

    /**
     * Jwt constructor.
     * @param Container $container
     * @throws Exception
     */
    public function __construct(Container $container)
    {
        $settings = $container['settings'];

        if (isset($settings['jwt']) == false or empty($settings['jwt']) == true) {
            throw new Exception('The configuration is not available for JWT in the configuration files');
        }

        //Option For All
        $optionJwt = [
            "logger" => $container["logger"],
            "error" => $this->getMsgError($container),
            "before" => $this->getBeBack($container)
        ];

        $this->options = array_merge($settings['jwt'], $optionJwt);
    }

    /**
     * Return final MiddleWare to Add Slim
     * @return JwtAuthentication
     */
    public function getMiddleware()
    {
        return new JwtAuthentication($this->options);
    }

    /**
     * Generates the error message
     * @param Container $container $container
     * @return Closure
     */
    protected function getMsgError(Container $container)
    {
        return function ($response, $arguments) use ($container) {
            /* @var $apiResponse ApiResponse */
            $apiResponse = $container->get('apiResponse');
            return $apiResponse
                ->setStatus(401)
                ->setError($arguments["message"], 'invalid_auth')
                ->write();
        };
    }

    /**
     * If successful, processing
     * @param Container $container
     * @return Closure
     */
    protected function getBeBack(Container $container)
    {
        return function ($response, $arguments) use ($container) {
            $container->get('token')->hydrate($arguments["decoded"]);
        };
    }
}
