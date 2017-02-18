<?php

namespace Lexxpavlov\SettingsBundle\Cache;

use Doctrine\Common\Cache\CacheProvider;

class DoctrineCache implements AdapterCacheInterface
{
    /** @var CacheProvider */
    private $provider;

    /**
     * @param CacheProvider $provider
     */
    public function __construct(CacheProvider $provider)
    {
        $this->provider = $provider;
    }

    /**
     * {@inheritdoc}
     */
    function get($key, $default = null)
    {
        $value = $this->provider->fetch($key);
        return $value !== false ? $value : $default;
    }

    /**
     * {@inheritdoc}
     */
    function set($key, $value, $ttl = 0)
    {
        return $item = $this->provider->save($key, $value, $ttl);
    }

    /**
     * {@inheritdoc}
     */
    function delete($key)
    {
        return $this->provider->delete($key);
    }
}