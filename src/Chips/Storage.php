<?php
/**
 * Storage API
 * User: moyo
 * Date: 24/10/2017
 * Time: 12:13 PM
 */

namespace Carno\Cache\Chips;

use Carno\Cache\Contracts\Storing;

trait Storage
{
    /**
     * @var Storing
     */
    private $backend = null;

    /**
     * @param string $key
     * @return bool
     */
    final public function has(string $key)
    {
        return yield $this->backend->has($this->key($key));
    }

    /**
     * @param string $key
     * @return mixed|null
     */
    final public function read(string $key)
    {
        return (false !== $got = yield $this->backend->read($this->key($key))) ? $got : null;
    }

    /**
     * @param string $key
     * @param mixed $data
     * @param int $ttl
     * @return bool
     */
    final public function write(string $key, $data, int $ttl = null)
    {
        return yield $this->backend->write($this->key($key), $data, $ttl ?? $this->ttl);
    }

    /**
     * @param string $key
     * @return bool
     */
    final public function remove(string $key)
    {
        return yield $this->backend->remove($this->key($key));
    }
}
