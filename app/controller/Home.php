<?php
namespace App\controller;

use Interop\Container\Exception\ContainerException;
use Psr\Http\Message\ResponseInterface;
use Slim\Http\Response;

/**
 * Description of Home
 *
 * @author thomas
 */
class Home extends AbstractController
{

    /**
     * HomePage APi
     * @return Response
     * @throws ContainerException
     */
    public function index()
    {
        $msg = "Hello World : Welcome to the API";
        return $this->getApiResponse()->write($msg);
    }

    /**
     * HomePage With Template
     * @return ResponseInterface
     * @throws ContainerException
     */
    public function indexTemplate()
    {
        $assign['msg'] = "Hello World : Welcome to the API";
        //appel de la vue
        return $this->getView()->render($this->getResponse(), 'home.html', $assign);
    }
}