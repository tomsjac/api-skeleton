<?php
namespace App\services\dependencies\response;

use Exception;
use Interop\Container\Exception\ContainerException;
use Psr\Http\Message\ ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Log\LoggerInterface;
use Slim\Container;
use Slim\Handlers\Error;

/**
 * Error Slim To json
 */
final class ApiError extends Error
{
    /**
     * @var Container
     */
    protected $container;

    /**
     * Obj Logger
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * ApiError constructor.
     * @param Container $container
     * @throws ContainerException
     */
    public function __construct(Container $container)
    {
        $this->container = $container;
        $this->logger    = $container->get("logger");
        parent::__construct(true);
    }

    /**
     *
     * @param Request $request
     * @param Response $response
     * @param Exception $exception
     * @return \Slim\Http\Response
     * @throws ContainerException
     */
    public function __invoke(Request $request, Response $response, Exception $exception)
    {
        /* @var $apiResponse ApiResponse */
        $apiResponse = $this->container->get('apiResponse');
        $this->logger->critical($exception->getMessage());

        $status  = $exception->getCode() ? : 500;
        $message = $exception->getMessage();

        return $apiResponse
                ->setStatus(500)
                ->setError('Code : '.$status.' - Message : '.$message)
                ->write();
    }
}