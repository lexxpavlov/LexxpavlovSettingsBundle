<?php

namespace Lexxpavlov\SettingsBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Doctrine\DBAL\Types\Type as DoctrineType;
use Lexxpavlov\SettingsBundle\DependencyInjection\SecondCompilerPass;

class LexxpavlovSettingsBundle extends Bundle
{
    public function boot()
    {
        if (!DoctrineType::hasType('LexxpavlovSettingsEnumType')) {
            DoctrineType::addType('LexxpavlovSettingsEnumType', 'Lexxpavlov\\SettingsBundle\\DBAL\\SettingsType');
        }
    }
}
