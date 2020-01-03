<?php

namespace App\controller\console;


use Interop\Container\Exception\ContainerException;
use Slim\Http\Request;
use Slim\Http\Response;

final class Example extends AConsole
{
    /**
     * Test
     * @param Request $request
     * @param Response $response
     * @param $args
     * @return bool|Response
     * @throws ContainerException
     */
    public function index(Request $request, Response $response, $args)
    {
        /**
         * Require, check if it is a CMD call
         */
        if (($error = $this->checkCmd()) !== true) {
            return $error;
        }

        echo "Welcome To the console \n";
    }

}