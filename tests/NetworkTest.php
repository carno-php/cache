<?php
/**
 * Network tests
 * User: moyo
 * Date: 2018/8/24
 * Time: 3:13 PM
 */

namespace Carno\Cache\Tests;

use Carno\Cache\Adaptors\Network;
use Carno\Cache\Refreshing;
use function Carno\Coroutine\async;
use function Carno\Coroutine\msleep;
use Carno\Redis\Redis;
use PHPUnit\Framework\TestCase;
use Throwable;

class NetworkTest extends TestCase
{
    public function testRedis()
    {
        $net = new class(new Refreshing) extends Network {
            private $redis = null;

            /**
             * @return Redis
             */
            public function redis() : Redis
            {
                return $this->redis;
            }

            protected function driver() : object
            {
                return $this->redis = new Redis('127.0.0.1:6379');
            }
        };

        async(function () use ($net) {
            yield $net->redis()->connect();

            yield $net->write($k1 = 'key1', $d1 = 'dat1', 1);
            yield $net->write($k2 = 'key2', $d2 = 'dat2', 3);

            $this->assertEquals($d1, yield $net->read($k1));
            $this->assertEquals($d2, yield $net->read($k2));

            yield msleep(2000);

            $this->assertFalse(yield $net->has($k1));
            $this->assertTrue(yield $net->has($k2));

            yield msleep(2000);

            $this->assertFalse(yield $net->has($k2));

            yield $net->redis()->close();
        })->catch(function (Throwable $e) {
            echo 'FAILURE ', get_class($e), ' :: ', $e->getMessage(), PHP_EOL;
            echo $e->getTraceAsString();
            exit(1);
        });

        swoole_event_wait();
    }
}
