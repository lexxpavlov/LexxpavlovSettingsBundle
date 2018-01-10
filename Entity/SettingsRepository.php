<?php

namespace Lexxpavlov\SettingsBundle\Entity;

use Doctrine\ORM\EntityRepository;

class SettingsRepository extends EntityRepository
{
    /**
     * @param string $name
     * @return Settings[]
     */
    public function getGroup($name)
    {
        return $this->createQueryBuilder('s')
                    ->innerJoin('s.category', 'c')
                    ->where('c.name = :name')
                    ->setParameter('name', $name)
                    ->getQuery()
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
