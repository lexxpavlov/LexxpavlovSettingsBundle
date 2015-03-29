<?php

namespace Lexxpavlov\SettingsBundle\Entity;

use Doctrine\ORM\EntityRepository;
use Lexxpavlov\SettingsBundle\Entity\Settings;

class SettingsRepository extends EntityRepository
{
    /**
     * @param string $name
     * @return Settings[]
     */
    public function getGroup($name)
    {
        return $this->_em
            ->createQuery("SELECT s FROM LexxpavlovSettingsBundle:Settings s INNER JOIN LexxpavlovSettingsBundle:Category g WHERE g.id = s.category AND g.name = ?1")
            ->setParameter(1, $name)
            ->getResult();
    }

    /**
     * @param Settings $setting
     */
    public function save(Settings $setting)
    {
        $this->_em->persist($setting);
        $this->_em->flush();
    }
}
