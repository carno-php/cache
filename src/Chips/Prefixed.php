<?php
/**
 * Prefixed cache-key
 * User: moyo
 * Date: 26/10/2017
 * Time: 10:56 AM
 */

namespace Carno\Cache\Chips;

trait Prefixed
{
    /**
     * @param string $input
     * @return string
     */
    protected function key(string $input) : string
    {
        return $this->prefix ? sprintf('%s:%s', $this->prefix, $input) : $input;
    }
}
