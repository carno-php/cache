<?php
/**
 * Local memory blocks
 * User: moyo
 * Date: 2018/7/11
 * Time: 12:01 PM
 */

namespace Carno\Cache;

use Carno\Cache\Stores\Local;

class Blocks
{
    /**
     * @var Local[]
     */
    private static $stores = [];

    /**
     * @param int $id
     * @return Local
     */
    public static function get(int $id) : ?Local
    {
        return self::$stores[$id] ?? null;
    }

    /**
     * @param Local $store
     * @return Local
     */
    public static function join(Local $store) : Local
    {
        return self::$stores[spl_object_id($store)] = $store;
    }

    /**
     * @param Local $store
     */
    public static function forget(Local $store) : void
    {
        unset(self::$stores[spl_object_id($store)]);
    }
}
