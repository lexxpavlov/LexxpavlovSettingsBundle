<?php

namespace Lexxpavlov\SettingsBundle\Cache;

use Symfony\Component\Cache\Psr16Cache;

class SimpleCache implements AdapterCacheInterface
{
    /** @var Psr16Cache */
    private $adapter;

    /**
     * @param Psr16Cache $adapter
     */
    public function __construct(Psr16Cache $adapter)
    {
        $this->adapter = $adapter;
    }

    /**
     * {@inheritdoc}
     */
    function get($key, $default = null)
    {
        return $this->adapter->get($key, $default);
    }

    /**
     * {@inheritdoc}
     */
    function set($key, $value, $ttl = 0)
    {
        return $item = $this->adapter->set($key, $value, $ttl);
    }

    /**
     * {@inheritdoc}
     */
    function delete($key)
    {
        return $this->adapter->delete($key);
    }
}
