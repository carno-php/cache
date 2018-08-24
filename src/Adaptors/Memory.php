<?php
/**
 * Memory adaptor
 * User: moyo
 * Date: 15/11/2017
 * Time: 4:56 PM
 */

namespace Carno\Cache\Adaptors;

use Carno\Cache\Blocks;
use Carno\Cache\Chips\Delegate;
use Carno\Cache\Chips\Prefixed;
use Carno\Cache\Chips\Properties;
use Carno\Cache\Chips\Storage;
use Carno\Cache\Eviction;
use Carno\Cache\Refreshing;
use Carno\Cache\Stores\Apcu;
use Carno\Cache\Stores\Local;

abstract class Memory
{
    use Properties, Prefixed, Storage, Delegate;

    /**
     * Memory constructor.
     * @param Refreshing $refresher
     * @param Eviction $eviction
     */
    final public function __construct(Refreshing $refresher, Eviction $eviction)
    {
        $this->refresher = $refresher;
        $this->backend =
            (extension_loaded('apcu') && ini_get('apc.enable_cli'))
                ? new Apcu
                : Blocks::join(new Local($eviction))
        ;
    }

    /**
     */
    final public function __destruct()
    {
        if ($this->backend instanceof Local) {
            Blocks::forget($this->backend);
        }
    }
}
