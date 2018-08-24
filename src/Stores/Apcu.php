<?php
/**
 * Apcu storing (shared memory)
 * User: moyo
 * Date: 2018/7/11
 * Time: 10:41 AM
 */

namespace Carno\Cache\Stores;

use Carno\Cache\Contracts\Storing;

class Apcu implements Storing
{
    /**
     * @param string $key
     * @return bool
     */
    public function has(string $key) : bool
    {
        return apcu_exists($key);
    }

    /**
     * @param string $key
     * @return mixed
     */
    public function read(string $key)
    {
        return apcu_fetch($key);
    }

    /**
     * @param string $key
     * @param mixed $data
     * @param int $ttl
     * @return bool
     */
    public function write(string $key, $data, int $ttl = null) : bool
    {
        return apcu_store($key, $data, $ttl ?? 0);
    }

    /**
     * @param string $key
     * @return bool
     */
    public function remove(string $key) : bool
    {
        return apcu_delete($key);
    }
}
