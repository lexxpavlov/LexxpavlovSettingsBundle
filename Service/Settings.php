<?php

namespace Lexxpavlov\SettingsBundle\Service;

use Doctrine\Common\Cache\CacheProvider;
use Doctrine\ORM\EntityManager;
use Lexxpavlov\SettingsBundle\Entity\SettingsRepository;

class Settings
{
    /** @var  EntityManager */
    private $em;

    /** @var  CacheProvider */
    private $cache;

    /** @var  SettingsRepository */
    private $repository;

    private $settings = array();
    private $groups = array();

    public function __construct(EntityManager $em, $container, $cacheServiceName)
    {
        $this->em = $em;
        if ($cacheServiceName && $container->has($cacheServiceName)) {
            $this->cache = $container->get($cacheServiceName);
        }
        $this->repository = $em->getRepository('Lexxpavlov\SettingsBundle\Entity\Settings');
    }

    private function getCacheKey($name)
    {
        return 'lexxpavlov_settings_' . $name;
    }

    private function getCacheGroupKey($name)
    {
        return 'lexxpavlov_settings_category_' . $name;
    }

    private function fetch($name)
    {
        return $this->repository->findOneBy(array('name' => $name))->getValue();
    }

    private function fetchGroup($name)
    {
        $list = $this->repository->getGroup($name);

        $settings = array();
        foreach ($list as $setting) {
            $settings[$setting->getName()] = $setting->getValue();
        }
        return $settings;
    }

    private function load($name)
    {
        if ($this->cache) {
            $value = $this->cache->fetch($this->getCacheKey($name));
            if (!$value) {
                $value = $this->fetch($name);
                $this->cache->save($this->getCacheKey($name), $value);
            }
            return $value;
        } else {
            return $this->fetch($name);
        }
    }

    private function loadGroup($name)
    {
        if ($this->cache) {
            $values = $this->cache->fetch($this->getCacheGroupKey($name));
            if (!$values) {
                $values = $this->fetchGroup($name);
                $this->cache->save($this->getCacheGroupKey($name), $values);
            }
            return $values;
        } else {
            return $this->fetchGroup($name);
        }
    }

    /**
     * Get one setting
     *
     * @param string $name Setting name or group name (if $subname is set)
     * @param string|null $subname Setting name (use with $name as group name)
     * @return mixed
     */
    public function get($name, $subname = null)
    {
        if ($subname) {
            $group = $this->group($name);
            return $group[$subname];
        } else {
            if (!isset($this->settings[$name])) {
                $this->settings[$name] = $this->load($name);
            }
            return $this->settings[$name];
        }
    }

    /**
     * Get group of settings
     *
     * @param string $name Group name
     * @return array
     */
    public function group($name)
    {
        if (!isset($this->groups[$name])) {
            $this->groups[$name] = $this->loadGroup($name);
        }
        return $this->groups[$name];
    }

    /**
     * Clear cache for setting name
     *
     * @param string $name Name of setting
     * @return bool
     */
    public function clearCache($name)
    {
        if ($this->cache) {
            return $this->cache->delete($this->getCacheKey($name));
        }
        return false;
    }

    /**
     * Clear cache for settings category
     *
     * @param string $name Name of category
     * @return bool
     */
    public function clearGroupCache($name)
    {
        if ($this->cache) {
            return $this->cache->delete($this->getCacheGroupKey($name));
        }
        return false;
    }
}