<?php

namespace Lexxpavlov\SettingsBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;

/**
 * This is the class that loads and manages your bundle configuration
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class LexxpavlovSettingsExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        if ($config['html_widget'] == 'ckeditor' && $container->hasParameter('ivory_ck_editor.form.type.base_path')) {
            $ckeditorBasePath = $container->getParameter('ivory_ck_editor.form.type.base_path');
            $ckeditorJsPath = $container->getParameter('ivory_ck_editor.form.type.js_path');
        } else {
            $ckeditorBasePath = $ckeditorJsPath = $config['html_widget'] = null;
        }

        $container->setParameter('lexxpavlov_settings.cache_provider', $config['cache_provider']);
        $container->setParameter('lexxpavlov_settings.html_widget', $config['html_widget']);
        $container->setParameter('lexxpavlov_settings.ivory_ck_editor.base_path', $ckeditorBasePath);
        $container->setParameter('lexxpavlov_settings.ivory_ck_editor.js_path', $ckeditorJsPath);

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');
        $loader->load('admin.yml');
        if ($config['enable_short_service']) {
            $loader->load('settings_service.yml');
        }
    }
}
