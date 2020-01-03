<?php

namespace App\services\dependencies;

use Interop\Container\Exception\ContainerException;
use PDO;
use Slim\Container;

/**
 * INSTALL TABLE SQL
 *
 *  CREATE TABLE IF NOT EXISTS `rate_limits_call` (
 *   `id` int(11) NOT NULL AUTO_INCREMENT,
 *   `originip` varchar(45) NOT NULL DEFAULT '',
 *   `method` varchar(120) NOT NULL DEFAULT '',
 *   `ts` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
 *   ) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COMMENT='Requests from remote IPs';
 *
 *   ALTER TABLE `rateLimitsCall` ADD PRIMARY KEY (`id`), ADD KEY `ts` (`ts`), ADD KEY `originip` (`originip`), ADD KEY `method` (`method`);
 */

/**
 * API Rate Limiter
 */
class RateLimiter
{
    /**
     *
     * @var Container
     */
    protected $container;

    /**
     *
     * @var Pdo
     */
    protected $pdo;

    /**
     * Name table for rating
     *
     * @var string
     */
    protected $tableName = 'rate_limits_call';

    /**
     * Call URL
     *
     * @var string
     */
    protected $callUri;

    /**
     * Ip user
     *
     * @var string
     */
    protected $remoteIp;

    /**
     * nb hours past, for The purge
     *
     * @var integer
     */
    protected $nbHoursPurge = 10;

    /**
     * Construct
     *
     * @param Container $container
     * @throws ContainerException
     */
    public function __construct(Container $container)
    {
        $this->container = $container;

        $request = $container->get('request');
        $this->callUri = $request->getUri()->getPath();
        $this->remoteIp = $request->getServerParam('REMOTE_ADDR');
    }

    /**
     * Set PDO
     *
     * @param Pdo $pdo
     * @return void
     */
    public function setPdo($pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Set table name
     *
     * @param string $name
     * @return void
     */
    public function setTableName(string $name)
    {
        $this->tableName = $name;
    }

    /**
     * Set Nb Hours before the purge
     *
     * @param int $nbHours
     * @return void
     */
    public function setNbHoursPurge(int $nbHours)
    {
        $this->nbHoursPurge = $nbHours;
    }

    /**
     * Set Ip Call
     *
     * @param string $ip
     * @return void
     */
    public function setRemoteIp(string $ip)
    {
        $this->remoteIp = $ip;
    }

    /**
     * Verify if the limits are reached
     *
     * @param int $maxRequest Nb request authorized
     * @param int $perMinute per minute
     * @param bool $allApi Check on all Api
     * @return boolean True : stop, False : Ok
     * @throws ContainerException
     */
    public function checkRate(int $maxRequest = 100, int $perMinute = 60, bool $allApi = false)
    {
        //Check WhiteList Ip
        if ($this->isWhiteList() === true) {
            return false;
        }

        $nbRequest = $this->getNumberRequest($maxRequest, $perMinute, $allApi);

        //OverQuota, limit Reach
        if ($nbRequest >= $maxRequest) {
            return true;
        }

        //Add entry
        $this->addEntryRequest();
        //Delete old data
        $this->purgeRequestOld();
        return false;
    }

    /**
     * Return the number of request
     *
     * @param int $maxRequest Nb request authorized
     * @param int $perMinute per minute
     * @param bool $allApi Check on all Api
     * @return string|int
     */
    private function getNumberRequest(int $maxRequest, int $perMinute, bool $allApi)
    {
        $optionMethod = '';
        if ($allApi == false) {
            $optionMethod = "AND method = :method";
        }

        $sql = "
            SELECT count(id) as requests 
            FROM " . $this->tableName . " 
            WHERE 
            originip = :originIp 
            AND ts >= date_sub(NOW(), interval :perMinute MINUTE)  
            " . $optionMethod . "
            LIMIT :maxRequest
        ";
        $sth = $this->pdo->prepare($sql);

        //Bind Params
        $sth->bindParam(':originIp', $this->remoteIp, PDO::PARAM_STR, 45);
        $sth->bindParam(':method', $this->callUri, PDO::PARAM_STR, 120);
        $sth->bindParam(':perMinute', $perMinute, PDO::PARAM_INT);
        $sth->bindParam(':maxRequest', $maxRequest, PDO::PARAM_INT);

        $sth->execute();

        if ($sth) {
            $result = $sth->fetch(PDO::FETCH_ASSOC);
            return $result['requests'];
        } else {
            return 0;
        }
    }

    /**
     * Add an entry to BDD for the request
     * @return void
     */
    private function addEntryRequest()
    {
        $sql = "
            INSERT INTO " . $this->tableName . " 
            (originip, method)
            VALUES 
            (:originIp, :method)
        ";
        $sth = $this->pdo->prepare($sql);

        $sth->bindParam(':originIp', $this->remoteIp, PDO::PARAM_STR, 45);
        $sth->bindParam(':method', $this->callUri, PDO::PARAM_STR, 120);
        $sth->execute();
    }

    /**
     * Purge the datas too old
     * @return void
     */
    private function purgeRequestOld()
    {
        $sql = "
            DELETE FROM " . $this->tableName . " 
            WHERE 
            ts <= date_sub(NOW(), interval :nbHoursPurge HOUR)  
        ";
        $sth = $this->pdo->prepare($sql);
        $sth->bindParam(':nbHoursPurge', $this->nbHoursPurge, PDO::PARAM_INT);
        $sth->execute();
    }

    /**
     * Test ip Remote is in the whiteList
     * @return boolean
     * @throws ContainerException
     */
    private function isWhiteList()
    {
        $arrayWhiteList = $this->container->get('settings')->get('rateLimit')['whiteList'];
        return in_array($this->remoteIp, $arrayWhiteList);
    }
}
