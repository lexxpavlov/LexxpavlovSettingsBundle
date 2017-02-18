<?php

namespace Lexxpavlov\SettingsBundle\Cache;

use Doctrine\Common\Cache\CacheProvider;
use Symfony\Component\Cache\Adapter\AdapterInterface;
use Symfony\Component\Cache\Simple\AbstractCache;
use Symfony\Component\DependencyInjection\ContainerInterface;

class CacheFactory
{
    /**
     * @param string $serviceName
     * @param ContainerInterface $container
     * @return AdapterCacheInterface|null
     * @static
     */
    public static function createByName($serviceName, ContainerInterface $container)
    {
        $service = $container->get($serviceName, ContainerInterface::NULL_ON_INVALID_REFERENCE);
        return is_null($service) ? null : self::create($service);
    }

    /**
     * @param object $service
     * @return AdapterCacheInterface|null
     * @static
     */
    public static function create($service)
    {
        if ($service instanceof AdapterInterface) {
            return new SymfonyCache($service);
        } elseif ($service instanceof CacheProvider) {
            return new DoctrineCache($service);
        } elseif ($service instanceof AbstractCache) {
            return new SimpleCache($service);
        }
        return null;
    }
}