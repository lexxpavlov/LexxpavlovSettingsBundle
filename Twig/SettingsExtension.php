<?php

namespace Lexxpavlov\SettingsBundle\Twig;

use Lexxpavlov\SettingsBundle\Service\Settings;

class SettingsExtension extends \Twig_Extension
{
    /** @var Settings */
    private $settings;

    /**
     * @param Settings $settings
     */
    public function __construct(Settings $settings)
    {
        $this->settings = $settings;
    }

    public function getName()
    {
        return 'lexxpavlov_settings_extension';
    }

    public function getFunctions()
    {
        return array(
            new \Twig_SimpleFunction('settings', array($this, 'getSettings')),
            new \Twig_SimpleFunction('settings_group', array($this, 'getSettingsGroup')),
        );
    }

    public function getSettings($name, $subname = null)
    {
        return $this->settings->get($name, $subname);
    }

    public function getSettingsGroup($name)
    {
        return $this->settings->group($name);
    }
}
