<?php
/**
 * Backend storing API
 * User: moyo
 * Date: 15/11/2017
 * Time: 5:02 PM
 */

namespace Carno\Cache\Contracts;

interface Storing
{
    /**
     * @param string $key
     * @return bool
     */
    public function has(string $key);

    /**
     * @param string $key
     * @return mixed
     */
    public function read(string $key);

    /**
     * @param string $key
     * @param mixed $data
     * @param int $ttl
     * @return bool
     */
    public function write(string $key, $data, int $ttl = null);

    /**
     * @param string $key
     * @return bool
     */
    public function remove(string $key);
}
