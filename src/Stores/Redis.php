<?php
/**
 * Redis storing
 * User: moyo
 * Date: 15/11/2017
 * Time: 5:06 PM
 */

namespace Carno\Cache\Stores;

use Carno\Cache\Contracts\Storing;
use Redis as API;

class Redis implements Storing
{
    /**
     * @var API
     */
    private $backend = null;

    /**
     * Redis constructor.
     * @param API $backend
     */
    public function __construct($backend)
    {
        $this->backend = $backend;
    }

    /**
     * @param string $key
     * @return bool
     */
    public function has(string $key)
    {
        return (yield $this->backend->exists($key)) ? true : false;
    }

    /**
     * @param string $key
     * @return mixed
     */
    public function read(string $key)
    {
        return yield $this->backend->get($key);
    }

    /**
     * @param string $key
     * @param mixed $data
     * @param int $ttl
     * @return bool
     */
    public function write(string $key, $data, int $ttl = null)
    {
        return yield $this->backend->setex($key, $ttl, $data);
    }

    /**
     * @param string $key
     * @return bool
     */
    public function remove(string $key)
    {
        return (yield $this->backend->del($key)) ? true : false;
    }
}
