<?php
/**
 * Network adaptor
 * User: moyo
 * Date: 24/10/2017
 * Time: 12:07 PM
 */

namespace Carno\Cache\Adaptors;

use Carno\Cache\Chips\DataOps;
use Carno\Cache\Chips\DefValues;
use Carno\Cache\Chips\Delegate;
use Carno\Cache\Chips\OpsKey;
use Carno\Cache\Exception\IllegalNetworkDriverException;
use Carno\Cache\Refreshing;
use Carno\Cache\Stores\Redis as RedisStorage;
use Carno\Redis\Cluster as RedisCluster;
use Carno\Redis\Redis as RedisClient;

abstract class Network
{
    use DefValues, OpsKey, DataOps, Delegate;

    /**
     * Redis constructor.
     * @param Refreshing $refresher
     */
    final public function __construct(Refreshing $refresher)
    {
        $this->refresher = $refresher;

        switch ($driver = $this->driver()) {
            case $driver instanceof RedisClient:
            case $driver instanceof RedisCluster:
                $storage = new RedisStorage($driver);
                break;
            default:
                throw new IllegalNetworkDriverException;
        }

        $this->backend = $storage;
    }

    /**
     * @return mixed
     */
    abstract protected function driver() : object;
}
