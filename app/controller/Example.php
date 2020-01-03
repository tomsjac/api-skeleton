<?php

namespace App\controller;

use Interop\Container\Exception\ContainerException;
use Slim\Http\Response;

/**
 * Description of Home
 *
 * @author thomas
 */
class Example extends AbstractController
{
    /**
     * Hello
     * @throws ContainerException
     */
    public function hello()
    {
        //Check Access
        /*
        if (($error = $this->checkAccess(['public'], ['GET'])) !== true) {
            return $error;
        }
        */

        //Load Formatter
        $formatter = $this->callFormatter(\App\formatter\Example::class);
        $data = $formatter->getData();
        return $formatter->formatData($data);
    }

    /**
     * example : Arguments
     * @throws ContainerException
     */
    public function args()
    {
        $data = array(
            'method' => $this->getRequest()->getMethod(),
            'argument' => $this->getUrlParams(),
            'query' => $this->getQueryParams(),
            'body' => $this->getBodyParams(),
            'all' => $this->getAllParams(),
        );

        return $this->getApiResponse()->setStatus(200)->write($data);
    }

    /**
     * example : Data filter
     *
     * @return Response
     * @throws ContainerException
     */
    public function argsFilter()
    {
        $dataFilter = $this->getContainer('dataFilter');

        $dataFilter->value('firstName')->string()->trim()->upperFirst();
        $dataFilter->value('lastName')->string()->defaults('Par Défault');
        $dataFilter->value('id')->int();

        $datas = $dataFilter->filterAllParams();

        return $this->getApiResponse()->setStatus(200)->write($datas);
    }

    /**
     * example : Logger information
     * @throws ContainerException
     */
    public function Log()
    {
        $logger = $this->getLogger();
        $data = array('firstName' => 'Toto', 'lastName' => 'Tutu');
        $logger->addInfo('Creating new item', $data);
    }

    /**
     * example : Response JSON
     * @throws ContainerException
     */
    public function response()
    {
        $data = array('firstName' => 'Toto', 'lastName' => 'Tutu');
        return $this->getApiResponse()->setStatus(200)->write($data);
    }

    /**
     * example : Connect BDD
     * @throws ContainerException
     */
    public function dataInBdd()
    {
        $data = [];
        $orm = $this->getDB();
        $listDirect = $orm->table('myTable')->get();
        $data += ['direct' => $listDirect];

        $example = new \App\models\Example();
        $listModel = $example::where('myCondition', 'values')->orderBy('myName', 'asc')->get();
        $data += ['model' => $listModel];

        return $this->getApiResponse()->setStatus(200)->write($data);
    }
}
