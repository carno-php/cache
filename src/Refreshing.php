<?php
/**
 * Refreshes manager
 * User: moyo
 * Date: 24/10/2017
 * Time: 12:30 PM
 */

namespace Carno\Cache;

use function Carno\Coroutine\co;
use Carno\Promise\Promised;
use Carno\Timer\Timer;
use Closure;

class Refreshing
{
    /**
     * @var array
     */
    private $timers = [];

    /**
     * @var array
     */
    private $daemons = [];

    /**
     * @var array
     */
    private $values = [];

    /**
     * @param string $key
     * @param Closure $daemon
     * @param int $refreshed
     * @return Promised
     */
    public function register(string $key, Closure $daemon, int $refreshed) : Promised
    {
        $this->daemons[$key] = $daemon;

        $refresher = co(function () use ($key) {
            $this->values[$key] = yield $this->daemons[$key]();
        });

        $this->timers[$key] = Timer::loop($refreshed * 1000, $refresher);

        return $refresher();
    }

    /**
     */
    public function shutdown() : void
    {
        if (empty($this->timers)) {
            return;
        }

        logger('cache')->info('Background refresher is closing', ['timers' => count($this->timers)]);

        foreach ($this->timers as $timer) {
            Timer::clear($timer);
        }
    }

    /**
     * @param string $key
     * @return bool
     */
    public function has(string $key) : bool
    {
        return isset($this->values[$key]) || isset($this->timers[$key]);
    }

    /**
     * @param string $key
     * @return mixed
     */
    public function value(string $key)
    {
        return $this->values[$key] ?? yield $this->daemons[$key]();
    }
}
