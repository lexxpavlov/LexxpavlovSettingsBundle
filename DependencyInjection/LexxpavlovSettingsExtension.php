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

        $container->setParameter('lexxpavlov_settings.cache_provider', $config['cache_provider']);
        $container->setParameter('lexxpavlov_settings.use_category_comment', $config['use_category_comment']);
        $container->setParameter('lexxpavlov_settings.html_widget', $config['html_widget']);
        $container->setParameter('lexxpavlov_settings.ckeditor.base_path', $config['ckeditor']['base_path']);
        $container->setParameter('lexxpavlov_settings.ckeditor.js_path', $config['ckeditor']['js_path']);

        $bundles = $container->getParameter('kernel.bundles');

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');
        if (isset($bundles['SonataAdminBundle'])) {
            $loader->load('admin.yml');
        }
        if ($config['enable_short_service']) {
            $loader->load('settings_service.yml');
        }
    }
}
