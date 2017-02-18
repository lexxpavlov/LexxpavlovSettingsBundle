<?php

namespace Lexxpavlov\SettingsBundle\Service;

use Doctrine\ORM\EntityManager;
use Lexxpavlov\SettingsBundle\Cache\AdapterCacheInterface;
use Lexxpavlov\SettingsBundle\DBAL\SettingsType;
use Lexxpavlov\SettingsBundle\Entity\Category;
use Lexxpavlov\SettingsBundle\Entity\Settings as SettingsEntity;
use Lexxpavlov\SettingsBundle\Entity\SettingsRepository;

class Settings
{
    /** @var EntityManager */
    private $em;

    /** @var AdapterCacheInterface */
    private $cache;

    /** @var SettingsRepository */
    private $repository;

    private $settings = array();
    private $groups = array();

    /**
     * @param EntityManager $em
     * @param AdapterCacheInterface|null $cache
     */
    public function __construct(EntityManager $em, $cache)
    {
        if ($cache instanceof AdapterCacheInterface) {
            $this->cache = $cache;
        }
        $this->em = $em;
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
        /** @var SettingsEntity $setting */
        foreach ($list as $setting) {
            $settings[$setting->getName()] = $setting->getValue();
        }
        return $settings;
    }

    private function load($name)
    {
        if ($this->cache) {
            $cacheKey = $this->getCacheKey($name);
            $value = $this->cache->get($cacheKey);
            if (is_null($value)) {
                $value = $this->fetch($name);
                $this->cache->set($cacheKey, $value);
            }
            return $value;
        } else {
            return $this->fetch($name);
        }
    }

    private function loadGroup($name)
    {
        if ($this->cache) {
            $cacheKey = $this->getCacheGroupKey($name);
            $values = $this->cache->get($cacheKey);
            if (is_null($values)) {
                $values = $this->fetchGroup($name);
                $this->cache->set($cacheKey, $values);
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
     * Save setting entity
     *
     * @param SettingsEntity $setting
     */
    public function save(SettingsEntity $setting)
    {
        if ($setting) {
            $this->repository->save($setting);
        }
    }

    /**
     * Save category entity
     *
     * @param Category $category
     */
    public function saveGroup(Category $category)
    {
        if ($category) {
            $this->saveCategory($category);
        }
    }

    /**
     * Update value of setting.
     *
     * Usage:
     *
     *     update('name', $value)
     *
     *     update('category', 'name', $value)
     *
     * @param string $name
     * @param string|mixed $subname
     * @param null|mixed $value
     */
    public function update($name, $subname, $value = null)
    {
        if (!is_null($value)) {
            $category = $this->getCategory($name);
            $name = $subname;
        } else {
            $category = null;
            $value = $subname;
        }

        /** @var SettingsEntity $setting */
        $setting = $this->repository->findOneBy(array('category' => $category, 'name' => $name));
        $setting->setValue($value);
        $this->repository->save($setting);

        if ($category) {
            $this->groups[$category->getName()][$name] = $value;
            $this->clearGroupCache($category->getName());
        } else {
            $this->settings[$name] = $value;
            $this->clearCache($name);
        }
    }

    /**
     * Create a new setting
     *
     * @param string $category Category
     * @param string $name Name of setting
     * @param string $type Type
     * @param mixed $value Value
     * @param string $comment Comment
     * @return SettingsEntity
     */
    public function create($category, $name, $type, $value, $comment = null)
    {
        if (!in_array($type, SettingsType::getValues())) {
            $types = implode(', ', SettingsType::getValues());
            throw new \InvalidArgumentException("Invalid type \"$type\". Type must be one of $types");
        }

        /** @var Category $category */
        $category = $this->getCategory($category);

        /** @var SettingsEntity $setting */
        $setting = new SettingsEntity();
        $setting
            ->setCategory($category)
            ->setType($type)
            ->setName($name)
            ->setValue($value)
            ->setComment($comment)
        ;
        $this->repository->save($setting);

        if ($category) {
            $this->groups[$category->getName()][$name] = $value;
            $this->clearGroupCache($category->getName());
        } else {
            $this->settings[$name] = $value;
            $this->clearCache($name);
        }

        return $setting;
    }

    /**
     * Create a new settings category
     *
     * @param string $name Name of new category
     * @param string|null $comment Optional comment
     */
    public function createGroup($name, $comment = null)
    {
        $category = new Category();
        $category
            ->setName($name)
            ->setComment($comment)
        ;
        $this->saveCategory($category);
        $this->clearGroupCache($category->getName());
    }

    /**
     * @param string $name
     * @return Category|null
     */
    private function getCategory($name)
    {
        if (!$name) return null;
        $category = $this->em->getRepository('LexxpavlovSettingsBundle:Category')->findOneBy(array('name' => $name));
        if (!$category) {
            $category = new Category();
            $category->setName($name);
            $this->saveCategory($category);
        }
        return $category;
    }

    /**
     * @param Category $category
     * @return Category
     */
    private function saveCategory(Category $category)
    {
        if ($category) {
            $this->em->persist($category);
            $this->em->flush();
        }
        return $category;
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