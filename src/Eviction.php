<?php
/**
 * Native memory keys eviction
 * User: moyo
 * Date: 2018/6/25
 * Time: 5:51 PM
 */

namespace Carno\Cache;

use Carno\Timer\Timer;
use SplPriorityQueue;

class Eviction
{
    /**
     * @var SplPriorityQueue
     */
    private $heap = null;

    /**
     * @var string
     */
    private $daemon = null;

    /**
     * @var int
     */
    private $nearest = PHP_INT_MAX;

    /**
     * Eviction constructor.
     */
    public function __construct()
    {
        $this->heap = new class extends SplPriorityQueue {
            /**
             * heap constructor.
             */
            public function __construct()
            {
                $this->setExtractFlags(SplPriorityQueue::EXTR_DATA);
            }

            /**
             * @param mixed $priority1
             * @param mixed $priority2
             * @return int
             */
            public function compare($priority1, $priority2) : int
            {
                return $priority2 <=> $priority1;
            }

            /**
             * @return array
             */
            public function nearest() : array
            {
                return $this->top();
            }

            /**
             * @param int $bid
             * @param string $key
             * @param int $ttl
             */
            public function append(int $bid, string $key, int $ttl) : void
            {
                $this->insert([time() + $ttl, $bid, $key], $ttl);
            }

            /**
             * extract and forget it
             */
            public function forget() : void
            {
                $this->extract();
            }
        };
    }

    /**
     */
    public function startup() : void
    {
        $this->daemon || $this->daemon = Timer::loop(1000, [$this, 'polling']);
    }

    /**
     */
    public function shutdown() : void
    {
        $this->daemon && Timer::clear($this->daemon);
    }

    /**
     */
    public function polling() : void
    {
        if ($this->nearest > 0 && $this->nearest -- > 0) {
            return;
        }

        $now = time();

        while ($this->heap->valid()) {
            [$expired, $bid, $key] = $this->heap->nearest();

            if ($now < $expired) {
                $this->nearest = $expired - $now;
                break;
            }

            $this->heap->forget();

            if (null !== $local = Blocks::get($bid)) {
                $local->remove($key);
            }
        }
    }

    /**
     * @param int $bid
     * @param string $key
     * @param int $ttl
     */
    public function watch(int $bid, string $key, int $ttl) : void
    {
        if ($ttl <= 0) {
            return;
        }

        if ($ttl < $this->nearest) {
            $this->nearest = $ttl;
        }

        $this->heap->append($bid, $key, $ttl);
    }
}
