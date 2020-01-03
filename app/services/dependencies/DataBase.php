<?php
namespace App\services\dependencies;

/**
 * Connection with the database + initialization orm
 * @author thomas
 */
use Illuminate\Database\Capsule\Manager;
use Interop\Container\Exception\ContainerException;
use Slim\Container;

class DataBase extends Manager
{
    /**
     * @var string
     */
    protected $defaultDB = 'default';

    /**
     * Construct
     * @param Container $container
     * @throws ContainerException
     */
    public function __construct(Container $container)
    {
        $settings  = $container->get('settings');
        $optionsDb = $settings->get('database');

        parent::__construct();

        //Add connection DB
        if (isset($optionsDb['connections']) === true) {
            foreach ($optionsDb['connections'] as $nameDB => $confDB) {
                $this->addConnection($confDB, $nameDB);
            }
        } else {
            $this->addConnection($optionsDb, $this->defaultDB);
        }

        //Init Default DB
        if (isset($optionsDb['default']) == true) {
            $this->defaultDB = $optionsDb['default'];
        }
        $this->getDatabaseManager()->setDefaultConnection($this->defaultDB);


        $this->setAsGlobal();
        $this->bootEloquent();
    }

    /**
     * Return Eloquent Object (Capsule)
     * @return Manager
     */
    public function __invoke()
    {
        return $this;
    }

    /**
     * Return Default Db
     * @return string
     */
    public function getDefaultDb()
    {
        return $this->defaultDB;
    }
}