<?php
namespace Tests\application;

use PHPUnit\Framework\TestCase;
use Slim\App;
use Slim\Http\Request;
use Slim\Http\Response;
use Slim\Http\Environment;

class BaseTestCase extends TestCase
{
    /**
     * Process the application given a request method and URI
     *
     * @param string $requestMethod the request method (e.g. GET, POST, etc.)
     * @param string $requestUri the request URI
     * @param array|object|null $requestData the request data
     * @param bool $activateAuth Use authentication ?
     * @return \Slim\Http\Response
     * @throws \Slim\Exception\MethodNotAllowedException
     * @throws \Slim\Exception\NotFoundException
     */
    public function runApp($requestMethod, $requestUri, $requestData = null, $activateAuth = true)
    {
        // Create a mock environment for testing with
        $environment = Environment::mock(
            [
                'REQUEST_METHOD' => $requestMethod,
                'REQUEST_URI' => $requestUri
            ]
        );
        // Set up a request object based on the environment
        $request = Request::createFromEnvironment($environment);
        // Add request data, if it exists
        if (isset($requestData)) {
            $request = $request->withParsedBody($requestData);
        }
        // Set up a response object
        $response = new Response();

        /**
         * INIT Slim
         */
        $settings = require __DIR__ . '/../../bootstrap/bootSettings.php';
        if ($activateAuth == false) {
            $settings['settings']['jwt']['ignore'] = ["/"];
        }

        // Instantiate the application
        $app             = new App($settings);
        
        // Set up dependencies
        require __DIR__ . '/../../bootstrap/bootServices.php';
        require __DIR__ . '/../../bootstrap/bootRoutes.php';

        // Process the application
        $response = $app->process($request, $response);
        // Return the response
        return $response;
    }
}
