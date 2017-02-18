<?php

namespace Lexxpavlov\SettingsBundle\Cache;


interface AdapterCacheInterface
{
    /**
     * @param string $key    Key
     * @param mixed $default Default value
     * @return false|mixed
     */
    function get($key, $default = null);

    /**
     * @param string $key   Key
     * @param mixed  $value Value
     * @param int    $ttl   Time to live, in seconds (0 - disable)
     * @return boolean
     */
    function set($key, $value, $ttl = 0);

    /**
     * @param string $key Key
     * @return boolean
     */
    function delete($key);
}