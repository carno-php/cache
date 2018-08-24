<?php
/**
 * Local storing (process memory)
 * User: moyo
 * Date: 15/11/2017
 * Time: 5:13 PM
 */

namespace Carno\Cache\Stores;

use Carno\Cache\Contracts\Storing;
use Carno\Cache\Eviction;

class Local implements Storing
{
    /**
     * @var int
     */
    private $oid = null;

    /**
     * @var array
     */
    private $block = [];

    /**
     * @var Eviction
     */
    private $eviction = null;

    /**
     * Local constructor.
     * @param Eviction $eviction
     */
    public function __construct(Eviction $eviction)
    {
        $this->oid = spl_object_id($this);
        ($this->eviction = $eviction)->startup();
    }

    /**
     * @param string $key
     * @return bool
     */
    public function has(string $key) : bool
    {
        return isset($this->block[$key]);
    }

    /**
     * @param string $key
     * @return mixed
     */
    public function read(string $key)
    {
        return $this->block[$key] ?? null;
    }

    /**
     * @param string $key
     * @param mixed $data
     * @param int $ttl
     * @return bool
     */
    public function write(string $key, $data, int $ttl = null) : bool
    {
        $this->block[$key] = $data;
        $this->eviction->watch($this->oid, $key, $ttl);
        return true;
    }

    /**
     * @param string $key
     * @return bool
     */
    public function remove(string $key) : bool
    {
        unset($this->block[$key]);
        return true;
    }
}
