<?php

namespace App\controller\cron;

use App\controller\AbstractController;
use Interop\Container\Exception\ContainerException;
use Slim\Http\Response;

/**
 * Description of CronJob
 *
 * @author thomas
 */
class CronJob extends AbstractController
{
    /**
     * Cron Job index
     * @return Response
     * @throws ContainerException
     */
    public function index()
    {
        $returnResponse = $this->getApiResponse();
        $returnResponse->setData(array('ok'));
        return $returnResponse->write();
    }

    /**
     * Cron executed every minute
     * @return Response
     * @throws ContainerException
     */
    public function cronMinute()
    {
        $returnResponse = $this->getApiResponse();
        $returnResponse->setData(array('Cron Minute'));
        return $returnResponse->write();
    }

    /**
     * Cron executed every hour
     * @return Response
     * @throws ContainerException
     */
    public function cronHour()
    {
        $returnResponse = $this->getApiResponse();
        $returnResponse->setData(array('Cron Hour'));
        return $returnResponse->write();
    }

    /**
     * Cron executed every day
     * @return Response
     * @throws ContainerException
     */
    public function cronDay()
    {
        $returnResponse = $this->getApiResponse();
        $returnResponse->setData(array('Cron Day'));
        return $returnResponse->write();
    }

    /**
     * Cron executed weekly
     * @return Response
     * @throws ContainerException
     */
    public function cronWeek()
    {
        $returnResponse = $this->getApiResponse();
        $returnResponse->setData(array('Cron Week'));
        return $returnResponse->write();
    }

    /**
     * Cron executed monthly
     * @return Response
     * @throws ContainerException
     */
    public function cronMonth()
    {
        $returnResponse = $this->getApiResponse();
        $returnResponse->setData(array('Cron Month'));
        return $returnResponse->write();
    }

    /**
     * Cron executed every year
     * @return Response
     * @throws ContainerException
     */
    public function cronYear()
    {
        $returnResponse = $this->getApiResponse();
        $returnResponse->setData(array('Cron Year'));
        return $returnResponse->write();
    }
}