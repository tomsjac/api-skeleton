<?php

namespace App\controller\console;


use Interop\Container\Exception\ContainerException;
use Slim\Container;
use Slim\Http\Response;

/**
 * Abstract Class for CMD
 * @package App\controller\console
 */
abstract class AConsole {

    /**
     * @var Container
     */
    private $container;

    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    /**
     * Check access cmd
     * @return bool|Response
     * @throws ContainerException
     */
    protected function checkCmd()
    {
        $request = $this->container->get('request');
        $response = $this->container->get('apiResponse');

        // ONLY WHEN CALLED THROUGH CLI
        if (PHP_SAPI !== 'cli' && !$request->getParam('event')) {
            $response
                ->setStatus(401)
                ->setError('You are not allowed to see his data', 'not_authorized');
            return $response->write();
        }
        return true;
    }
}