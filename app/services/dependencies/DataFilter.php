<?php
namespace App\services\dependencies;


use App\services\traits\Container;
use App\services\traits\Request;
use Particle\Filter\Filter;

/**
 * Class DataFilter
 * @package App\services\dependencies
 */
class DataFilter extends Filter
{
    /**
     * Add Trait
     */
    use Container;
    use Request;

    /**
     * DataFilter constructor.
     * @param \Slim\Container $container
     */
    public function __construct(\Slim\Container $container)
    {
        $this->container = $container;
    }

    /**
     * filters the data in URL
     * @example auth/jwt/token/id::xxx/secret::yyyy
     * @return array
     */
    public function filterUrlParams(){
        $datas = $this->getUrlParams();
        return $this->filter((array) $datas);
    }

    /**
     * filters the data in GET
     * @return array
     */
    public function filterQueryParams(){
        $datas = $this->getQueryParams();
        return $this->filter((array) $datas);
    }

    /**
     * filters the data in POST
     * @return array
     */
    public function filterBodyParams(){
        $datas = $this->getBodyParams();
        return $this->filter((array) $datas);
    }

    /**
     * filters the data in GET & POST
     * @return array
     */
    public function filterAllParams(){
        $datas = $this->getAllParams();
        return $this->filter((array) $datas);
    }
}