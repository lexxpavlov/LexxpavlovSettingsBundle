<?php

namespace Lexxpavlov\SettingsBundle\Entity;

use Doctrine\ORM\EntityRepository;

class SettingsRepository extends EntityRepository
{
    public function getGroup($name)
    {
        return $this->_em
            ->createQuery("SELECT s FROM LexxpavlovSettingsBundle:Settings s INNER JOIN LexxpavlovSettingsBundle:Category g WHERE g.id = s.category AND g.name = ?1")
            ->setParameter(1, $name)
            ->getResult();
    }
}
