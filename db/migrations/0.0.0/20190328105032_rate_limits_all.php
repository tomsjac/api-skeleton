<?php
/**
 * Create Limit Rate Table
 */

use Phinx\Migration\AbstractMigration;

class RateLimitsAll extends AbstractMigration
{
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
    public function change()
    {
        $table = $this->table('rate_limits_call', ['engine' => 'InnoDB', 'comment' => 'Requests from remote IPs']);
        $table
            ->addColumn('originip', 'string', ['limit' => 45, 'null' => false, 'default' =>''])
            ->addColumn('method', 'string', ['limit' => 120, 'null' => false, 'default' =>''])
            ->addColumn('ts', 'timestamp', ['null' => false, 'default' => 'CURRENT_TIMESTAMP'])
            ->addIndex(['ts', 'originip', 'method'])
            ->create();
    }
}
