<?php
/**
 * Memory tests
 * User: moyo
 * Date: 2018/8/24
 * Time: 3:13 PM
 */

namespace Carno\Cache\Tests;

use Carno\Cache\Blocks;
use Carno\Cache\Eviction;
use Carno\Cache\Stores\Local;
use function Carno\Coroutine\go;
use function Carno\Coroutine\msleep;
use PHPUnit\Framework\TestCase;

class MemoryTest extends TestCase
{
    public function testLocal()
    {
        go(function () {
            $evi = new Eviction;

            $local = new Local($evi);

            Blocks::join($local);

            $local->write($k1 = 'key1', $d1 = 'dat1', 1);
            $local->write($k2 = 'key2', $d2 = 'dat2', 3);

            $this->assertEquals($d1, $local->read($k1));
            $this->assertEquals($d2, $local->read($k2));

            yield msleep(2000);

            $this->assertFalse($local->has($k1));
            $this->assertTrue($local->has($k2));

            yield msleep(2000);

            $this->assertFalse($local->has($k2));

            $evi->shutdown();
        });

        swoole_event_wait();
    }
}
