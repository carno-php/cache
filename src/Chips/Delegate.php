<?php
/**
 * Refresh delegate
 * User: moyo
 * Date: 24/10/2017
 * Time: 12:13 PM
 */

namespace Carno\Cache\Chips;

use Carno\Cache\Refreshing;
use Closure;

trait Delegate
{
    /**
     * @var Refreshing
     */
    private $refresher = null;

    /**
     * @param string $key
     * @param Closure $getter
     * @param int $refreshed
     * @return mixed
     */
    final public function delegate(string $key, Closure $getter, int $refreshed = 0)
    {
        if ($refreshed) {
            $key2 = $this->key($key);
            if ($this->refresher->has($key2)) {
                return yield $this->refresher->value($key2);
            }
            return yield $this->refresher->register($key2, $getter, $refreshed);
        } else {
            if (yield $this->has($key)) {
                return yield $this->read($key);
            } else {
                yield $this->write($key, $dat = yield $getter());
                return $dat;
            }
        }
    }
}
